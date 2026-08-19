@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Expense Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $expense->title }}</h5>
            <p class="card-text"><strong>Date:</strong> {{ $expense->expense_date->format('Y-m-d') }}</p>
            <p class="card-text"><strong>Head:</strong> {{ $expense->head?->name }}</p>
            <p class="card-text"><strong>Amount:</strong> {{ number_format($expense->amount,2) }}</p>
            <p class="card-text"><strong>Payment Mode:</strong> {{ ucfirst($expense->payment_mode) }}</p>
            <p class="card-text"><strong>Status:</strong> {{ ucfirst($expense->status) }}</p>
            <p class="card-text"><strong>Reference:</strong> {{ $expense->reference_number }}</p>
            <p class="card-text"><strong>Notes:</strong><br>{{ $expense->notes }}</p>
            <p class="card-text"><strong>Created By:</strong> {{ $expense->creator?->name }}</p>
            @if($expense->approver)
                <p class="card-text"><strong>Approved By:</strong> {{ $expense->approver->name }}</p>
            @endif

            @if($expense->receipt_path)
                <p class="card-text">
                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="btn btn-outline-primary">View Receipt</a>
                </p>
            @endif

            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
            @can('update', $expense)
                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">Edit</a>
            @endcan
        </div>
    </div>
</div>
@endsection
