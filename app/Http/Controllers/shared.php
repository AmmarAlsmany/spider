<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class shared extends Controller
{
    public function changeUserProfile()
    {
        $id = Auth::id();
        if (Auth::guard('client')->check()) {
            $profile_data = client::find($id);
        } else {
            $profile_data = User::find(Auth::user()->id);
        }
        return view('shared.change_profile', compact('profile_data'));
    }

    public function changeUserpassword()
    {
        $id = Auth::user()->id;
        if (Auth::guard('client')->check()) {
            $profileData = client::find($id);
        } else {
            $profileData = User::find($id);
        }
        return view('shared.change_password', compact('profileData'));
    }

    public function updateUserProfile(Request $request)
    {
        $id = Auth::id();
        if (Auth::guard('client')->check()) {
            $profile_data = client::find($id);
        } else {
            $profile_data = User::find($id);
        }
        $profile_data->name = $request->name;
        $profile_data->email = $request->email;
        $profile_data->address = $request->address;
        $profile_data->phone = $request->phone;
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            @unlink(public_path('upload/profile_images/' . $profile_data->avatar));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/profile_images'), $filename);
            $profile_data['avatar'] = $filename;
        }
        $profile_data->save();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updateUserpassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:old_password',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'new_password_confirmation' => ['required', 'string'],
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.',
            'new_password.different' => 'New password must be different from the current password.',
        ]);

        try {
            // Get the authenticated user and guard
            $isClient = Auth::guard('client')->check();
            $user = $isClient ? 
                client::find(Auth::guard('client')->id()) : 
                User::find(Auth::id());

            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'User not found');
            }

            // Define notifications
            $notifications = [
                'success' => [
                    'message' => 'Password has been changed successfully. Please login with your new password.',
                    'alert-type' => 'success'
                ],
                'password_mismatch' => [
                    'message' => 'Current password is incorrect',
                    'alert-type' => 'error'
                ],
                'same_password' => [
                    'message' => 'New password must be different from your current password',
                    'alert-type' => 'error'
                ]
            ];

            // Check if current password matches using the correct guard
            if ($isClient) {
                if (!Auth::guard('client')->attempt(['email' => $user->email, 'password' => $request->old_password])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $notifications['password_mismatch']['message']);
                }
            } else {
                if (!Hash::check($request->old_password, $user->password)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $notifications['password_mismatch']['message']);
                }
            }

            // Check if new password is same as old password
            if (Hash::check($request->new_password, $user->password)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $notifications['same_password']['message']);
            }

            // Update password
            $user->password = Hash::make($request->new_password);

            $user->save();

            // Log the password change event
            Log::info('Password changed for user', ['user_id' => $user->id, 'ip' => $request->ip()]);

            // Logout the user to force re-authentication with new password
            Auth::guard($isClient ? 'client' : 'web')->logout();

            return redirect()->route('login')
                ->with('success', $notifications['success']['message']);

        } catch (\Exception $e) {
            Log::error('Password change failed', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while changing your password. Please try again.');
        }
    }
}
