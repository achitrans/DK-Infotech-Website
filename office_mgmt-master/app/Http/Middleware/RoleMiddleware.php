<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyRole($roles)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized request.');
        }

        return $next($request);
    }
}
