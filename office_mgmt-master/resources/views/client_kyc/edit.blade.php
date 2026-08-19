@extends('layouts.app')
@section('title', 'Edit Client KYC')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Edit Client KYC</div>
        <div class="card-body">
            <form method="POST" action="{{ route('client-kyc.update', auth()->id()) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="business_type">Business Type</label>
                        <select name="business_type" id="business_type" class="form-control" required readonly>
                            @foreach(\App\Models\ClientKyc::$businessTypes as $type)
                                <option value="{{ $type }}" {{ old('business_type', $kyc->business_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="business_name">Business Name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $kyc->business_name) }}" maxlength="100" required>
                        <small class="form-text text-muted">e.g., ABC Enterprises Pvt Ltd</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="owner_name">Owner Name</label>
                        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $kyc->owner_name) }}" maxlength="100" required>
                        <small class="form-text text-muted">e.g., John Doe</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_address">Business Address</label>
                        <input type="text" name="business_address" class="form-control" value="{{ old('business_address', $kyc->business_address) }}" maxlength="150" required>
                        <small class="form-text text-muted">e.g., 123 Main Street, City, State, PIN 123456</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="business_phone">Business Phone</label>
                        <input type="text" name="business_phone" class="form-control" value="{{ old('business_phone', $kyc->business_phone) }}" pattern="^[6-9]\d{9}$" required>
                        <small class="form-text text-muted">e.g., 9876543210 (10 digits starting with 6-9)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_email">Business Email</label>
                        <input type="email" name="business_email" class="form-control" value="{{ old('business_email', $kyc->business_email) }}" maxlength="100" required>
                        <small class="form-text text-muted">e.g., contact@abc.com</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="business_website">Business Website</label>
                        <input type="url" name="business_website" class="form-control" value="{{ old('business_website', $kyc->business_website) }}" maxlength="150">
                        <small class="form-text text-muted">e.g., https://www.abc.com</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_pan">Business PAN</label>
                        <input type="text" name="business_pan" class="form-control" value="{{ old('business_pan', $kyc->business_pan) }}" pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>
                        <small class="form-text text-muted">e.g., ABCDE1234F</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="business_gstin">Business GSTIN</label>
                        <input type="text" name="business_gstin" class="form-control" value="{{ old('business_gstin', $kyc->business_gstin) }}" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$">
                        <small class="form-text text-muted">e.g., 22AAAAA0000A1Z5</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bank_account_number">Bank Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $kyc->bank_account_number) }}" pattern="^[A-Za-z0-9]{8,32}$">
                        <small class="form-text text-muted">e.g., 1234567890123456</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bank_ifsc_code">Bank IFSC Code</label>
                        <input type="text" name="bank_ifsc_code" class="form-control" value="{{ old('bank_ifsc_code', $kyc->bank_ifsc_code) }}" pattern="^[A-Z]{4}0[A-Z0-9]{6}$">
                        <small class="form-text text-muted">e.g., HDFC0001234</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $kyc->bank_name) }}" maxlength="100">
                        <small class="form-text text-muted">e.g., HDFC Bank</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bank_branch">Bank Branch</label>
                        <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $kyc->bank_branch) }}" maxlength="100">
                        <small class="form-text text-muted">e.g., Main Branch, City</small>
                    </div>
                </div>
                <hr>
                <h5>Document Uploaded</h5>
                <div class="row">
                @foreach($kyc->docs as $type)
                    <div class="mb-1 col-3">
                        <a href="{{ Storage::url($type->document_path) }}" target="_blank">View {{ ucfirst(str_replace('_', ' ', $type->document_type)) }}</a>
                    </div>
                @endforeach
                </div>
                <hr>
                <h5>Document Uploads</h5>
                <div id="document_uploads" class="row"></div>
                <script>
                function renderDocumentFields() {
                    var businessType = document.getElementById('business_type').value;
                    fetch('/client-kyc/document-types?business_type=' + businessType)
                        .then(response => response.json())
                        .then(function(docTypes) {
                            var container = document.getElementById('document_uploads');
                            container.innerHTML = '';
                            docTypes.forEach(function(type) {
                                var label = type.replace(/_/g, ' ').replace(/\[\]/, ' (Multiple)');
                                var html = '<div class="mb-3 col-6">';
                                html += '<label>' + label.charAt(0).toUpperCase() + label.slice(1) + ' Document</label>';
                                if (type.endsWith('[]')) {
                                    var baseType = type.replace('[]', '');
                                    html += '<div id="' + baseType + '_container" class="d-inline-block">';
                                    html += '<input type="file" name="' + baseType + '[]" class="form-control mb-2" >';
                                    html += '</div>';
                                    html += '<button type="button" class="btn btn-sm btn-info ml-3 mb-1"  onclick="addDocInput(\'' + baseType + '\')">Add</button>';
                                } else {
                                    html += '<input type="file" name="' + type + '" class="form-control" >';
                                }
                                html += '</div>';
                                container.innerHTML += html;
                            });
                        });
                }
                document.getElementById('business_type').addEventListener('change', renderDocumentFields);
                window.addEventListener('DOMContentLoaded', renderDocumentFields);
                function addDocInput(type) {
                    var container = document.getElementById(type + '_container');
                    var input = document.createElement('input');
                    input.type = 'file';
                    input.name = type + '[]';
                    input.className = 'form-control mb-2';
                    input.required = true;
                    container.appendChild(input);
                }
                </script>
                <button type="submit" class="btn btn-primary">Update KYC</button>
            </form>
        </div>
    </div>
</div>
<script>
function addDocInput(type) {
    var container = document.getElementById(type + '_container');
    var input = document.createElement('input');
    input.type = 'file';
    input.name = type + '[]';
    input.className = 'form-control mb-2';
    input.required = true;
    container.appendChild(input);
}
</script>
@endsection
