@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Edit Product</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-link btn-sm">Back</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('products.update', $product) }}">
                            @csrf
                            @method('PUT')

                            <span class="text-warning fs-4">Product Info </span>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input id="name" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="sku">SKU</label>
                                    <input id="sku" class="form-control @error('sku') is-invalid @enderror"
                                        name="sku" value="{{ old('sku', $product->sku) }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="hsn_code">HSN</label>
                                    <input id="hsn_code" class="form-control @error('hsn_code') is-invalid @enderror"
                                        name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}" required>
                                    @error('hsn_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="uom">UOM</label>
                                    <select id="uom" name="uom"
                                        class="form-select @error('uom') is-invalid @enderror" required>
                                        @foreach ($uomOptions as $option)
                                            <option value="{{ $option }}"
                                                {{ old('uom', $product->uom) === $option ? 'selected' : '' }}>
                                                {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @error('uom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <span class="text-warning fs-4">Pricing Info </span>
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="sales_price">Sales price</label>
                                    <input id="sales_price" type="number" step="0.01"
                                        class="form-control @error('sales_price') is-invalid @enderror" name="sales_price"
                                        value="{{ old('sales_price', $product->sales_price) }}" required>
                                    @error('sales_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="gst_rate">GST rate (%)</label>
                                    <select id="gst_rate" name="gst_rate"
                                        class="form-select @error('gst_rate') is-invalid @enderror" required>
                                        @foreach ($gstOptions as $rate)
                                            <option value="{{ $rate }}"
                                                {{ (float) old('gst_rate', $product->gst_rate) === (float) $rate ? 'selected' : '' }}>
                                                {{ number_format($rate, is_float($rate) && intval($rate) !== $rate ? 2 : 0) }}%
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('gst_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <span class="text-warning fs-4">Product Details</span>
                            <div class="row">

                                <div class="form-check col-md-6 mb-3">
                                    <input id="is_service" type="checkbox" class="form-check-input" name="is_service"
                                        value="1" {{ old('is_service', $product->is_service) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_service">Is service?</label>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                        name="description">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr>

                            <span class="text-warning fs-4">Rich Description</span>
                            <div class="mb-3">
                                <label class="form-label" for="html_description">Rich description (HTML)</label>
                                <textarea id="html_description" class="form-control wysiwyg @error('html_description') is-invalid @enderror"
                                    rows="4" name="html_description">{{ old('html_description', $product->html_description) }}</textarea>
                                @error('html_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
