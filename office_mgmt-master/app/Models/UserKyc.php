<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKyc extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'gender',
        'mobile_number',
        'mobile_number_alt',
        'email',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'address_proof_type',
        'address_proof_number',
        'address_proof_doc_path',
        'id_proof_type',
        'id_proof_number',
        'id_proof_doc_path',
        'pan_number',
        'aadhaar_last4',
        'photograph_path',
        'kyc_status',
        'remarks',
        'father_name',
        'father_mobile',
        'blood_group',
        'account_no',
        'ifsc_code',
        'bank_name',
        'bank_branch',
        'bank_doc',
        'qualifications',
        'others',
        'past_experience_letter',
        'past_offer_letter',
        'past_salary_slip',
        'father_aadhar',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'qualifications' => 'array',
        'others' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getKycStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    public static function getAddressProofType()
    {
        return [
            'aadhaar' => 'Aadhaar',
            'passport' => 'Passport',
            'voter id' => 'Voter ID',
            'driving license' => 'Driving License',
            'utility bill' => 'Utility Bill',
        ];
    }

    public static function getIdProofTypeOptions()
    {
        return [
            'aadhaar' => 'Aadhaar',
            'pan' => 'Pan',
            'passport' => 'Passport',
            'voter id' => 'Voter ID',
            'driving license' => 'Driving License',
            'nrega' => 'NREGA',
        ];
    }

    public static function getBloodGroupOptions()
    {
        return [
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'AB+' => 'AB+',
            'AB-' => 'AB-',
            'O+' => 'O+',
            'O-' => 'O-',
            'Rh-null' => 'Rh-null',
        ];
    }
}
