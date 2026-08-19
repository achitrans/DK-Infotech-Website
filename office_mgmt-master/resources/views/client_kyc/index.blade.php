@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Client KYC List</h2>
    <div class=" table-responsive">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Client Name</th>
                <th>Business Type</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kycs as $kyc)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $kyc->client_name }}</td>
                <td>{{ $kyc->business_type }}</td>
                <td><span class="badge bg-{{ $kyc->kyc_status == 'approved' ? 'success' : ($kyc->kyc_status == 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($kyc->kyc_status) }}</span></td>
                <td>{{ $kyc->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    <a href="{{ route('client-kyc.show', Crypt::encrypt($kyc->id)) }}" class="btn btn-info btn-sm">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">No KYC records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
