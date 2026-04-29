<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class SimpleApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            $parts = @explode('|', base64_decode($token));
            if (count($parts) >= 2) {
                $userId = $parts[0];
                $user = User::find($userId);
                if ($user) {
                    auth()->setUser($user);
                }
            }
        }

        return $next($request);
    }
}
