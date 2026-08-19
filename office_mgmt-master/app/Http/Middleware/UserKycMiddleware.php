<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserKycMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {

            if ($user->isEmployee() == true) {
                if ($user->kyc) {
                    if ($user->kyc->kyc_status !== 'approved') {
                        return redirect()->route('dashboard')->with('error', 'Please wait till KYC approval.');
                    }
                } else {
                    return redirect()->route('user-kyc.create')->with('error', 'Please complete your KYC.');
                }
            } elseif ($user->isClient() == true) {
                // return response()->json($user->kycClient);
                if ($user->kycClient) {
                    if ($user->kycClient->kyc_status !== 'approved') {
                        return redirect()->route('dashboard')->with('error', 'Please wait till KYC approval.');
                    }
                } else {
                    return redirect()->route('client-kyc.create')->with('error', 'Please complete your KYC.');
                }
            }
        }
        return $next($request);
    }
}
