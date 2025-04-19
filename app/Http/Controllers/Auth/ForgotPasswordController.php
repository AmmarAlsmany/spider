<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\client;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        
        // Check if the email exists in the users table
        $user = User::where('email', $email)->first();
        $userType = 'user';
        
        // If not found in users, check in clients table
        if (!$user) {
            $user = client::where('email', $email)->first();
            $userType = 'client';
            
            if (!$user) {
                return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
            }
        }
        
        // Create a password reset token
        $token = Str::random(64);
        
        // Store the token in the password_resets table
        DB::table('password_resets')->where('email', $email)->delete();
        
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'user_type' => $userType,
            'created_at' => Carbon::now()
        ]);
        
        // Create reset link
        $resetLink = url(route('password.reset', [
            'token' => $token,
            'email' => $email,
        ], false));
        
        // Send email with reset link
        Mail::send('auth.emails.password', ['resetLink' => $resetLink, 'user' => $user], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset Your Password - Spider Web');
            $message->from(config('mail.from.address', 'noreply@spiderweb.com'), config('mail.from.name', 'Spider Web'));
        });
        
        return back()->with('status', 'We have emailed your password reset link!');
    }
}
