@extends('layouts.app')
@section('title', 'Request Advance Salary')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">Request Advance Salary</div>
        <div class="card-body">
            @php
                $canManageAdvance = auth()->user()->hasAnyRole(['admin', 'accounts', 'branch manager']);
            @endphp

            <form method="POST" action="{{ route('advance-salaries.store') }}">
                @csrf
                <div class="row">
                    @if($canManageAdvance)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->department }})</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                        </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Term</label>
                        <select name="term_type" class="form-select" required>
                            <option value="">Select term</option>
                            <option value="{{ \App\Models\AdvanceSalary::TERM_FULL }}" {{ old('term_type') == \App\Models\AdvanceSalary::TERM_FULL ? 'selected' : '' }}>Full debit from next salary</option>
                            <option value="{{ \App\Models\AdvanceSalary::TERM_FIXED }}" {{ old('term_type') == \App\Models\AdvanceSalary::TERM_FIXED ? 'selected' : '' }}>Fixed monthly deduction</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Monthly Deduction (if fixed)</label>
                        <input type="number" step="0.01" name="deduction_value" class="form-control" value="{{ old('deduction_value') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                    </div>
                </div>
                <div class="text-end">
                    <button class="btn btn-success">Save Request</button>
                    <a href="{{ route('advance-salaries.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
