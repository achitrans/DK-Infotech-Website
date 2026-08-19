@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Card 1: Basic & Work Details -->
                    <div class="card mb-4">
                        <div class="card-header">Edit User - {{ $user->name }}</div>
                        <div class="card-body">
                            <span class="text-warning fs-4">Basic Information</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Barcode/RFID</label>
                                    <input type="text" name="barcode_rfid" class="form-control"
                                        value="{{ old('barcode_rfid', $user->barcode_rfid) }}"
                                        placeholder="Enter barcode or RFID">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" name="mobile" class="form-control"
                                        value="{{ old('mobile', $user->mobile) }}" placeholder="Enter mobile number"
                                        required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Type</label>
                                    <select name="type" id="user_type" class="form-control" required>
                                        @foreach (\App\Models\User::$types as $key => $label)
                                            @if ($key !== 'client')
                                                <option value="{{ $key }}"
                                                    {{ old('type', $user->type) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <span class="text-warning fs-4">Department & Work Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Department</label>
                                    <select name="department" id="department_select" class="form-control" required>
                                        @foreach (\App\Models\User::$departments as $key => $label)
                                            @if ($key !== 'client')
                                                <option value="{{ $key }}"
                                                    {{ old('department', $user->department) == $key ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Position</label>
                                    <select name="position" class="form-control">
                                        <option value="">Select Position</option>
                                        @foreach (\App\Models\User::$positions as $label)
                                            <option value="{{ $label }}"
                                                {{ old('position', $user->position) == $label ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Work Location</label>
                                    <select name="work_location" class="form-control" required>
                                        @foreach (\App\Models\User::$workLocations as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('work_location', $user->work_location) == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Tawk Code</label>
                                    <input type="text" id="tawk_code" name="tawk_code" class="form-control"
                                        value="{{ old('tawk_code', $user->tawk_code) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Salary Details Card -->
                    <div class="card mb-4" id="userSalary">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-money-bill-wave mr-1"></i> Salary Details
                        </div>
                        <div class="card-body">
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Basic</label>
                                    <input type="number" step="0.01" name="basic" class="form-control"
                                        value="{{ old('basic', $user->salary->basic ?? 0) }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>HRA</label>
                                    <input type="number" step="0.01" name="hra" class="form-control"
                                        value="{{ old('hra', $user->salary->hra ?? 0) }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Conveyance</label>
                                    <input type="number" step="0.01" name="conveyance" class="form-control"
                                        value="{{ old('conveyance', $user->salary->conveyance ?? 0) }}">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Special Allowance</label>
                                    <input type="number" step="0.01" name="special_allowance"
                                        class="form-control"
                                        value="{{ old('special_allowance', $user->salary->special_allowance ?? 0) }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Medical Allowance</label>
                                    <input type="number" step="0.01" name="medical_allowance"
                                        class="form-control"
                                        value="{{ old('medical_allowance', $user->salary->medical_allowance ?? 0) }}">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Other Allowance</label>
                                    <input type="number" step="0.01" name="other_allowance"
                                        class="form-control"
                                        value="{{ old('other_allowance', $user->salary->other_allowance ?? 0) }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Gross Salary</label>
                                    <input type="number" step="0.01" name="gross_salary" id="gross_salary"
                                        class="form-control"
                                        value="{{ old('gross_salary', $user->salary->gross_salary ?? 0) }}"
                                        required readonly>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>PF</label>
                                    <input type="number" step="0.01" name="pf" id="pf"
                                        class="form-control" value="{{ old('pf', $user->salary->pf ?? 0) }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>ESI</label>
                                    <input type="number" step="0.01" name="esi" id="esi"
                                        class="form-control" value="{{ old('esi', $user->salary->esi ?? 0) }}">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Professional Tax</label>
                                    <input type="number" step="0.01" name="professional_tax"
                                        id="professional_tax" class="form-control"
                                        value="{{ old('professional_tax', $user->salary->professional_tax ?? 0) }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>TDS</label>
                                    <input type="number" step="0.01" name="tds" id="tds"
                                        class="form-control" value="{{ old('tds', $user->salary->tds ?? 0) }}">
                                </div>
                                <div class="row date-fields-row">
                                <div class="form-group my-2 col-md-6 date-field">
                                    <label>Effective From</label>
                                    <input type="date" name="effective_from" class="form-control"
                                        value="{{ old('effective_from', $user->salary->effective_from ?? date('Y-m-d')) }}"
                                        required>
                                </div>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Net Salary</label>
                                    <input type="text" id="net_salary" class="form-control" value=""
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: User KYC Information Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-id-card mr-1"></i> KYC Information
                        </div>
                        <div class="card-body">
                            <span class="text-warning fs-4">Personal KYC Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label>Father's Name</label>
                                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $user->kyc->father_name ?? '') }}" placeholder="Father's Name">
                                </div>
                                <div class="row date-fields-row">
                                <div class="form-group my-2 col-md-3 date-field">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($user->kyc->date_of_birth ?? null)->format('Y-m-d')) }}">
                                </div>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->kyc->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->kyc->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->kyc->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Blood Group</label>
                                    <select name="blood_group" class="form-control">
                                        <option value="">Select Blood Group</option>
                                        @foreach (\App\Models\UserKyc::getBloodGroupOptions() as $bgKey => $bgLabel)
                                            <option value="{{ $bgKey }}" {{ old('blood_group', $user->kyc->blood_group ?? '') == $bgKey ? 'selected' : '' }}>{{ $bgLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Alternate Mobile</label>
                                    <input type="text" name="mobile_number_alt" class="form-control" value="{{ old('mobile_number_alt', $user->kyc->mobile_number_alt ?? '') }}" placeholder="Alternate Mobile">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Address</label>
                                    <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $user->kyc->address_line1 ?? '') }}" placeholder="Address Line 1">
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $user->kyc->city ?? '') }}" placeholder="City">
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>State</label>
                                    <input type="text" name="state" class="form-control" value="{{ old('state', $user->kyc->state ?? '') }}" placeholder="State">
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $user->kyc->postal_code ?? '') }}" placeholder="Postal Code">
                                </div>
                            </div>

                            <hr>
                            <span class="text-warning fs-4">Proof Numbers</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>PAN Number</label>
                                    <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number', $user->kyc->pan_number ?? '') }}" placeholder="PAN Number">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Aadhaar (Last 4 digits)</label>
                                    <input type="text" name="aadhaar_last4" class="form-control" value="{{ old('aadhaar_last4', $user->kyc->aadhaar_last4 ?? '') }}" placeholder="Aadhaar Last 4 digits">
                                </div>
                            </div>

                            <hr>
                            <span class="text-warning fs-4">Bank Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label>Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $user->kyc->bank_name ?? '') }}" placeholder="Bank Name">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Account Number</label>
                                    <input type="text" name="account_no" class="form-control" value="{{ old('account_no', $user->kyc->account_no ?? '') }}" placeholder="Account Number">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $user->kyc->ifsc_code ?? '') }}" placeholder="IFSC Code">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label>Branch</label>
                                    <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $user->kyc->bank_branch ?? '') }}" placeholder="Branch Name">
                                </div>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <hr>
                                <span class="text-warning fs-4">KYC Approval & Remarks</span>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6">
                                        <label>KYC Status</label>
                                        <select name="kyc_status" class="form-control">
                                            @foreach (\App\Models\UserKyc::getKycStatusOptions() as $kStatus => $kLabel)
                                                <option value="{{ $kStatus }}" {{ old('kyc_status', $user->kyc->kyc_status ?? 'pending') == $kStatus ? 'selected' : '' }}>
                                                    {{ $kLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label>Remarks</label>
                                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $user->kyc->remarks ?? '') }}" placeholder="Approval / Rejection remarks">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-success">Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function calculateGrossSalary() {
            let basic = parseFloat(document.querySelector('[name="basic"]').value) || 0;
            let hra = parseFloat(document.querySelector('[name="hra"]').value) || 0;
            let conveyance = parseFloat(document.querySelector('[name="conveyance"]').value) || 0;
            let special = parseFloat(document.querySelector('[name="special_allowance"]').value) || 0;
            let medical = parseFloat(document.querySelector('[name="medical_allowance"]').value) || 0;
            let other = parseFloat(document.querySelector('[name="other_allowance"]').value) || 0;
            let gross = basic + hra + conveyance + special + medical + other;
            document.getElementById('gross_salary').value = gross.toFixed(2);
            return gross;
        }

        function calculateNetSalary() {
            let gross = calculateGrossSalary();
            let pf = parseFloat(document.getElementById('pf').value) || 0;
            let esi = parseFloat(document.getElementById('esi').value) || 0;
            let professional_tax = parseFloat(document.getElementById('professional_tax').value) || 0;
            let tds = parseFloat(document.getElementById('tds').value) || 0;
            let net = gross - pf - esi - professional_tax - tds;
            document.getElementById('net_salary').value = net.toFixed(2);
        }

        function handleTypeChange() {
            var typeSelect = document.getElementById('user_type');
            var departmentSelect = document.getElementById('department_select');
            var userSalaryDiv = document.getElementById('userSalary');
            if (typeSelect.value === 'client') {
                departmentSelect.value = 'client';
                userSalaryDiv.style.display = 'none';
            } else {
                userSalaryDiv.style.display = '';
            }
        }
        document.getElementById('user_type').addEventListener('change', handleTypeChange);
        document.querySelectorAll(
            '[name="basic"], [name="hra"], [name="conveyance"], [name="special_allowance"], [name="medical_allowance"], [name="other_allowance"], #pf, #esi, #professional_tax, #tds'
        ).forEach(function(el) {
            el.addEventListener('input', calculateNetSalary);
        });
        window.addEventListener('DOMContentLoaded', function() {
            calculateNetSalary();
            handleTypeChange();
        });
    </script>
@endsection
