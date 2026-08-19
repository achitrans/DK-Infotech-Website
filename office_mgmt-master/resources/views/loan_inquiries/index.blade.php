@extends('layouts.app')
@section('title', 'Loan Inquiries')
@section('content')
<div class="container py-4">

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">Filter Loan Inquiries</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('loan-inquiries.index') }}" class="form-row row">
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
                        </div>
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="Mobile" value="{{ request('phone') }}">
                        </div>
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Category</label>
                            <select name="category" class="form-control">
                                <option value="">Category</option>
                                @foreach(\App\Models\LoanInquiry::$categories as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Status</option>
                                @foreach(\App\Models\LoanInquiry::$statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Source</label>
                            <select name="source" class="form-control">
                                <option value="">Source</option>
                                @foreach(\App\Models\LoanInquiry::$sources as $key => $label)
                                    <option value="{{ $key }}" {{ request('source') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2 mb-2 col-md-2">
                            <label>Created BY</label>
                            <select name="user" class="form-control">
                                <option value="">User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 mt-2 text-right">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('loan-inquiries.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Loan Inquiry List</span>
            <a href="{{ route('loan-inquiries.create') }}" class="btn btn-success btn-sm">Add Inquiry</a>
        </div>
        <div class="card-body table-responsive">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Tenure</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->id }}</td>
                        <td>{{ $inquiry->name }}</td>
                        <td>{{ \App\Models\LoanInquiry::$categories[$inquiry->category] ?? $inquiry->category }}</td>
                        <td>{{ $inquiry->type }}</td>
                        <td>{{ number_format($inquiry->amount) }}</td>
                        <td>{{ $inquiry->tenure }} (M)</td>
                        <td>{{ \App\Models\LoanInquiry::$statuses[$inquiry->status] ?? $inquiry->status }}</td>
                        <td>{{ \App\Models\LoanInquiry::$sources[$inquiry->source] ?? $inquiry->source }}</td>
                        <td>{{ $inquiry->user->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('loan-inquiries.show', $inquiry->id) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('loan-inquiries.edit', $inquiry->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('loan-inquiries.destroy', $inquiry->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this inquiry?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No inquiries found.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $inquiries->links() }}
        </div>
    </div>
</div>
@endsection
