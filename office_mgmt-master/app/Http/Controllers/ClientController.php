<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $query = User::where('type', 'client')->with('kycClient');

        if (request('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('email')) {
            $query->where('email', 'like', '%' . request('email') . '%');
        }
        if (request('status') && array_key_exists(request('status'), User::$status)) {
            $query->where('status', request('status'));
        }

        if (\Auth::user()->type != 'admin' && \Auth::user()->type != 'branch manager') {
            $query->where('parent_id', \Auth::id());
        }

        if (!request()->has('search') && \Auth::user()->type == 'branch manager') {
            $query->where('branch_id', \Auth::user()->branch_id);
        }

        $clients = $query->get();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns,strict|unique:users,email',
            'mobile' => 'required|string|max:15',
            'status' => 'required|in:active,inactive,suspended',
            'tawk_code' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        $client = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'department' => 'client',
            'type' => 'client',
            'status' => $data['status'],
            'tawk_code' => $data['tawk_code'] ?? null,
            'password' => !empty($data['password']) ? $data['password'] : (env('DEFAULT_PASSWORD', Str::random(15))),
            'employee_id' => null,
            'created_by' => \Auth::id(),
            'parent_id' => \Auth::id(),
            'branch_id' => $this->branchContext->currentBranchId(),
        ]);



        event(new UserCreated($client));

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }

    public function edit($id)
    {
        $client = User::where('type', 'client')->with('kycClient')->findOrFail($id);

        if (\Auth::user()->type != 'admin' && $client->parent_id != \Auth::id()) {
            return abort(404);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = User::where('type', 'client')->findOrFail($id);

        if (\Auth::user()->type != 'admin' && $client->parent_id != \Auth::id()) {
            return abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns,strict|unique:users,email,' . $id,
            'mobile' => 'required|string|max:15',
            'status' => 'required|in:active,inactive,suspended',
            'tawk_code' => 'nullable|string|max:100',
            // KYC Fields
            'owner_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|in:individual,proprietorship,partnership,llc,limited,opc',
            'company_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'business_phone' => 'nullable|string|max:255',
            'business_email' => 'nullable|email:rfc,dns,strict|max:255',
            'business_website' => 'nullable|string|max:255',
            'business_pan' => 'nullable|string|max:20',
            'business_gstin' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'kyc_status' => 'nullable|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ]);

        $client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'status' => $data['status'],
            'tawk_code' => $data['tawk_code'] ?? null,
        ]);

        $kycPayload = [
            'owner_name' => $data['owner_name'] ?? ($client->kycClient->owner_name ?? null),
            'business_type' => $data['business_type'] ?? ($client->kycClient->business_type ?? 'individual'),
            'business_name' => $data['company_name'] ?? ($client->kycClient->business_name ?? $client->name),
            'business_address' => $data['business_address'] ?? ($client->kycClient->business_address ?? 'N/A'),
            'business_phone' => $data['business_phone'] ?? ($client->kycClient->business_phone ?? $data['mobile']),
            'business_email' => $data['business_email'] ?? ($client->kycClient->business_email ?? $data['email']),
            'business_website' => $data['business_website'] ?? ($client->kycClient->business_website ?? null),
            'business_pan' => $data['business_pan'] ?? ($client->kycClient->business_pan ?? 'N/A'),
            'business_gstin' => $data['business_gstin'] ?? ($client->kycClient->business_gstin ?? null),
            'bank_account_number' => $data['bank_account_number'] ?? ($client->kycClient->bank_account_number ?? null),
            'bank_ifsc_code' => $data['bank_ifsc_code'] ?? ($client->kycClient->bank_ifsc_code ?? null),
            'bank_name' => $data['bank_name'] ?? ($client->kycClient->bank_name ?? null),
            'bank_branch' => $data['bank_branch'] ?? ($client->kycClient->bank_branch ?? null),
        ];

        if (\Auth::user()->isAdmin() && isset($data['kyc_status'])) {
            $kycPayload['kyc_status'] = $data['kyc_status'];
            if ($data['kyc_status'] === 'approved') {
                $kycPayload['approved_at'] = now();
                $kycPayload['approved_by'] = \Auth::id();
            } elseif ($data['kyc_status'] === 'rejected') {
                $kycPayload['rejected_at'] = now();
            }
        }
        if (isset($data['remarks'])) {
            $kycPayload['remarks'] = $data['remarks'];
        }

        $client->kycClient()->updateOrCreate(
            ['user_id' => $client->id],
            $kycPayload
        );

        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }

    public function destroy($id)
    {
        if (!\Auth::user()->isAdmin()) {
            return abort(403, 'Unauthorized action.');
        }

        $client = User::where('type', 'client')->findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }
}
