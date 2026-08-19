<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KycStatusMail extends Mailable
{
    use Queueable, SerializesModels;
    public $kyc;
    public $oldStatus;
    public $newStatus;
    public $userType;
    public function __construct($userType, $kyc)
    {
        $this->userType = $userType;
        $this->kyc = $kyc;
    }
    public function build()
    {
        return $this->subject('KYC Status Update')
            ->view('emails.kyc_status');
    }
}
