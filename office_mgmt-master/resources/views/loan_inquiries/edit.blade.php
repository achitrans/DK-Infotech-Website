@extends('layouts.app')
@section('title', 'Edit Loan Inquiry')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">Edit Loan Inquiry</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('loan-inquiries.update', $inquiry->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-control" required>
                                @foreach(\App\Models\LoanInquiry::$categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category', $inquiry->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $inquiry->type) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" value="{{ old('amount', $inquiry->amount) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Tenure (in Months) </label>
                            <input type="number" min="3" name="tenure" class="form-control" value="{{ old('tenure', $inquiry->tenure) }}">
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $inquiry->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $inquiry->email) }}">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $inquiry->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" name="gender" class="form-control" value="{{ old('gender', $inquiry->gender) }}">
                        </div>
                        <div class="row date-fields-row">
                        <div class="form-group  date-field">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $inquiry->dob) }}">
                        </div>
                        </div>
                        <div class="form-group">
                            <label>PAN</label>
                            <input type="text" name="pan" class="form-control" value="{{ old('pan', $inquiry->pan) }}">
                        </div>
                        <div class="form-group">
                            <label>Aadhar</label>
                            <input type="text" name="aadhar" class="form-control" value="{{ old('aadhar', $inquiry->aadhar) }}">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $inquiry->city) }}">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $inquiry->state) }}">
                        </div>
                        <div class="form-group">
                            <label>Pin Code</label>
                            <input type="text" name="pin_code" class="form-control" value="{{ old('pin_code', $inquiry->pin_code) }}">
                        </div>
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control">{{ old('remarks', $inquiry->remarks) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Source</label>
                            <select name="source" class="form-control" required>
                                @foreach(\App\Models\LoanInquiry::$sources as $key => $label)
                                    <option value="{{ $key }}" {{ old('source', $inquiry->source) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                @foreach(\App\Models\LoanInquiry::$statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $inquiry->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row date-fields-row">
                        <div class="form-group  date-field">
                            <label>Follow Up Due</label>
                            <input type="date" name="follow_up_due" class="form-control" value="{{ old('follow_up_due', $inquiry->follow_up_due ? $inquiry->follow_up_due->format('Y-m-d') : '') }}">
                        </div>
                        </div>
                        <div class="row date-fields-row">
                        <div class="form-group  date-field">
                            <label>Closed At</label>
                            <input type="date" name="closed_at" class="form-control" value="{{ old('closed_at', $inquiry->closed_at ? $inquiry->closed_at->format('Y-m-d') : '') }}">
                        </div>
                        </div>
                        <div class="form-group">
                            <label>Statement File</label>
                            <input type="file" name="statement_file" class="form-control">
                            @if($inquiry->statement_file)
                                <a href="{{ asset('storage/' . $inquiry->statement_file) }}" target="_blank" class="d-block mt-2">View Current File</a>
                            @endif
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('loan-inquiries.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
