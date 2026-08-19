@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Create invoice</h4>
                <small class="text-muted">Capture a new invoice for a customer or client.</small>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back to invoices</a>
        </div>
        <div class="card-body">
            @include('invoices._form', [
                'formAction' => route('invoices.store'),
                'formMethod' => 'POST',
                'submitLabel' => 'Save invoice',
                'clients' => $clients,
                'statuses' => $statuses,
                'products' => $products,
                'productMeta' => $productMeta,
                'nextNumber' => $nextNumber,
            ])
        </div>
    </div>
@endsection