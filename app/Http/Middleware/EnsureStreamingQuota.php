<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

final class EnsureStreamingQuota
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        $key = 'streaming_quota:'.$user->getKey();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Too many requests. Please wait before sending more messages.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
