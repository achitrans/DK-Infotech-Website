@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Client KYC Details</h2>
    <div class="card mb-3">
        <div class="card-header">
            <strong>{{ $kyc->business_name ?? '-' }}</strong> ({{ $kyc->business_type ?? '-' }})
            @if ($kyc->kyc_status == 'rejected' && auth()->user()->isClient())
                <a href="{{ route('client-kyc.edit', $kyc->id) }}" class="btn btn-warning btn-sm float-right">Update Kyc</a>
            @endif
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Status</th><td><span class="badge bg-{{ $kyc->kyc_status == 'approved' ? 'success' : ($kyc->kyc_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($kyc->kyc_status) }}</span></td></tr>
                    <tr><th>Submitted At</th><td>{{ $kyc->created_at ? $kyc->created_at->format('d-m-Y H:i') : '-' }}</td></tr>
                    <tr><th>Owner Name</th><td>{{ $kyc->owner_name ?? '-' }}</td></tr>
                    <tr><th>Business Name</th><td>{{ $kyc->business_name ?? '-' }}</td></tr>
                    <tr><th>Business Address</th><td>{{ $kyc->business_address ?? '-' }}</td></tr>
                    <tr><th>Business Phone</th><td>{{ $kyc->business_phone ?? '-' }}</td></tr>
                    <tr><th>Business Email</th><td>{{ $kyc->business_email ?? '-' }}</td></tr>
                    <tr><th>Business Website</th><td>{{ $kyc->business_website ?? '-' }}</td></tr>
                    <tr><th>Business PAN</th><td>{{ $kyc->business_pan ?? '-' }}</td></tr>
                    <tr><th>Business GSTIN</th><td>{{ $kyc->business_gstin ?? '-' }}</td></tr>
                    <tr><th>Bank Account Number</th><td>{{ $kyc->bank_account_number ?? '-' }}</td></tr>
                    <tr><th>Bank IFSC Code</th><td>{{ $kyc->bank_ifsc_code ?? '-' }}</td></tr>
                    <tr><th>Bank Name</th><td>{{ $kyc->bank_name ?? '-' }}</td></tr>
                    <tr><th>Bank Branch</th><td>{{ $kyc->bank_branch ?? '-' }}</td></tr>
                    <tr><th>Approved At</th><td>{{ $kyc->approved_at ? $kyc->approved_at->format('d-m-Y H:i') : '-' }}</td></tr>
                    <tr><th>Approved By</th><td>{{ $kyc->approver ? $kyc->approver->name : '-' }}</td></tr>
                    <tr><th>Rejected At</th><td>{{ $kyc->rejected_at ? $kyc->rejected_at->format('d-m-Y H:i') : '-' }}</td></tr>
                </tbody>
            </table>
            <hr>
            <h5>Documents</h5>
            <ul>
                @foreach($kyc->docs as $doc)
                <li>
                    <strong>{{ $doc->document_type }}:</strong>
                    @if(is_array($doc->document_path))
                        @foreach($doc->document_path as $path)
                            <a href="{{ Storage::url($path) }}" target="_blank">View</a>
                        @endforeach
                    @else
                        <a href="{{ Storage::url($doc->document_path) }}" target="_blank">View</a>
                    @endif
                </li>
                @endforeach
            </ul>
            <hr>
            <h5>Remarks</h5>
            <p>{{ $kyc->remarks ?? 'N/A' }}</p>
        </div>
    </div>

    @if (auth()->user()->isAdmin())
            <div class="card mb-3">
                <div class="card-header">Update KYC Status</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('client-kyc.updateStatus', $kyc->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kyc_status">KYC Status</label>
                            <select name="kyc_status" id="kyc_status" class="form-control">
                                @foreach (\App\Models\ClientKyc::getKycStatusOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ $kyc->kyc_status == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control"
                                value="{{ old('remarks', $kyc->remarks) }}">
                        </div>
                        <button type="submit" class="btn btn-success">Update Status</button>
                    </form>
                </div>
            </div>
        @endif

    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Home</a>

</div>
@endsection
