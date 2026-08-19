<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientKycDoc extends Model
{
    protected $fillable = [
        'client_kyc_id',
        'document_type',
        'document_path',
    ];

    public function clientKyc()
    {
        return $this->belongsTo(ClientKyc::class);
    }

    public static function getRequiredDocumentTypes($businessType)
    {
        $requiredDocuments = [
            'individual'      => ['pan', 'address_proof', 'bank_document', 'gst'],
            'proprietorship'  => ['proprietor_pan', 'proprietor_address_proof', 'business_registration', 'bank_document', 'gst'],
            'partnership'     => ['partnership_pan', 'partnership_deed', 'bank_document', 'gst', 'partners_identity_proof[]', 'partners_address_proof[]'],
            'llc'             => ['llc_pan', 'coi', 'bank_document', 'gst', 'partners_identity_proof[]', 'partners_address_proof[]'],
            'limited'         => ['coi', 'moa', 'aoa', 'company_pan', 'bank_document', 'gst', 'directors_identity_proof[]', 'directors_address_proof[]','owner_identity_proof', 'owner_address_proof'],
            'opc'             => ['coi', 'moa', 'aoa', 'company_pan', 'bank_document', 'gst', 'directors_identity_proof', 'directors_address_proof'],
        ];

        return $requiredDocuments[$businessType] ?? [];
    }
}
