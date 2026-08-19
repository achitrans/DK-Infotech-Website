<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetOtpMail;
use App\Models\ClientKyc;
use App\Models\User;
use App\Models\UserKyc;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showPasswordResetForm()
    {
        return view('auth.password_reset');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        if (Auth::attempt($credentials)) {
            if (Auth::user()->status !== 'active') {
                Auth::logout();

                return back()->withErrors(['status' => 'Your account is not active.']);
            }
            $request->session()->regenerate();

            if (Auth::user()->isClient() && ! ClientKyc::where('user_id', Auth::id())->first()) {
                return redirect()->route('client-kyc.create')->with('warning', 'Please complete your KYC before proceeding.');
            } elseif (Auth::user()->isAssociate() && ! ClientKyc::where('user_id', Auth::id())->first()) {
                return redirect()->route('client-kyc.create')->with('warning', 'Please complete your KYC before proceeding.');
            } elseif (Auth::user()->isEmployee() && ! UserKyc::where('user_id', Auth::id())->first()) {
                return redirect()->route('user-kyc.create')->with('warning', 'Please complete your KYC before proceeding.');
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = $this->normalizeIdentifier($request->input('identifier'));
        $user = $this->findActiveUserByIdentifier($identifier);

        if (! $user) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'We could not find an active user with the provided email or mobile number.']);
        }

        $cacheKey = $this->passwordResetCacheKey($identifier);
        $existing = Cache::get($cacheKey);

        if ($existing && isset($existing['sent_at']) && now()->timestamp - $existing['sent_at'] < 120) {
            $waitSeconds = 120 - (now()->timestamp - $existing['sent_at']);

            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => "Please wait $waitSeconds seconds before requesting another OTP."]);
        }

        $otp = (string) random_int(100000, 999999);
        $payload = [
            'identifier' => $identifier,
            'otp' => Hash::make($otp),
            'sent_at' => now()->timestamp,
        ];

        //        Log::info('Generated password reset OTP', ['user_id' => $user->id, 'identifier' => $identifier, 'otp' => $otp ]);

        Cache::put($cacheKey, $payload, now()->addMinutes(10));

        if ($user->email) {
            $validator = Validator::make(['email' => $user->email], [
                'email' => 'email:rfc,dns',
            ]);

            if ($validator->passes()) {
                try {
                    \App\Jobs\SendEmailJob::dispatch($user->email, new PasswordResetOtpMail($user, $otp));
                } catch (\Throwable $e) {
                    Log::error('Password reset OTP email dispatch failed: ' . $e->getMessage());
                }
            } else {
                Log::warning('Skipping password reset email for user '.$user->id.' because the email is invalid (RFC/DNS check failed).', [
                    'email' => $user->email,
                ]);
            }
        }

        $this->sendWhatsappOtp($user, $otp);

        return back()
            ->withInput($request->only('identifier'))
            ->with('success', 'OTP sent to your registered email and WhatsApp (if a valid number exists).')
            ->with('otp_sent', true);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $identifier = $this->normalizeIdentifier($request->input('identifier'));
        $cacheKey = $this->passwordResetCacheKey($identifier);
        $payload = Cache::get($cacheKey);

        if (! $payload || ! Hash::check($request->input('otp'), $payload['otp'])) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['otp' => 'The one-time code is invalid or has expired.']);
        }

        $user = $this->findActiveUserByIdentifier($identifier);

        if (! $user) {
            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'We could not find an active user with the provided identifier.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        Cache::forget($cacheKey);

        return redirect()->route('login')->with('success', 'Password updated. You may now log in with your new password.');
    }

    public function changePasswordForm()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        if (config('app.password_change_access') == false) {
            return back()->withErrors(['current_password' => 'Password change not allowed.']);
        }
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        $user = Auth::user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }

    public function loginByPass($id)
    {
        try {
            $id = \Crypt::decrypt($id);
            $user = User::findOrFail($id);
            Auth::login($user);
            \request()->session()->regenerate();

            return redirect()->intended('dashboard');

        } catch (\Exception $exception) {
            return back()->with('error', 'Invalid Request');
        }
    }

    public function loginByToken(Request $request)
    {
        if ($request->token) {
            $token = $request->token;
            $data = PersonalAccessToken::findToken($token);
            if ($data && $data->tokenable) {
                $user = $data->tokenable;
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid Token', 'data' => []], 401);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid Request', 'data' => []], 400);
        }
    }

    protected function normalizeIdentifier(string $value): string
    {
        $value = trim($value);
        if (Str::contains($value, '@')) {
            return Str::lower($value);
        }

        return preg_replace('/\D+/', '', $value);
    }

    protected function passwordResetCacheKey(string $identifier): string
    {
        return 'password-reset:'.hash('sha256', $identifier);
    }

    protected function findActiveUserByIdentifier(string $identifier): ?User
    {
        $query = User::where('status', 'active');

        if (Str::contains($identifier, '@')) {
            return $query->whereRaw('LOWER(email) = ?', [Str::lower($identifier)])->first();
        }

        return $query->where('mobile', $identifier)->first();
    }

    protected function sendWhatsappOtp(User $user, string $otp): void
    {
        if (empty($user->mobile)) {
            return;
        }

        try {
            $message = sprintf(
                'Your %s password reset OTP is %s. It expires in 10 minutes.',
                config('app.name', 'Portal'),
                $otp
            );

            app(WhatsappService::class)->sendMessageAsync($user->mobile, $message);
        } catch (Throwable $exception) {
            Log::warning('Unable to deliver WhatsApp password reset OTP', [
                'user_id' => $user->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
