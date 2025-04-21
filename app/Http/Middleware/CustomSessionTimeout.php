<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;

class CustomSessionTimeout
{
    protected $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check both web and client guards
        $isWebAuthenticated = Auth::guard('web')->check();
        $isClientAuthenticated = Auth::guard('client')->check();
        
        // Skip for unauthenticated users
        if (!$isWebAuthenticated && !$isClientAuthenticated) {
            return $next($request);
        }
        
        // Get the user from the appropriate guard
        if ($isClientAuthenticated) {
            $user = Auth::guard('client')->user();
            $guard = 'client';
        } else {
            $user = Auth::guard('web')->user();
            $guard = 'web';
        }
        
        // Get the custom lifetime from session or use default
        $lifetime = $this->session->get('_lifetime', config('session.lifetime', 240));
        
        // Get the last activity timestamp from session
        $lastActivity = $this->session->get('last_activity');
        
        // If last_activity doesn't exist, set it now
        if (!$lastActivity) {
            $this->session->put('last_activity', time());
            return $next($request);
        }
        
        // Check if we need to log the user out due to inactivity
        $elapsedTime = time() - $lastActivity;
        if ($elapsedTime > ($lifetime * 60)) {
            // Session has expired based on custom role-based timeout
            
            // Logout from the appropriate guard
            if ($guard === 'client') {
                Auth::guard('client')->logout();
            } else {
                Auth::guard('web')->logout();
            }
            
            $this->session->flush();
            $this->session->regenerate();
            
            return redirect()->route('login')
                ->with('error', 'Your session has expired due to inactivity. Please log in again.');
        }
        
        // Only update last_activity time for certain routes/actions
        // This prevents the timer from being reset on every request
        if ($this->shouldResetTimer($request)) {
            $this->session->put('last_activity', time());
        }
        
        return $next($request);
    }
    
    /**
     * Determine if the request should reset the inactivity timer
     * Only significant user actions should reset the timer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldResetTimer($request)
    {
        // POST, PUT, PATCH, DELETE requests indicate user activity (form submissions)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }
        
        // Specific routes that indicate active user engagement
        // Add your dashboard routes or other routes indicating activity
        $activeRoutes = [
            'client.dashboard',
            'sales.dashboard',
            'technical.dashboard',
            'admin.dashboard',
            'finance.dashboard',
            'sales_manager.dashboard',
            'team-leader.dashboard'
        ];
        
        // Check if current route name is in the active routes list
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        if ($currentRoute && in_array($currentRoute, $activeRoutes)) {
            return true;
        }
        
        // AJAX requests for notifications or real-time updates shouldn't reset the timer
        if ($request->ajax() && !$request->input('reset_timer', false)) {
            return false;
        }
        
        // Default - don't reset timer for most GETs to prevent timeout extension
        // This is key for proper timeout enforcement
        return false;
    }
}
