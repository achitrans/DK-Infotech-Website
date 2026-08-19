<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;
    public Carbon $expiresAt;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->expiresAt = Carbon::now()->addMinutes(10);
    }

    public function build()
    {
        return $this->subject('Your password reset code')
            ->view('emails.password_reset_otp');
    }
}
