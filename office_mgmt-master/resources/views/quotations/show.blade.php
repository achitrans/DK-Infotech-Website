@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quotation details</h5>
            <div>
                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-link btn-sm">Edit</a>
                <a href="{{ route('quotations.index') }}" class="btn btn-link btn-sm">Back</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $quotation->title }}</dd>

                <dt class="col-sm-3">Customer</dt>
                <dd class="col-sm-9">{{ $quotation->name }}</dd>

                <dt class="col-sm-3">Product</dt>
                <dd class="col-sm-9">{{ $quotation->product->name }}</dd>

                <dt class="col-sm-3">Dates</dt>
                <dd class="col-sm-9">{{ $quotation->date }} &mdash; {{ $quotation->exp_date ?? 'No expiry' }}</dd>
            </dl>

            <div class="mb-4">
                <h6>Introduction</h6>
                <div class="border p-3 bg-white">{!! $quotation->intro ?? '<em>No introduction yet.</em>' !!}</div>
            </div>

            <div class="mb-4">
                <h6>Description</h6>
                <div class="border p-3 bg-white">{!! $quotation->description ?? '<em>No description yet.</em>' !!}</div>
            </div>

            <div class="mb-4">
                <h6>Terms</h6>
                <div class="border p-3 bg-white">{!! $quotation->terms ?? '<em>No terms yet.</em>' !!}</div>
            </div>
        </div>
    </div>
</div>
@endsection