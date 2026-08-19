@extends('layouts.app')
@section('title', 'User KYC')
@section('content')
    <div class="container-fluid">
        <h1>KYC Details for {{ $kyc->user->name }}</h1>
        <div class="card mb-3">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $kyc->kyc_status == 'approved' ? 'success' : ($kyc->kyc_status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($kyc->kyc_status) }}
                                </span>
                                @if ($kyc->kyc_status == 'rejected' && auth()->user()->isEmployee())
                                    <a href="{{ route('user-kyc.edit', $kyc->id) }}" class="btn btn-warning btn-sm float-right">Update Kyc</a>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Full Name</th><td>{{ $kyc->full_name }}</td></tr>
                        <tr><th>Date of Birth</th><td>{{ $kyc->date_of_birth }}</td></tr>
                        <tr><th>Gender</th><td>{{ $kyc->gender }}</td></tr>
                        <tr><th>Father's Name</th><td>{{ $kyc->father_name }}</td></tr>
                        <tr><th>Father's Mobile</th><td>{{ $kyc->father_mobile }}</td></tr>
                        <tr><th>Father's Aadhar</th><td>{{ $kyc->father_aadhar }}</td></tr>
                        <tr><th>Blood Group</th><td>{{ $kyc->blood_group }}</td></tr>
                        <tr><th>Mobile Number</th><td>{{ $kyc->mobile_number }}</td></tr>
                        <tr><th>Email</th><td>{{ $kyc->email }}</td></tr>
                        <tr><th>Address</th><td>{{ $kyc->address_line1 }}, {{ $kyc->address_line2 }}, {{ $kyc->city }}, {{ $kyc->state }}, {{ $kyc->country }} - {{ $kyc->postal_code }}</td></tr>
                        <tr><th>Address Proof</th><td>{{ $kyc->address_proof_type }} ({{ $kyc->address_proof_number }}) <a href="{{ Storage::url($kyc->address_proof_doc_path) }}" target="_blank">View Doc</a></td></tr>
                        <tr><th>ID Proof</th><td>{{ $kyc->id_proof_type }} ({{ $kyc->id_proof_number }}) <a href="{{ Storage::url($kyc->id_proof_doc_path) }}" target="_blank">View Doc</a></td></tr>
                        <tr><th>PAN</th><td>{{ $kyc->pan_number }}</td></tr>
                        <tr><th>Aadhaar Last 4</th><td>{{ $kyc->aadhaar_last4 }}</td></tr>
                        <tr><th>Bank Account No</th><td>{{ $kyc->account_no }}</td></tr>
                        <tr><th>IFSC Code</th><td>{{ $kyc->ifsc_code }}</td></tr>
                        <tr><th>Bank Name</th><td>{{ $kyc->bank_name }}</td></tr>
                        <tr><th>Bank Branch</th><td>{{ $kyc->bank_branch }}</td></tr>
                        <tr><th>Bank Statement/Passbook</th><td><a href="{{ Storage::url($kyc->bank_doc) }}" target="_blank">View Doc</a></td></tr>
                        <tr><th>Photo</th><td><a href="{{ Storage::url($kyc->photograph_path) }}" target="_blank"><img src="{{ Storage::url($kyc->photograph_path) }}" alt="" width="160"></a></td></tr>

                        <tr><th>Experience Letter</th><td><a href="{{ Storage::url($kyc->past_experience_letter) }}" target="_blank">View Doc</a></td></tr>
                        <tr><th>Offer Letter</th><td><a href="{{ Storage::url($kyc->past_offer_letter) }}" target="_blank">View Doc</a></td></tr>
                        <tr><th>Salary Slip</th><td><a href="{{ Storage::url($kyc->past_salary_slip) }}" target="_blank">View Doc</a></td></tr>

                        <tr><th class="text-danger">Remarks</th><td class="text-danger">{{ $kyc->remarks }}</td></tr>
                        <tr>
                            <th>Qualifications</th>
                            <td>
                                @if(is_array($kyc->qualifications) && count($kyc->qualifications))
                                    <div class="mt-2"><strong>Qualifications:</strong>
                                        <ul class="small mb-0">
                                            @foreach($kyc->qualifications as $q)
                                                @php
                                                    $degree = is_array($q) ? ($q['degree'] ?? '') : (is_string($q) ? $q : '');
                                                    $file = is_array($q) ? ($q['file'] ?? '') : '';
                                                    $board = is_array($q) ? ($q['board'] ?? '') : '';
                                                    $college = is_array($q) ? ($q['college'] ?? '') : '';
                                                    $grade = is_array($q) ? ($q['grade'] ?? '') : '';
                                                @endphp
                                                <li>{{ $degree }} @if($board) • {{ $board }}@endif @if($college) • {{ $college }}@endif @if($grade) • Grade: {{ $grade }}@endif
                                                    @if($file) • <a href="{{ Storage::url($file) }}" target="_blank"> View Doc</a> @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="mt-2"><strong>Qualifications:</strong> <span class="small">-</span></div>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Accepted Terms & Conditions:</th><td>Yes At  {{ $kyc->created_at}}
                            @if ($kyc->user->department == 'intern')
                                <a href="{{ route('pages.tnc.internship')}}">Click to View</a>
                            @else
                                <a href="{{ route('pages.tnc.employee')}}">Click to View</a>
                            @endif
                        </td></tr>
                    </tbody>
                </table>

            </div>
        </div>
        @if (auth()->user()->isAdmin())
            <div class="card mb-3">
                <div class="card-header">Update KYC Status</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user-kyc.updateStatus', $kyc->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="kyc_status">KYC Status</label>
                            <select name="kyc_status" id="kyc_status" class="form-control">
                                @foreach (\App\Models\UserKyc::getKycStatusOptions() as $key => $label)
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
    </div>
@endsection
