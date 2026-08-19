@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Products</h5>
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">Add product</a>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 mb-3" method="GET" action="{{ route('products.index') }}">
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Search name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" value="{{ $filters['sku'] ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Search SKU">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">HSN</label>
                                <input type="text" name="hsn_code" value="{{ $filters['hsn_code'] ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Search HSN">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">GST rate</label>
                                <select name="gst_rate" class="form-select form-select-sm">
                                    <option value="">Any</option>
                                    @foreach (App\Models\Product::gstRateOptions() as $rate)
                                        <option value="{{ $rate }}"
                                            {{ isset($filters['gst_rate']) && (float) $filters['gst_rate'] === (float) $rate ? 'selected' : '' }}>
                                            {{ number_format($rate, is_float($rate) && intval($rate) !== $rate ? 2 : 0) }}%
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button class="btn btn-dark btn-sm w-100" type="submit">Filter</button>
                                <a href="{{ route('products.index') }}"
                                    class="btn btn-outline-info btn-sm w-100">Reset</a>
                            </div>
                        </form>

                        @if (array_filter($filters ?? []))
                            <div class="alert alert-info py-2" role="alert">
                                Showing {{ $products->count() }} result(s) for the current filters.
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">

                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>HSN</th>
                                        <th>UOM</th>
                                        <th class="text-end">Price</th>
                                        <th>GST%</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->sku ?? '-' }}</td>
                                            <td>{{ $product->hsn_code }}</td>
                                            <td>{{ $product->uom }}</td>
                                            <td class="text-end">{{ number_format($product->sales_price, 2) }}</td>
                                            <td>{{ number_format($product->gst_rate, 2) }}%</td>
                                            <td>{{ $product->is_service ? 'Service' : 'Goods' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('products.edit', $product) }}"
                                                    class="btn btn-outline-primary btn-sm">Edit</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Remove this product?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No products yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
