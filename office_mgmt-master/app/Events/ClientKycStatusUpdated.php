<?php
namespace App\Events;
use App\Models\ClientKyc;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientKycStatusUpdated
{
    use Dispatchable, SerializesModels;
    public $kyc;
    public $oldStatus;
    public $newStatus;
    public function __construct(ClientKyc $kyc, $oldStatus, $newStatus)
    {
        $this->kyc = $kyc;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
