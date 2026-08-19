<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInquiryStatusLog extends Model
{
    use HasFactory;

    protected $table = 'loan_inquiry_status_log';

    protected $fillable = [
        'inquiry_id',
        'user_id',
        'status_old',
        'status_new',
    ];

    public function inquiry()
    {
        return $this->belongsTo(LoanInquiry::class, 'inquiry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
