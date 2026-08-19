@extends('layouts.app')

@section('title', 'Wallet Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Wallet Management</h4>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('wallet.index', ['status' => 'all']) }}" class="btn btn-outline-info {{ $status == 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route('wallet.index', ['status' => 'active']) }}" class="btn btn-outline-success {{ $status == 'active' ? 'active' : '' }}">Active</a>
                        <a href="{{ route('wallet.index', ['status' => 'inactive']) }}" class="btn btn-outline-warning {{ $status == 'inactive' ? 'active' : '' }}">Inactive</a>
                        <a href="{{ route('wallet.index', ['status' => 'suspended']) }}" class="btn btn-outline-danger {{ $status == 'suspended' ? 'active' : '' }}">Suspended</a>
                    </div>
                </div>
                <div class="card-body">

                    <form action="{{ route('wallet.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Select User</label>
                                    <select name="user_id" id="user_id" class="form-control select2" required>
                                        <option value="">Choose User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="credit">Credit</option>
                                        <option value="debit">Debit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference">Reference (Optional)</label>
                                    <input type="text" name="reference" id="reference" class="form-control">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Transaction</button>
                        <a href="{{ route('wallet.transactions') }}" class="btn btn-secondary">View Transactions</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'Search for a user...',
        allowClear: true
    });
});
</script>
@endpush
@endsection
