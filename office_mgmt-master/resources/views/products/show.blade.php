@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Product Details</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-link btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $product->name }}</dd>

                        <dt class="col-sm-4">SKU</dt>
                        <dd class="col-sm-8">{{ $product->sku ?: '—' }}</dd>

                        <dt class="col-sm-4">HSN code</dt>
                        <dd class="col-sm-8">{{ $product->hsn_code }}</dd>

                        <dt class="col-sm-4">UOM</dt>
                        <dd class="col-sm-8">{{ $product->uom }}</dd>

                        <dt class="col-sm-4">Sales price</dt>
                        <dd class="col-sm-8">{{ number_format($product->sales_price, 2) }}</dd>

                        <dt class="col-sm-4">GST rate</dt>
                        <dd class="col-sm-8">{{ number_format($product->gst_rate, 2) }}%</dd>

                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">{{ $product->is_service ? 'Service' : 'Goods' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection