<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class IpWhitelistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // Admins and clients bypass IP check
        if ($user && ($user->type === 'admin' || $user->type === 'client')) {
            return $next($request);
        }

        if($user && ($user->work_location == 'remote' || $user->work_location == 'temporary remote')) {
            return $next($request);
        }

        $ipCheck = Setting::where('name', 'ip_check')->value('value');
        if ($ipCheck === 'on') {
            $whitelist = [];
            $ipsSetting = Setting::where('name', 'ips')->value('value');
            if ($ipsSetting) {
                $whitelist = array_filter(explode(',', $ipsSetting));
            }
            $ip = $request->ip();
            if (!in_array($ip, $whitelist)) {
                abort(403, 'Outside Access not allowed.');
            }
        }

        return $next($request);
    }
}
