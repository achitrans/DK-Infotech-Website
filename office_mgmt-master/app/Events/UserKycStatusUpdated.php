<?php
namespace App\Events;
use App\Models\UserKyc;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserKycStatusUpdated
{
    use Dispatchable, SerializesModels;
    public $kyc;
    public $oldStatus;
    public $newStatus;
    public function __construct(UserKyc $kyc, $oldStatus, $newStatus)
    {
        $this->kyc = $kyc;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
