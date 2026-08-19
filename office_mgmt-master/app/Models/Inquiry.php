<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Inquiry extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'branch_id', 'name', 'email', 'phone', 'subject', 'message', 'source', 'status', 'follow_up_due', 'closed_at','state', 'city'
    ];

    public static function statuses()
    {
        return [
            'new' => 'New',
            'positive' => 'Positive',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function sources()
    {
        return [
            'website' => 'Website',
            'phone' => 'Phone',
            'email' => 'Email',
            'in person' => 'In Person',
            'referral' => 'Referral',
            'other' => 'Other',
            'advertisement' => 'Advertisement',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function followUps() { return $this->hasMany(InquiryFollowUp::class)->latest(); }
}
