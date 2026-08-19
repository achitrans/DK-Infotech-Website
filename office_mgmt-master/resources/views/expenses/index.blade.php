@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Expenses</h3>
                <p class="mb-0 text-muted">Current Balance: <strong>{{ number_format($currentBalance, 2) }}</strong></p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add Expense</a>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Filter</div>
                <div class="card-body">

                    <form method="GET" class="row g-2 mb-3">
                        <div class="row date-fields-row">
                            <div class="col-auto  date-field">
                                <label>From Date</label>
                                <input type="date" name="from" class="form-control" value="{{ request('from') }}"
                                    placeholder="From">
                            </div>

                            <div class="col-auto  date-field">
                                <label>Start Date</label>
                                <input type="date" name="to" class="form-control" value="{{ request('to') }}"
                                    placeholder="To">
                            </div>
                            <div class="col-auto">
                                <label >All Heads</label>
                                <select name="head_id" class="form-select">
                                    <option value="">-- Select Options --</option>
                                    @foreach ($heads as $h)
                                    <option value="{{ $h->id }}"
                                        {{ request('head_id') == $h->id ? 'selected' : '' }}>
                                        {{ $h->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <label >All Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">-- Select Options --</option>
                                        @foreach (\App\Models\Expense::statusOptions() as $k => $v)
                                        <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>
                                            {{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-3 align-items-end">
                                        <button class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Head</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ $expense->head?->name }}</td>
                                    <td class="text-success">{{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ ucfirst($expense->payment_mode) }}</td>
                                    <td>
                                        @if ($expense->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($expense->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($expense->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $expense->creator?->name }}</td>
                                    <td>
                                        @if (auth()->user()->type == 'admin')
                                            <a href="{{ route('expenses.edit', $expense) }}"
                                                class="btn btn-square btn-s btn-outline-primary light ms-1">Edit</a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                                style="display:inline-block" onsubmit="return confirm('Delete expense?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-square btn-s btn-outline-danger light ms-1">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No expenses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $expenses->links() }}


        </div>
    @endsection
