<?php

namespace App\Http\Controllers;

use App\Models\User;

class ClientLookupController extends Controller
{
    public function show(User $client)
    {
        if (!$client->isClient()) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->loadMissing('kycClient');

        return response()->json([
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'mobile' => $client->mobile,
            'kyc' => [
                'business_name' => $client->kycClient->business_name ?? null,
                'business_address' => $client->kycClient->business_address ?? null,
                'business_phone' => $client->kycClient->business_phone ?? null,
                'business_email' => $client->kycClient->business_email ?? null,
                'business_gstin' => $client->kycClient->business_gstin ?? null,
            ],
        ]);
    }
}