<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InquiryAccessMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $allowed = ['admin', 'digital marketing', 'sales','intern'];
        if (!$user || !in_array($user->department, $allowed)) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}
