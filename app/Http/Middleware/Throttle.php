<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class Throttle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);
        
        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $this->getTimeUntilNextAttempt($key)
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->incrementAttempts($key, $decayMinutes);

        $response = $next($request);

        return $response;
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->ip() . '|' . $request->route()?->getName()
        );
    }

    /**
     * Determine if the user has too many attempts.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return bool
     */
    protected function tooManyAttempts($key, $maxAttempts)
    {
        $attempts = Cache::get($key, 0);
        return $attempts >= $maxAttempts;
    }

    /**
     * Increment the counter for a given key.
     *
     * @param  string  $key
     * @param  int  $decayMinutes
     * @return void
     */
    protected function incrementAttempts($key, $decayMinutes)
    {
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
    }

    /**
     * Get the number of seconds until the next attempt is available.
     *
     * @param  string  $key
     * @return int
     */
    protected function getTimeUntilNextAttempt($key)
    {
        return Cache::ttl($key);
    }
}
