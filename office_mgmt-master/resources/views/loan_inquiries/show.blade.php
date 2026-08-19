@extends('layouts.app')
@section('title', 'Loan Inquiry Details')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">Loan Inquiry Details</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>ID</th><td>{{ $inquiry->id }}</td></tr>
                        <tr><th>Name</th><td>{{ $inquiry->name }}</td></tr>
                        <tr><th>Category</th><td>{{ \App\Models\LoanInquiry::$categories[$inquiry->category] ?? $inquiry->category }}</td></tr>
                        <tr><th>Type</th><td>{{ $inquiry->type }}</td></tr>
                        <tr><th>Amount</th><td>{{ number_format($inquiry->amount) }}</td></tr>
                        <tr><th>Tenure</th><td>{{ $inquiry->tenure }}</td></tr>
                        <tr><th>Status</th><td>{{ \App\Models\LoanInquiry::$statuses[$inquiry->status] ?? $inquiry->status }}</td></tr>
                        <tr><th>Source</th><td>{{ \App\Models\LoanInquiry::$sources[$inquiry->source] ?? $inquiry->source }}</td></tr>
                        <tr><th>Email</th><td>{{ $inquiry->email }}</td></tr>
                        <tr><th>Phone</th><td>{{ $inquiry->phone }}</td></tr>
                        <tr><th>Gender</th><td>{{ $inquiry->gender }}</td></tr>
                        <tr><th>Date of Birth</th><td>{{ $inquiry->dob }}</td></tr>
                        <tr><th>PAN</th><td>{{ $inquiry->pan }}</td></tr>
                        <tr><th>Aadhar</th><td>{{ $inquiry->aadhar }}</td></tr>
                        <tr><th>City</th><td>{{ $inquiry->city }}</td></tr>
                        <tr><th>State</th><td>{{ $inquiry->state }}</td></tr>
                        <tr><th>Remarks</th><td>{{ $inquiry->remarks }}</td></tr>
                        <tr><th>Follow Up Due</th><td>{{ $inquiry->follow_up_due ? $inquiry->follow_up_due->format('Y-m-d') : '-' }}</td></tr>
                        <tr><th>Closed At</th><td>{{ $inquiry->closed_at ? $inquiry->closed_at->format('Y-m-d') : '-' }}</td></tr>
                        <tr><th>Statement File</th><td>@if($inquiry->statement_file)<a href="{{ asset('storage/' . $inquiry->statement_file) }}" target="_blank">Download/View</a>@else - @endif</td></tr>
                        <tr><th>Created BY</th><td>{{ $inquiry->user->name ?? '-' }}</td></tr>
                        <tr><th>Pin Code</th><td>{{ $inquiry->pin_code }}</td></tr>
                    </table>
                    <div class="text-center">
                        <a href="{{ route('loan-inquiries.edit', $inquiry->id) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('loan-inquiries.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $logs = $inquiry->statusLogs ?? [];
    @endphp
    @if(count($logs)>0)
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-secondary text-white">Status Change Logs</div>
                    <div class="card-body table-responsive">

                        @if($logs && count($logs))
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Changed By</th>
                                    <th>Old Status</th>
                                    <th>New Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $log->user->name ?? '-' }}</td>
                                        <td>{{ \App\Models\LoanInquiry::$statuses[$log->status_old] ?? $log->status_old }}</td>
                                        <td>{{ \App\Models\LoanInquiry::$statuses[$log->status_new] ?? $log->status_new }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-muted">No status change logs found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
