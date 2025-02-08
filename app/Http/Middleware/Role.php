<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    public function handle(Request $request, Closure $next, $role)
    {
        // For client routes, check client guard first
        if (str_starts_with($request->path(), 'client/')) {
            if (Auth::guard('client')->check()) {
                $user = Auth::guard('client')->user();
                if ($user->role === $role) {
                    return $next($request);
                }
                return response('You are not authorized to access this page as a client', 403);
            }
        }
        // For all other routes, check web guard first
        else if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === $role) {
                return $next($request);
            }
            return response('You are not authorized to access this page as a ' . ucfirst($user->role), 403);
        }

        // If no authentication at all
        return redirect()->route('login')->with('error', 'Please login to access this page');
    }
}