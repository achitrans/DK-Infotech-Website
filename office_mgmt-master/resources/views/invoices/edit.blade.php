@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Edit invoice</h4>
                <small class="text-muted">Update to adjust items, taxes, or client data.</small>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back to invoices</a>
        </div>
        <div class="card-body">
            @include('invoices._form', [
                'formAction' => route('invoices.update', $invoice),
                'formMethod' => 'PUT',
                'submitLabel' => 'Update invoice',
                'clients' => $clients,
                'statuses' => $statuses,
                'products' => $products,
                'productMeta' => $productMeta,
                'invoice' => $invoice,
                'items' => $items,
                'nextNumber' => null,
            ])
        </div>
    </div>
@endsection