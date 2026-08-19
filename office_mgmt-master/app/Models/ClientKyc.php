<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClientKyc extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'owner_name',
        'business_type',
        'business_name',
        'business_address',
        'business_phone',
        'business_email',
        'business_website',
        'business_pan',
        'business_gstin',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_name',
        'bank_branch',
        'bank_document_path',
        'kyc_status',
        'remarks',
        'approved_at',
        'approved_by',
        'rejected_at',
    ];
    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function docs()
    {
        return $this->hasMany(ClientKycDoc::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static $businessTypes = [
        'individual',
        'proprietorship',
        'partnership',
        'llc',
        'limited',
        'opc'
    ];

    public static function getKycStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }
}
