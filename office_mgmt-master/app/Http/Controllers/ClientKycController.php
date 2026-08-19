<?php
namespace App\Http\Controllers;

use App\Models\ClientKyc;
use App\Models\ClientKycDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class ClientKycController extends Controller
{
    // List KYC for current user
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        $user = Auth::user();
        $kyc = ClientKyc::where('user_id', $user->id)->first();
        return view('client_kyc.index', compact('kyc'));
    }

    // Show KYC for a user (admin or owner)
    public function show($userId)
    {
        try {
            $userId = Crypt::decrypt($userId);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Invalid request.');
        }
        $user = Auth::user();
        if ($user->type !== 'admin' && $user->id != $userId) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        $kyc = ClientKyc::where('user_id', $userId)->first();
        if (!$kyc) {
            if( Auth::user()->isAdmin()) {
                return back()->with('error', 'KYC not found for this client.');
            }
            return redirect()->route('client-kyc.create')->with('error', 'KYC not found.');
        }
        return view('client_kyc.show', compact('kyc'));
    }
    // Show KYC form for client
    public function create()
    {
        $user = Auth::user();
        $kyc = ClientKyc::where('user_id', $user->id)->first();
        // Only allow if no KYC or rejected
        if ($kyc && $kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You have already submitted KYC.');
        }

        if ($user->isClient() || $user->isAssociate()) {
            // Document requirements by business type
            $defaultType = \App\Models\ClientKyc::$businessTypes[0];
            $docTypes = \App\Models\ClientKycDoc::getRequiredDocumentTypes($defaultType);
            return view('client_kyc.create', compact('kyc', 'docTypes'));
        } else {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
    }

    // Store client KYC
    public function store(Request $request)
    {
        $user = Auth::user();
        $kyc = ClientKyc::where('user_id', $user->id)->first();
        if ($kyc) {
            return redirect()->route('dashboard')->with('error', 'You have already submitted KYC.');
        }
        $businessType = $request->input('business_type');
        $docTypes = \App\Models\ClientKycDoc::getRequiredDocumentTypes($businessType);
        $rules = [
            'business_type' => 'required|in:' . implode(',', (\App\Models\ClientKyc::$businessTypes)),
            'business_name' => 'required|max:100',
            'owner_name' => 'required|max:100',
            'business_address' => 'required|max:150',
            'business_email' => 'required|email:rfc,dns,strict|max:100',
            'business_phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'business_pan' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'business_gstin' => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'business_website' => 'nullable|url|max:150',
            'bank_account_number' => ['nullable', 'regex:/^[A-Za-z0-9]{8,32}$/'],
            'bank_ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_name' => 'nullable|max:100',
            'bank_branch' => 'nullable|max:100',
        ];
        // All docs mandatory
        foreach ($docTypes as $type) {
            if (strpos($type, '[]') !== false) {
                $type = str_replace('[]', '', $type);
                $rules[$type] = 'required|array|min:1';
                $rules[ $type. '.*'] = 'file|mimes:pdf,jpg,jpeg,png';
            } else {
                $rules[$type] = 'required|file|mimes:pdf,jpg,jpeg,png';
            }
        }
        // return $rules;
        // return dd($request->all()); // Debugging line to inspect request data

        $data = $request->validate($rules);
        $data['user_id'] = $user->id;
        $data['kyc_status'] = 'pending';
        $kyc = ClientKyc::create($data);
        // Save docs
        foreach ($docTypes as $type) {
            if (strpos($type, '[]') !== false) {
                $type = str_replace('[]', '', $type);
                foreach ($request->file($type, []) as $file) {
                    $path = $file->store('client_kyc_docs', 'public');
                    ClientKycDoc::create([
                        'client_kyc_id' => $kyc->id,
                        'document_type' => $type,
                        'document_path' => $path,
                    ]);
                }
            } else {
                $file = $request->file($type);
                $path = $file->store('client_kyc_docs', 'public');
                ClientKycDoc::create([
                    'client_kyc_id' => $kyc->id,
                    'document_type' => $type,
                    'document_path' => $path,
                ]);
            }
        }
        return redirect()->route('dashboard')->with('success', 'KYC submitted successfully.');
    }

    // Edit client KYC (only if rejected)
    public function edit()
    {
        $user = Auth::user();
        $kyc = ClientKyc::where('user_id', $user->id)->firstOrFail();
        if ($kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You can only edit KYC if it is rejected.');
        }
        $docTypes = \App\Models\ClientKycDoc::getRequiredDocumentTypes($kyc->business_type);
        return view('client_kyc.edit', compact('kyc', 'docTypes'));
    }

    // Update client KYC
    public function update(Request $request)
    {
        $user = Auth::user();
        $kyc = ClientKyc::where('user_id', $user->id)->firstOrFail();
        if ($kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You can only edit KYC if it is rejected.');
        }
        $businessType = $request->input('business_type');
        $docTypes = \App\Models\ClientKycDoc::getRequiredDocumentTypes($businessType);
        $rules = [
            'business_type' => 'required|in:' . implode(',', (\App\Models\ClientKyc::$businessTypes)),
            'business_name' => 'required|max:100',
            'owner_name' => 'required|max:100',
            'business_address' => 'required|max:150',
            'business_email' => 'required|email:rfc,dns,strict|max:100',
            'business_phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'business_pan' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'business_gstin' => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'business_website' => 'nullable|url|max:150',
            'bank_account_number' => ['nullable', 'regex:/^[A-Za-z0-9]{8,32}$/'],
            'bank_ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ];
        foreach ($docTypes as $type) {
            if (strpos($type, '[]') !== false) {
                $type = str_replace('[]', '', $type);
                $rules[$type] = 'array|min:1';
                $rules[ $type. '.*'] = 'file|mimes:pdf,jpg,jpeg,png';
            } else {
                $rules[$type] = 'file|mimes:pdf,jpg,jpeg,png';
            }
        }

        $data = $request->validate($rules);
        $data['kyc_status'] = 'pending';

        // Remove old docs and save new ones
        // $kyc->docs()->delete();
        foreach ($docTypes as $type) {
            if (strpos($type, '[]') !== false) {
                $type = str_replace('[]', '', $type);
                foreach ($request->file($type, []) as $file) {
                    if (!$file) continue; // Skip if no file uploaded
                    $path = $file->store('client_kyc_docs', 'public');
                    ClientKycDoc::updateOrCreate([
                        'client_kyc_id' => $kyc->id,
                        'document_type' => $type,
                        'document_path' => $path,
                    ]);
                }
            } else {
                $file = $request->file($type);
                if (!$file) continue; // Skip if no file uploaded
                $path = $file->store('client_kyc_docs', 'public');
                ClientKycDoc::updateOrCreate([
                    'client_kyc_id' => $kyc->id,
                    'document_type' => $type,
                    'document_path' => $path,
                ]);
            }
        }
         $kyc->update($data);
        return redirect()->route('dashboard')->with('success', 'KYC updated and submitted for review.');
    }

            // Admin-only: update KYC status and remarks
    public function updateStatus(Request $request, $kycId)
    {
        $kyc = ClientKyc::findOrFail($kycId);
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        $data = $request->validate([
            'kyc_status' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ]);
        $oldStatus = $kyc->kyc_status;
        $kyc->update($data);
        if ($data['kyc_status'] === 'approved') {
            $kyc->approved_at = now();
            $kyc->approved_by = Auth::id();
        } elseif ($data['kyc_status'] === 'rejected') {
            $kyc->rejected_at = now();
            // Delete files from storage before deleting docs
            // foreach ($kyc->docs as $doc) {
            //     if ($doc->document_path) {
            //         Storage::disk('public')->delete($doc->document_path);
            //     }
            // }
            // $kyc->docs()->delete(); // Remove all docs on rejection
        }
        $kyc->save();

        if($oldStatus != $kyc->kyc_status && strtolower($kyc->kyc_status) != 'pending'){
            event(new \App\Events\ClientKycStatusUpdated($kyc, $oldStatus, $kyc->kyc_status));
        }

        return redirect()->route('client-kyc.show', Crypt::encrypt($kyc->user_id))->with('success', 'KYC status updated.');
    }
}
