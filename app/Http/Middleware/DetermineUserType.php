<?php

namespace App\Http\Middleware\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetermineUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('web')->check()) {
            // User is logged in as regular user
            return redirect()->route('usr.dashboard');
        }

        if (auth()->guard('client')->check()) {
            // User is logged in as client
            return redirect()->route('cli.dashboard');
        }

        return $next($request);
    }
}
