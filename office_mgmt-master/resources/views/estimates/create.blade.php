@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Create Estimate</h4>
                <small class="text-muted">Capture the pricing and item breakdown before sending to the client.</small>
            </div>
            <a href="{{ route('estimates.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
        </div>
        <div class="card-body">
            @include('estimates._form', [
                'formAction' => route('estimates.store'),
                'formMethod' => 'POST',
                'submitLabel' => 'Save estimate',
                'nextNumber' => $nextNumber,
                'defaultExpiry' => $defaultExpiry,
                'items' => [],
                'estimate' => null,
            ])
        </div>
    </div>
@endsection
