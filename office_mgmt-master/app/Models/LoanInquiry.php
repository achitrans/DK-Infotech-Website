<?php

namespace App\Models;

use App\Services\WhatsappService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanInquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category',
        'type',
        'amount',
        'tenure',
        'name',
        'email',
        'phone',
        'gender',
        'dob',
        'city',
        'state',
        'pin_code',
        'remarks',
        'statement_file',
        'pan',
        'aadhar',
        'source',
        'status',
        'follow_up_due',
        'closed_at',
    ];

    protected $casts = [
        'follow_up_due' => 'date'
    ];

    public static $categories = [
        'retail' => 'Retail',
        'business' => 'Business',
        'agriculture' => 'Agriculture',
        'others' => 'Others',
    ];

    public static $sources = [
        'website' => 'Website',
        'phone' => 'Phone',
        'email' => 'Email',
        'in person' => 'In Person',
        'referral' => 'Referral',
        'other' => 'Other',
        'advertisement' => 'Advertisement',
    ];

    public static $statuses = [
//        'inquiry' => 'Inquiry',
        'login' => 'Login',
        'credit' => 'Credit',
        'technical' => 'Technical',
        'legal' => 'Legal',
        'sanction' => 'Sanction',
        'disburse' => 'Disburse',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(LoanInquiryStatusLog::class, 'inquiry_id');
    }

    public static function sendNotification(LoanInquiry $loanInquiry)
    {
        if(in_array($loanInquiry->status,['technical','legal','sanction','disburse','login'])){
            $message = "Dear {$loanInquiry->name},\n";

            if ($loanInquiry->status=='login'){
                $message .= "We have successfully received your loan file. Our team will begin the verification and processing shortly.

You will be updated on the status at each stage. If any additional information or documents are required, we will contact you.

Thank you for choosing ".env('COMPANY_NAME');
            }elseif(in_array($loanInquiry->status,['technical','legal','sanction'])){
                $message .= "We are pleased to inform you that your loan has been successfully {$loanInquiry->status}. The approved amount of {$loanInquiry->amount} for {$loanInquiry->tenure} month(s) is now ready for disbursement After Next Step.

If you need any further details or have any questions, feel free to contact us.";
            }elseif($loanInquiry->status=='disburse'){
                $message .="We are pleased to inform you that your loan of {$loanInquiry->amount} for {$loanInquiry->tenure} month(s) has been successfully disbursed. The funds should reflect in your account within 10 business days.

If you have any questions, feel free to contact us.";
            }

            $message .= "\nBest regards,\n". env('COMPANY_NAME');
            $ws = new WhatsappService();

            $ws->sendMessage($loanInquiry->phone, $message);
        }


    }
}
