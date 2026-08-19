@extends('layouts.app')

@section('title', 'Wallet Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Wallet Transactions</h4>
                    <a href="{{ route('wallet.index') }}" class="btn btn-primary btn-sm">Add Transaction</a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="user_id" class="form-control select2">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Reference</th>
                                    <th>Balance Before</th>
                                    <th>Balance After</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ $transaction->reference }}</td>
                                        <td>{{ number_format($transaction->balance_before, 2) }}</td>
                                        <td>{{ number_format($transaction->balance_after, 2) }}</td>
                                        <td>{{ $transaction->transaction_date->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'Select user to filter...',
        allowClear: true
    });
});
</script>
@endpush
@endsection