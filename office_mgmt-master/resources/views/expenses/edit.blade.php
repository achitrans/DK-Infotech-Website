@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Edit Expense</h3>
        <div class="card">
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <span class="text-warning fs-4">Expense Info</span>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expense Head</label>
                            <select name="expense_head_id" class="form-select" required>
                                <option value="">-- Select Head --</option>
                                @foreach ($heads as $h)
                                    <option value="{{ $h->id }}"
                                        {{ old('expense_head_id', $expense->expense_head_id) == $h->id ? 'selected' : '' }}>
                                        {{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $expense->title) }}" required>
                        </div>
                    </div>
                    <div class="row date-fields-row">

                        <div class="col-md-6 mb-3  date-field">
                            <label class="form-label">Date</label>
                            <input type="date" name="expense_date" class="form-control"
                                value="{{ old('expense_date', $expense->expense_date->toDateString()) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control"
                                value="{{ old('amount', $expense->amount) }}" required>
                        </div>
                    </div>

                    <hr>

                    <span class="text-warning fs-4">Payment Details</span>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                @foreach (['cash', 'bank_transfer', 'card', 'cheque', 'upi', 'other'] as $mode)
                                    <option value="{{ $mode }}"
                                        {{ old('payment_mode', $expense->payment_mode) == $mode ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $mode)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control"
                                value="{{ old('reference_number', $expense->reference_number) }}">
                        </div>
                    </div>

                    <hr>

                    <span class="text-warning fs-4">Additional Info</span>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                        </div>

                        @if ($expense->receipt_path)
                            <div class="col-md-6 mb-3">
                                <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"
                                    class="btn btn-outline-primary">View Current Receipt</a>
                            </div>
                        @endif
                    </div>

                    <hr>
                    <span class="text-warning fs-4">Approval Info</span>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Replace Receipt (jpg,png,pdf)</label>
                            <input type="file" name="receipt" class="form-control">
                        </div>

                        @if (auth()->user() && auth()->user()->type === 'admin')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    @foreach (\App\Models\Expense::statusOptions() as $k => $v)
                                        <option value="{{ $k }}"
                                            {{ old('status', $expense->status) == $k ? 'selected' : '' }}>
                                            {{ $v }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                </form>

            </div>
        </div>

    </div>
@endsection
