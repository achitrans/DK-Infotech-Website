@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Edit Estimate</h4>
                <small class="text-muted">Adjust the line items or buyer details before resending.</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estimates.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
                <a href="{{ route('estimates.show', $estimate) }}" class="btn btn-sm btn-outline-primary">View estimate</a>
            </div>
        </div>
        <div class="card-body">
            @include('estimates._form', [
                'formAction' => route('estimates.update', $estimate),
                'formMethod' => 'PUT',
                'submitLabel' => 'Update estimate',
                'estimate' => $estimate,
                'items' => $items,
                'nextNumber' => null,
            ])
        </div>
    </div>
@endsection
