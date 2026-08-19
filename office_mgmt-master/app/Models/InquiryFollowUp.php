<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InquiryFollowUp extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'inquiry_id', 'user_id', 'follow_up_date', 'action_taken', 'remarks'
    ];
    public function inquiry() { return $this->belongsTo(Inquiry::class); }
    public function user() { return $this->belongsTo(User::class); }
}
