<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Rate limiting - 5 attempts per minute
        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if user exists and is active
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && $user->status !== 'active') {
            RateLimiter::hit($throttleKey);
            return back()->withErrors([
                'email' => 'Your account is not active. Please contact the administrator.',
            ]);
        }

        // If user is a client, check client status
        if ($user && $user->role === 'client') {
            $client = \App\Models\Client::where('user_id', $user->id)->first();
            if (!$client || $client->status !== 'active') {
                RateLimiter::hit($throttleKey);
                return back()->withErrors([
                    'email' => 'Your client account is not active. Please contact your sales representative.',
                ]);
            }
        }

        // Try to authenticate as user
        if (Auth::guard('web')->attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Update last login timestamp
            $user = Auth::user();
            $user->last_login_at = Carbon::now();
            $user->last_login_ip = $request->ip();
            $user->save();

            $url = match ($user->role) {
                'admin' => route('admin.dashboard', absolute: false),
                'finance' => route('finance.dashboard', absolute: false),
                'sales_manager' => route('sales_manager.dashboard', absolute: false),
                'technical' => route('technical.dashboard', absolute: false),
                'team_leader' => route('team-leader.dashboard', absolute: false),
                'sales' => route('sales.dashboard', absolute: false),
                default => route('/', absolute: false),
            };
            return redirect()->intended($url);
        }
        // If user authentication fails, try client authentication
        elseif (Auth::guard('client')->attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $client = Auth::guard('client')->user();
            $client->last_login_at = Carbon::now();
            $client->last_login_ip = $request->ip();
            $client->save();

            return redirect()->intended('client/dashboard');
        }

        RateLimiter::hit($throttleKey);

        // If both fail, return with error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {

        Auth::guard('web')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'You have been successfully logged out.');
    }
}
