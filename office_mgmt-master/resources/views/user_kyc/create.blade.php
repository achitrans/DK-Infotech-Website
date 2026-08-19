@extends('layouts.app')
@section('title', 'Submit KYC')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center pt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-info fs-4">Complete your KYC verification to enjoy a secure and seamless
                        experience.
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user-kyc.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ old('full_name', $kyc->full_name ?? '') }}" required>
                                </div>

                                <div class="row date-fields-row">
                                <div class="form-group my-2 col-md-3 date-field">
                                    <label class="mb-1">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth', $kyc->date_of_birth ?? '') }}" required>
                                </div>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-control" required>
                                        <option value="male"
                                            {{ old('gender', $kyc->gender ?? '') == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female"
                                            {{ old('gender', $kyc->gender ?? '') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="other"
                                            {{ old('gender', $kyc->gender ?? '') == 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" class="form-control"
                                        value="{{ old('mobile_number', $kyc->mobile_number ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Alternate Mobile Number</label>
                                    <input type="text" name="mobile_number_alt" class="form-control"
                                        value="{{ old('mobile_number_alt', $kyc->mobile_number_alt ?? '') }}">
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Blood Group <span class="text-danger">*</span></label>
                                    <select name="blood_group" class="form-control" required>
                                        @foreach (\App\Models\UserKyc::getBloodGroupOptions() as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('blood_group', $kyc->blood_group ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <hr>
                            <span class="text-warning fs-4">Family Details</span>


                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Father's Name <span class="text-danger">*</span></label>
                                    <input type="text" name="father_name" class="form-control"
                                        value="{{ old('father_name', $kyc->father_name ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Father's Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" name="father_mobile" class="form-control"
                                        value="{{ old('father_mobile', $kyc->father_mobile ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Father's Aadhar (PDF/Image) <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="father_aadhar" class="form-control"
                                        value="{{ old('father_aadhar', $kyc->father_aadhar ?? '') }}" required>
                                </div>
                            </div>

                            <hr>
                            <span class="text-warning fs-4">Address Details</span>

                            <div class="form-row d-none">
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ auth()->user()->email }}" required tabindex="-1">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" name="address_line1" class="form-control"
                                        value="{{ old('address_line1', $kyc->address_line1 ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Address Line 2</label>
                                    <input type="text" name="address_line2" class="form-control"
                                        value="{{ old('address_line2', $kyc->address_line2 ?? '') }}">
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control"
                                        value="{{ old('city', $kyc->city ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">State <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control"
                                        value="{{ old('state', $kyc->state ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Country <span class="text-danger">*</span></label>
                                    <input type="text" name="country" class="form-control"
                                        value="{{ old('country', $kyc->country ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" class="form-control"
                                        value="{{ old('postal_code', $kyc->postal_code ?? '') }}" required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Address Proof Type <span class="text-danger">*</span></label>
                                    <select name="address_proof_type" class="form-control" required>
                                        @foreach (\App\Models\UserKyc::getAddressProofType() as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('address_proof_type', $kyc->address_proof_type ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Address Proof Number <span class="text-danger">*</span></label>
                                    <input type="text" name="address_proof_number" class="form-control"
                                        value="{{ old('address_proof_number', $kyc->address_proof_number ?? '') }}"
                                        required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Address Proof Document (PDF/Image) <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="address_proof_doc_path" class="form-control"
                                        accept=".pdf,image/*" required>
                                </div>
                            </div>


                            <hr>
                            <span class="text-warning fs-4">Identity Details</span>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">ID Proof Type <span class="text-danger">*</span></label>
                                    <select name="id_proof_type" class="form-control" required>
                                        @foreach (\App\Models\UserKyc::getIdProofTypeOptions() as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('id_proof_type', $kyc->id_proof_type ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">ID Proof Number <span class="text-danger">*</span></label>
                                    <input type="text" name="id_proof_number" class="form-control"
                                        value="{{ old('id_proof_number', $kyc->id_proof_number ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">ID Proof Document (PDF/Image) <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="id_proof_doc_path" class="form-control"
                                        accept=".pdf,image/*" required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">PAN Number <span class="text-danger">*</span></label>
                                    <input type="text" name="pan_number" class="form-control"
                                        value="{{ old('pan_number', $kyc->pan_number ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Aadhaar Last 4 Digits <span class="text-danger">*</span></label>
                                    <input type="text" name="aadhaar_last4" class="form-control"
                                        value="{{ old('aadhaar_last4', $kyc->aadhaar_last4 ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Photograph (Image) <span class="text-danger">*</span></label>
                                    <input type="file" name="photograph_path" class="form-control" accept="image/*"
                                        required>
                                </div>
                            </div>


                            <hr>
                            <span class="text-warning fs-4">Bank Details</span>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="account_no" class="form-control"
                                        value="{{ old('account_no', $kyc->account_no ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-3">
                                    <label class="mb-1">IFSC Code <span class="text-danger">*</span></label>
                                    <input type="text" name="ifsc_code" class="form-control"
                                        value="{{ old('ifsc_code', $kyc->ifsc_code ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Bank Statement/Passbook (PDF/Image) <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="bank_doc" class="form-control" accept=".pdf,image/*"
                                        required>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" class="form-control"
                                        value="{{ old('bank_name', $kyc->bank_name ?? '') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label class="mb-1">Bank Branch <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_branch" class="form-control"
                                        value="{{ old('bank_branch', $kyc->bank_branch ?? '') }}" required>
                                </div>
                            </div>


                            <hr>
                            <span class="text-warning fs-4">Documents</span>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label class="mb-1">Experience Letter</label>
                                    <input type="file" name="past_experience_letter" class="form-control"
                                        value="{{ old('past_experience_letter', $kyc->past_experience_letter ?? '') }}">
                                </div>

                                <div class="form-group my-2 col-md-4">
                                    <label class="mb-1">Offer Letter</label>
                                    <input type="file" name="past_offer_letter" class="form-control"
                                        value="{{ old('past_offer_letter', $kyc->past_offer_letter ?? '') }}">
                                </div>

                                <div class="form-group my-2 col-md-4">
                                    <label class="mb-1">Salary Slip</label>
                                    <input type="file" name="past_salary_slip" class="form-control"
                                        value="{{ old('past_salary_slip', $kyc->past_salary_slip ?? '') }}">
                                </div>

                            </div>
                            <hr>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-12">
                                    <label class="mb-1 text-warning fs-4">Qualifications</label>
                                    @php
                                        $degreeOptions = [
                                            'Matriculation',
                                            'Intermediate',
                                            'Diploma',
                                            'Bachelor',
                                            'Master',
                                            'PhD',
                                        ];
                                    @endphp
                                    <table class="table table-sm" id="qualifications-table">
                                        <thead>
                                            <tr>
                                                <th>Degree</th>
                                                <th>File</th>
                                                <th>Board</th>
                                                <th>College</th>
                                                <th>Grade</th>
                                                <th style="width:1%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <button type="button" id="add-qualification"
                                        class="btn btn-sm btn-outline-primary">Add Qualification</button>
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-12">
                                    <label class="mb-1">Terms and Conditions <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tnc" id="tnc"
                                            required>
                                        <label class="form-check-label" for="tnc">
                                            I agree to the
                                            @if (auth()->user()->department == 'intern')
                                                <a href="{{ route('pages.tnc.internship') }}" target="_blank">Terms and
                                                    Conditions</a>
                                            @else
                                                <a href="{{ route('pages.tnc.employee') }}" target="_blank">Terms and
                                                    Conditions</a>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Submit KYC</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        (function() {
            function esc(s) {
                return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#39;');
            }
            var DEGREE_OPTS = ["Matriculation", "Intermediate", "Diploma", "Bachelor", "Master", "PhD"];

            function buildDegreeSelectHtml(selected) {
                var html =
                    '<select name="qualifications[][degree]" class="form-control form-control-sm degree-select">';
                html += '<option value="">-</option>';
                DEGREE_OPTS.forEach(function(o) {
                    html += '<option value="' + esc(o) + '"' + (selected == o ? ' selected' : '') + '>' + esc(
                        o) + '</option>';
                });
                html += '</select>';
                return html;
            }

            function addRow(data) {
                var tbody = document.querySelector('#qualifications-table tbody');
                var row = document.createElement('tr');
                var td0 = document.createElement('td');
                td0.innerHTML = buildDegreeSelectHtml(data.degree || '');
                row.appendChild(td0);
                var td1 = document.createElement('td');
                td1.innerHTML =
                    '<input type="file" name="qualifications[][file]" class="form-control form-control-sm" value="' +
                    esc(data.file) + '">';
                row.appendChild(td1);
                var td2 = document.createElement('td');
                td2.innerHTML =
                    '<input type="text" name="qualifications[][board]" class="form-control form-control-sm" value="' +
                    esc(data.board) + '">';
                row.appendChild(td2);
                var td3 = document.createElement('td');
                td3.innerHTML =
                    '<input type="text" name="qualifications[][college]" class="form-control form-control-sm" value="' +
                    esc(data.college) + '">';
                row.appendChild(td3);
                var td4 = document.createElement('td');
                td4.innerHTML =
                    '<input type="text" name="qualifications[][grade]" class="form-control form-control-sm" value="' +
                    esc(data.grade) + '">';
                row.appendChild(td4);
                var td5 = document.createElement('td');
                td5.innerHTML = '<button type="button" class="btn btn-sm btn-danger remove-row">×</button>';
                row.appendChild(td5);
                tbody.appendChild(row);
                reindexQualifications();
                refreshDegreeOptions();
            }
            document.getElementById('add-qualification').addEventListener('click', function() {
                addRow({});
            });
            document.querySelector('#qualifications-table').addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-row')) {
                    var tr = e.target.closest('tr');
                    if (tr) tr.remove();
                    reindexQualifications();
                    refreshDegreeOptions();
                }
            });

            function reindexQualifications() {
                var rows = Array.from(document.querySelectorAll('#qualifications-table tbody tr'));
                rows.forEach(function(row, idx) {
                    // degree select
                    var sel = row.querySelector('.degree-select');
                    if (sel) sel.name = 'qualifications[' + idx + '][degree]';
                    // other inputs
                    var file = row.querySelector('input[name*="[file]"]') || row.querySelector(
                        'input[name^="qualifications"]');
                    if (file) file.name = 'qualifications[' + idx + '][file]';
                    var board = row.querySelector('input[name*="[board]"]');
                    if (board) board.name = 'qualifications[' + idx + '][board]';
                    var college = row.querySelector('input[name*="[college]"]');
                    if (college) college.name = 'qualifications[' + idx + '][college]';
                    var grade = row.querySelector('input[name*="[grade]"]');
                    if (grade) grade.name = 'qualifications[' + idx + '][grade]';
                });
            }

            function refreshDegreeOptions() {
                var selects = Array.from(document.querySelectorAll('.degree-select'));
                var selected = selects.map(function(s) {
                    return s.value;
                }).filter(Boolean);
                selects.forEach(function(s) {
                    var current = s.value;
                    Array.from(s.options).forEach(function(opt) {
                        if (!opt.value) {
                            opt.disabled = false;
                            return;
                        }
                        opt.disabled = (opt.value !== current && selected.indexOf(opt.value) !== -1);
                    });
                });
            }
            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('degree-select')) {
                    refreshDegreeOptions();
                }
            });
            // initialize disable state for pre-filled rows
            reindexQualifications();
            refreshDegreeOptions();
        })();
    </script>
@endsection
