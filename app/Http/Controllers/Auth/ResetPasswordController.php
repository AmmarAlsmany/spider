<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\client;

class ResetPasswordController extends Controller
{
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $email = $request->email;
        $password = $request->password;
        $token = $request->token;

        // Check if token is valid
        $tokenData = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$tokenData) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        // Check if token is expired (60 minutes)
        $createdAt = Carbon::parse($tokenData->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 60) {
            DB::table('password_resets')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired.']);
        }

        // Get the user type from the token data
        $userType = $tokenData->user_type ?? 'user';

        if ($userType === 'user') {
            // Reset password for user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
            }
            
            $user->password = Hash::make($password);
            $user->remember_token = Str::random(60);
            $user->save();
        } else {
            // Reset password for client
            $client = client::where('email', $email)->first();
            
            if (!$client) {
                return back()->withErrors(['email' => 'We can\'t find a client with that email address.']);
            }
            
            $client->password = Hash::make($password);
            $client->save();
        }

        // Delete the token after password reset
        DB::table('password_resets')->where('email', $email)->delete();
        
        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
