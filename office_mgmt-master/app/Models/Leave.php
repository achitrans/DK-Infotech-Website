<?php

namespace App\Models;

use App\Services\WhatsappService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
           'branch_id',
        'from_date',
        'to_date',
        'leave_type', // paid, unpaid
        'status', // pending, approved, rejected
        'reason',
        'remarks',
        'applied_by',
        'approved_by',
        'rejected_by',
    ];
    protected $casts = [
        'from_date' => 'date:Y-m-d',
        'to_date'   => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function types()
    {
        return ['paid', 'unpaid'];
    }

    public static function statuses()
    {
        return ['pending', 'approved', 'rejected'];
    }

    public static function sendUpdateNotification($leave)
    {
        $wa = new WhatsappService();

        if ($leave->from_date == $leave->to_date){
            $dateString = "for ".$leave->from_date->format('d M Y');
        }else{
            $dateString = "from ".$leave->from_date->format('d M Y')." to ".$leave->to_date->format('d M Y');
        }

        if ($leave->status == 'approved') {
            $message = "Leave Request – *Approved*

Dear {$leave->user->name},

Your leave request {$dateString} has been approved.
Please ensure that all pending tasks are updated/handed over before proceeding on leave.

Regards,
HR Department
".env('COMPANY_NAME');
        } elseif ($leave->status == 'rejected') {
            $message = "Leave Request – *Rejected*

Dear {$leave->user->name},

We regret to inform you that your leave request {$dateString} has been rejected.

We request you to coordinate with your reporting manager to plan your leave at a suitable time.

Regards,
HR Department
".env('COMPANY_NAME');
        }

        if (isset($message)){
            $wa->sendMessage($leave->user->mobile, $message);
        }

    }
}
