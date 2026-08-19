@extends('layouts.app')
@section('title', 'My Leaves')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            My Leaves
            <a href="{{ route('leaves.create') }}" class="btn btn-success btn-sm float-right">Apply Leave</a>
        </div>
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Days</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $leave->from_date->toDateString() }}</td>
                            <td>{{ $leave->to_date->toDateString() }}</td>
                            <td>{{ $leave->from_date->diffInDays($leave->to_date) + 1 }}</td>
                            <td>{{ ucfirst($leave->leave_type) }}</td>
                            <td>
                                @if($leave->status === 'approved')
                                    <span class="text-success">{{ ucfirst($leave->status) }}</span>
                                @elseif($leave->status === 'rejected')
                                    <span class="text-danger">{{ ucfirst($leave->status) }}</span>
                                @else
                                    {{ ucfirst($leave->status) }}
                                @endif
                            </td>
                            <td>{{ $leave->reason }}</td>
                            <td>{{ $leave->remarks }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No leaves found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $leaves->links() }}
        </div>
    </div>
</div>
@endsection
