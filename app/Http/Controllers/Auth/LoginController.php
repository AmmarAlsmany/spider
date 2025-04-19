<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use App\Models\User;
use App\Models\client;

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

        try {
            // First, try to find the user regardless of type
            $user = User::where('email', $request->email)->first();
            $client = client::where('email', $request->email)->first();

            // Handle user authentication
            if ($user) {
                if ($user->status !== 'active') {
                    throw new \Exception('Your account is not active. Please contact the administrator.');
                }

                if ($user->role === 'client') {
                    $clientProfile = client::where('user_id', $user->id)->first();
                    if (!$clientProfile || $clientProfile->status !== 'active') {
                        throw new \Exception('Your client account is not active. Please contact your sales representative.');
                    }
                }

                if (Auth::guard('web')->attempt($credentials, $request->has('remember'))) {
                    return $this->handleSuccessfulLogin($request, $user, 'web');
                }
            }
            // Handle client authentication
            elseif ($client) {
                if ($client->status !== 'active') {
                    throw new \Exception('Your client account is not active. Please contact your sales representative.');
                }

                if (Auth::guard('client')->attempt($credentials, $request->has('remember'))) {
                    return $this->handleSuccessfulLogin($request, $client, 'client');
                }
            }

            throw new \Exception('The provided credentials do not match our records.');
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey);
            return back()->withErrors(['email' => $e->getMessage()])->withInput($request->only('email'));
        }
    }

    private function handleSuccessfulLogin(Request $request, $user, string $guard)
    {
        RateLimiter::clear($throttleKey = strtolower($user->email) . '|' . $request->ip());
        $request->session()->regenerate();

        // Update last login information
        $user->last_login_at = Carbon::now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // Set a session flag to indicate a new login for notification popup
        $request->session()->put('new_login', true);

        // Get the appropriate dashboard URL based on user type
        $dashboardUrl = $this->getDashboardUrl($user, $guard);

        return redirect()->intended($dashboardUrl);
    }

    private function getDashboardUrl($user, string $guard): string
    {
        if ($guard === 'client') {
            return route('client.dashboard', absolute: false);
        }

        return match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'finance' => route('finance.dashboard', absolute: false),
            'sales_manager' => route('sales_manager.dashboard', absolute: false),
            'technical' => route('technical.dashboard', absolute: false),
            'team_leader' => route('team-leader.dashboard', absolute: false),
            'sales' => route('sales.dashboard', absolute: false),
            default => route('home', absolute: false),
        };
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('client')->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Clear and invalidate cookies
        $cookie = cookie()->forget('laravel_session');
        
        $response = redirect()->route('login')
            ->withCookie($cookie);

        // Add cache control headers to prevent back/forward navigation
        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Cache-Control', 'post-check=0, pre-check=0', false)
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }
}
