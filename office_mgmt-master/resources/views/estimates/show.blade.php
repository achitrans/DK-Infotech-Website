@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Estimate {{ $estimate->estimate_number }}
                    <small>({{ ucfirst($estimate->status) }})</small></h4>
                <small class="text-muted">Details captured on {{ $estimate->estimate_date?->format('Y-m-d') ?? 'N/A' }}
                    .</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estimates.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
                <a href="{{ route('estimates.edit', $estimate) }}" class="btn btn-sm btn-outline-primary">Edit
                    estimate</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">

                <div class="col-md-2">
                    <div class="small text-muted">Client</div>
                    <div class="fw-semibold">{{ $estimate->client?->name ?? '-' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Buyer Name</div>
                    <div class="fw-semibold">{{ $estimate->buyer_name }}</div>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Buyer Mobile</div>
                    <div class="fw-semibold">{{ $estimate->buyer_mobile ?? '-' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Estimate date</div>
                    <div class="fw-semibold">{{ $estimate->estimate_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Expiry</div>
                    <div class="fw-semibold">{{ $estimate->expiry_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="col-md-2">
                    <div class="small text-muted">Total</div>
                    <div class="fw-semibold">Rs {{ number_format($estimate->grand_total, 2) }}</div>
                </div>
            </div>

            <div class="table-responsive" style="border-top: 1px solid #e5dada; padding-top: 9px">
                <h5>Items/Products</h5>
                <table class="table table-sm align-middle">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Item</th>
                        <th>HSN</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">GST %</th>
                        <th class="text-end">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($estimate->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? '-' }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->hsn_code ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-end">Rs {{ number_format($item->rate, 2) }}</td>
                            <td class="text-end">Rs {{ number_format($item->discount, 2) }}</td>
                            <td class="text-end">{{ number_format($item->gst_rate, fmod($item->gst_rate, 1) ? 2 : 0) }}
                                %
                            </td>
                            <td class="text-end">Rs {{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row g-2 mt-2 justify-content-end">
                <div class="col-md-6 text-end">
                    <div>Sub total:
                        <span class="fw-semibold">Rs {{ number_format($estimate->sub_total, 2) }}</span>
                    </div>

                    <div>Tax:
                        <span class="fw-semibold">Rs {{ number_format($estimate->total_tax, 2) }}</span>
                    </div>

                    <div>Total:
                        <span class="fs-4 fw-bold">Rs {{ number_format($estimate->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
