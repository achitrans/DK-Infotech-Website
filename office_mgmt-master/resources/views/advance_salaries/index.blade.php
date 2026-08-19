@extends('layouts.app')
@section('title', 'Advance Salaries')
@section('content')
<div class="container py-4">
    @php
        $canManageAdvance = auth()->user()->hasAnyRole(['admin', 'accounts', 'branch manager']);
        $statusBadgeClasses = [
            \App\Models\AdvanceSalary::STATUS_PENDING => 'warning',
            \App\Models\AdvanceSalary::STATUS_APPROVED => 'success',
            \App\Models\AdvanceSalary::STATUS_REJECTED => 'danger',
            \App\Models\AdvanceSalary::STATUS_SETTLED => 'info',
        ];
    @endphp
    <div class="d-flex justify-content-between mb-3">
        <h1>Advance Salary Requests</h1>
        <a href="{{ route('advance-salaries.create') }}" class="btn btn-primary">New Advance</a>
    </div>

        <div class="my-3">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-5">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach([\App\Models\AdvanceSalary::STATUS_PENDING => 'Pending', \App\Models\AdvanceSalary::STATUS_APPROVED => 'Approved', \App\Models\AdvanceSalary::STATUS_REJECTED => 'Rejected', \App\Models\AdvanceSalary::STATUS_SETTLED => 'Settled'] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary ">Filter</button>
                </div>
            </form>
        </div>

    <div class="card ">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Employee</th>
                        <th>Amount</th>
                        <th>Term</th>
                        <th>Deduction</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($advances as $advance)
                        <tr>
                            <td>{{ $advance->user->name }}</td>
                            <td>{{ number_format($advance->amount, 2) }}</td>
                            <td>{{ $advance->term_type === \App\Models\AdvanceSalary::TERM_FULL ? 'Full' : 'Fixed amount' }}</td>
                            <td>{{ number_format($advance->deduction_value, 2) }}</td>
                            <td>{{ number_format($advance->outstanding_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $statusBadgeClasses[$advance->status] ?? 'secondary' }}">
                                    {{ ucfirst($advance->status) }}
                                </span>
                            </td>
                            <td>{{ $advance->requested_by ? '#'.$advance->requested_by : 'System' }}</td>
                            <td>
                                @if($canManageAdvance && $advance->status === \App\Models\AdvanceSalary::STATUS_PENDING)
                                    <form method="POST" action="{{ route('advance-salaries.approve', $advance->id) }}" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Approve this advance?')">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('advance-salaries.reject', $advance->id) }}" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Reject this advance?')">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($advances->isEmpty())
                        <tr><td colspan="8" class="text-center">No advance requests found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $advances->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
