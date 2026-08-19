@extends('layouts.app')
@section('title', 'Edit Client')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <form method="POST" action="{{ route('clients.update', $client->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card mb-4">
                        <div class="card-header">Edit Client - {{ $client->name }}</div>
                        <div class="card-body">
                            <span class="text-warning fs-4">Basic Information</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $client->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Tawk Code</label>
                                    <input type="text" name="tawk_code" class="form-control" value="{{ old('tawk_code', $client->tawk_code) }}" placeholder="Enter Tawk code">
                                </div>
                            </div>

                            <span class="text-warning fs-4">Personal Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Mobile</label>
                                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $client->mobile) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client KYC Edit Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-file-contract mr-1"></i> Business & KYC Information
                        </div>
                        <div class="card-body">
                            <span class="text-warning fs-4">Business Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label>Business Type</label>
                                    <select name="business_type" class="form-control">
                                        @foreach (\App\Models\ClientKyc::$businessTypes as $typeOption)
                                            <option value="{{ $typeOption }}" {{ old('business_type', $client->kycClient->business_type ?? 'individual') == $typeOption ? 'selected' : '' }}>
                                                {{ ucfirst($typeOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Company Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $client->kycClient->business_name ?? '') }}" placeholder="Enter Company Name">
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Business GSTIN</label>
                                    <input type="text" name="business_gstin" class="form-control" value="{{ old('business_gstin', $client->kycClient->business_gstin ?? '') }}" placeholder="Enter GSTIN">
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label>Business Phone</label>
                                    <input type="text" name="business_phone" class="form-control" value="{{ old('business_phone', $client->kycClient->business_phone ?? '') }}" placeholder="Enter Business Phone">
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Business Email</label>
                                    <input type="email" name="business_email" class="form-control" value="{{ old('business_email', $client->kycClient->business_email ?? '') }}" placeholder="Enter Business Email">
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Business PAN</label>
                                    <input type="text" name="business_pan" class="form-control" value="{{ old('business_pan', $client->kycClient->business_pan ?? '') }}" placeholder="Enter Business PAN">
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Business Address</label>
                                    <input type="text" name="business_address" class="form-control" value="{{ old('business_address', $client->kycClient->business_address ?? '') }}" placeholder="Enter Business Address">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Business Website</label>
                                    <input type="url" name="business_website" class="form-control" value="{{ old('business_website', $client->kycClient->business_website ?? '') }}" placeholder="https://example.com">
                                </div>
                            </div>

                            <hr>
                            <span class="text-warning fs-4">Bank Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label>Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $client->kycClient->bank_name ?? '') }}" placeholder="Bank Name">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $client->kycClient->bank_account_number ?? '') }}" placeholder="Account Number">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>IFSC Code</label>
                                    <input type="text" name="bank_ifsc_code" class="form-control" value="{{ old('bank_ifsc_code', $client->kycClient->bank_ifsc_code ?? '') }}" placeholder="IFSC Code">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Branch</label>
                                    <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $client->kycClient->bank_branch ?? '') }}" placeholder="Branch Name">
                                </div>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <hr>
                                <span class="text-warning fs-4">KYC Approval & Remarks</span>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6">
                                        <label>KYC Status</label>
                                        <select name="kyc_status" class="form-control">
                                            @foreach (\App\Models\ClientKyc::getKycStatusOptions() as $kStatus => $kLabel)
                                                <option value="{{ $kStatus }}" {{ old('kyc_status', $client->kycClient->kyc_status ?? 'pending') == $kStatus ? 'selected' : '' }}>
                                                    {{ $kLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label>Remarks</label>
                                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $client->kycClient->remarks ?? '') }}" placeholder="Approval / Rejection remarks">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-primary">Update Client</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
