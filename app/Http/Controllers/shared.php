<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $notification = array(
            'message' => 'Profile updated successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function updateUserpassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        // Get the authenticated user and guard
        $isClient = Auth::guard('client')->check();
        $user = $isClient ? 
            client::find(Auth::guard('client')->id()) : 
            User::find(Auth::id());

        if (!$user) {
            return redirect()->back()->with([
                'message' => 'User not found',
                'alert-type' => 'error'
            ]);
        }

        // Define notifications
        $notifications = [
            'success' => [
                'message' => 'Password is changed successfully',
                'alert-type' => 'success'
            ],
            'password_mismatch' => [
                'message' => 'Current password not match!',
                'alert-type' => 'error'
            ],
            'same_password' => [
                'message' => 'New password cannot be the old password!',
                'alert-type' => 'error'
            ]
        ];

        // Check if current password matches using the correct guard
        if ($isClient) {
            if (!Auth::guard('client')->attempt(['email' => $user->email, 'password' => $request->old_password])) {
                return redirect()->back()->with($notifications['password_mismatch']);
            }
        } else {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with($notifications['password_mismatch']);
            }
        }

        // Check if new password is same as old password
        if (Hash::check($request->new_password, $user->password)) {
            return redirect()->back()->with($notifications['same_password']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with($notifications['success']);
    }
}
