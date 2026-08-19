<?php
namespace App\Http\Controllers;

use App\Models\UserKyc;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class UserKycController extends Controller
{
    public function index()
    {
        if(!Auth::user()->isAdmin()){
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        $user = Auth::user();
        $kyc = UserKyc::where('user_id', $user->id)->first();
        return view('user_kyc.index', compact('kyc'));
    }

    public function create()
    {
        $user = Auth::user();
        // Only allow if user has no KYC or KYC is rejected
        $kyc = UserKyc::where('user_id', $user->id)->first();
        if ($kyc && $kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You have already submitted KYC.');
        }
        if ($user->isEmployee()) {
            return view('user_kyc.create', compact('kyc'));
        }else{
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $kyc = UserKyc::where('user_id', $user->id)->first();
        if ($kyc && $kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You have already submitted KYC.');
        }
        $data = $request->validate([
            'full_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|max:100',
            'mobile_number' => 'required|max:15',
            'mobile_number_alt' => 'required|max:15',
            'address_line1' => 'required|max:100',
            'address_line2' => 'required|max:100',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'country' => 'required|max:100',
            'postal_code' => 'required|max:8',
            'address_proof_type' => 'required|max:100',
            'address_proof_number' => 'required|max:100',
            'address_proof_doc_path' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'id_proof_type' => 'required|max:100',
            'id_proof_number' => 'required|max:100',
            'id_proof_doc_path' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'pan_number' => 'required|max:10',
            'aadhaar_last4' => 'required|numeric|digits:4',
            'photograph_path' => 'required|file|mimes:jpg,jpeg,png',
            'father_name' => 'required|string|max:100',
            'father_mobile' => 'required|string|max:100',
            'father_aadhar' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'blood_group' => 'required|string|max:100',
            'account_no' => 'required|string|max:100',
            'ifsc_code' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'required|string|max:100',
            'bank_doc' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => "nullable|in:Matriculation,Intermediate,Diploma,Bachelor,Master,PhD",
            'qualifications.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'qualifications.*.board' => 'nullable|string|max:100',
            'qualifications.*.college' => 'nullable|string|max:100',
            'qualifications.*.grade' => 'nullable|string|max:100',
            'past_experience_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'past_offer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'past_salary_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        // Handle file uploads
        foreach(['address_proof_doc_path', 'id_proof_doc_path', 'photograph_path','bank_doc','past_experience_letter','past_offer_letter','past_salary_slip','father_aadhar'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);
                $path = $file->store('kyc_docs', 'public');
                $data[$fileField] = $path;
            }
        }
        if (array_key_exists('qualifications', $data)) {
            $qualificationsInput = is_array($data['qualifications']) ? $data['qualifications'] : [];
            $data['qualifications'] = $this->normalizeQualificationFiles($qualificationsInput);
        }
        $data['user_id'] = $user->id;
        $data['kyc_status'] = 'pending';
        $data['email'] = $user->email;
        if ($kyc) {
            $kyc->update($data);
        } else {
            UserKyc::create($data);
        }
        return redirect()->route('dashboard')->with('success', 'KYC submitted successfully.');
    }

    public function show($userId)
    {
        try {
            $userId = Crypt::decrypt($userId);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Invalid request.');
        }
        if(!Auth::user()->isAdmin() && Auth::id() != $userId){
            return redirect()->route('dashboard')->with('error', 'Access denied..');
        }
        $kyc = UserKyc::where('user_id', $userId)->first();
        if (!$kyc) {
            if( Auth::user()->isAdmin()) {
                return back()->with('error', 'KYC not found for this user.');
            }
            return redirect()->route('user-kyc.create')->with('error', 'KYC not found.');
        }
        return view('user_kyc.show', compact('kyc'));
    }

    public function edit()
    {
        $user = Auth::user();
        $kyc = UserKyc::where('user_id', $user->id)->firstOrFail();
        if ($kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You can only edit KYC if it is rejected.');
        }
        return view('user_kyc.edit', compact('kyc'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $kyc = UserKyc::where('user_id', $user->id)->firstOrFail();
        if ($kyc->kyc_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'You can only edit KYC if it is rejected.');
        }
        $data = $request->validate([
            'full_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|max:100',
            'mobile_number' => 'required|max:15',
            'mobile_number_alt' => 'required|max:15',
            'address_line1' => 'required|max:100',
            'address_line2' => 'required|max:100',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'country' => 'required|max:100',
            'postal_code' => 'required|max:8',
            'address_proof_type' => 'required|max:100',
            'address_proof_number' => 'required|max:100',
            'address_proof_doc_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'id_proof_type' => 'required|max:100',
            'id_proof_number' => 'required|max:100',
            'id_proof_doc_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'pan_number' => 'required|max:10',
            'aadhaar_last4' => 'required|max:4',
            'father_name' => 'required|string|max:100',
            'father_mobile' => 'required|string|max:15',
            'father_aadhar' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'blood_group' => 'required|string|max:100',
            'account_no' => 'required|string|max:100',
            'ifsc_code' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'bank_branch' => 'required|string|max:100',
            'photograph_path' => 'nullable|file|mimes:jpg,jpeg,png',
            'bank_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => "nullable|in:Matriculation,Intermediate,Diploma,Bachelor,Master,PhD",
            'qualifications.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'qualifications.*.board' => 'nullable|string|max:100',
            'qualifications.*.college' => 'nullable|string|max:100',
            'qualifications.*.grade' => 'nullable|string|max:100',
            'past_experience_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'past_offer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'past_salary_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        // Delete old files if new ones uploaded
        foreach(['address_proof_doc_path', 'id_proof_doc_path', 'photograph_path','bank_doc','past_experience_letter','past_offer_letter','past_salary_slip','father_aadhar'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($kyc->$fileField) {
                    Storage::disk('public')->delete($kyc->$fileField);
                }
                $file = $request->file($fileField);
                $path = $file->store('kyc_docs', 'public');
                $data[$fileField] = $path;
            }
        }
        if (array_key_exists('qualifications', $data)) {
            $qualificationsInput = is_array($data['qualifications']) ? $data['qualifications'] : [];
            $existingQualifications = is_array($kyc->qualifications) ? $kyc->qualifications : [];
            $data['qualifications'] = $this->normalizeQualificationFiles($qualificationsInput, $existingQualifications);
        }
        $data['kyc_status'] = 'pending';
        $data['email'] = $user->email;
        $kyc->update($data);
        return redirect()->route('dashboard')->with('success', 'KYC updated and submitted for review.');
    }

        // Admin-only: update KYC status and remarks
    public function updateStatus(Request $request, $kycId)
    {
        $kyc = UserKyc::findOrFail($kycId);
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        $data = $request->validate([
            'kyc_status' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string|max:200',
        ]);

        $oldStatus = $kyc->kyc_status;
        $kyc->update($data);
        if($oldStatus != $kyc->kyc_status && strtolower($kyc->kyc_status) != 'pending'){
            event(new \App\Events\UserKycStatusUpdated($kyc, $oldStatus, $kyc->kyc_status));
        }

        return redirect()->route('user-kyc.show', Crypt::encrypt($kyc->user_id))->with('success', 'KYC status updated.');

    }

    // Replace qualification upload objects with stored paths and clean up superseded files.
    protected function normalizeQualificationFiles(array $qualifications, array $existingQualifications = []): array
    {
        foreach ($qualifications as $index => $qualification) {
            if (!is_array($qualification)) {
                continue;
            }

            $uploadedFile = $qualification['file'] ?? null;

            if ($uploadedFile instanceof UploadedFile) {
                $existingPath = $existingQualifications[$index]['file'] ?? null;
                if ($existingPath) {
                    Storage::disk('public')->delete($existingPath);
                }
                $qualifications[$index]['file'] = $uploadedFile->store('kyc_docs', 'public');
            } elseif (isset($existingQualifications[$index]['file'])) {
                $qualifications[$index]['file'] = $existingQualifications[$index]['file'];
            } else {
                $qualifications[$index]['file'] = null;
            }
        }

        return $qualifications;
    }

}
