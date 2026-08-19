@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quotations</h5>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">New quotation</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Title</th>
                            <th>Expiry</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($quotations as $quotation)
                        <tr>
                            <td>{{ $quotation->date }}</td>
                            <td>{{ $quotation->product->name }}</td>
                            <td>{{ $quotation->name }}</td>
                            <td>{{ $quotation->title }}</td>
                            <td>{{ $quotation->exp_date ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-warning btn-sm">Edit</a>
                                <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-success btn-sm">Print</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No quotations yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $quotations->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection