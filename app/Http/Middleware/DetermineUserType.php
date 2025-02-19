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
            $user = auth()->user()->role;
            if($user === 'admin'){
                return redirect()->route('admin.dashboard');
            }elseif($user === 'team_leader'){
                return redirect()->route('team-leader.dashboard');
            }elseif($user === 'sales_manager'){
                return redirect()->route('sales_manager.dashboard');
            }elseif($user === 'technical'){
                return redirect()->route('technical.dashboard');
            }elseif($user === 'sales'){
                return redirect()->route('sales.dashboard');
            }elseif($user === 'finance'){
                return redirect()->route('finance.dashboard');
            }else{
                return redirect()->route('login');
            }
        }

        if (auth()->guard('client')->check()) {
            // User is logged in as client
            return redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}
