@extends('layouts.app')

@section('content')
    @php
        $publicLink = route('invoices.public', $invoice->public_token);
        $totalTax = $invoice->total_cgst + $invoice->total_sgst + $invoice->total_igst;
    @endphp

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h4 class="card-title mb-1">Invoice {{ $invoice->invoice_number }}</h4>
                <p class="mb-0 text-muted">Issued {{ $invoice->invoice_date?->format('Y-m-d') ?? '—' }} · Status
                    {{ ucfirst($invoice->status) }}
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                <a href="{{ $publicLink }}" target="_blank" class="btn btn-sm btn-outline-primary">Download PDF</a>
                @if(!Auth::user()->isClient())
                <a href="{{ $publicLink }}" target="_blank" class="btn btn-sm btn-outline-success">Share link</a>
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-info">Edit</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted mb-2">Bill to</h6>
                    <p class="mb-0 fw-semibold">{{ $invoice->buyer_name }}</p>
                    <p class="mb-0">{{ $invoice->billing_address ?? 'Billing address not set' }}</p>
                    <p class="mb-0">Mobile: {{ $invoice->buyer_mobile ?? '—' }}</p>
                    <p class="mb-0">GSTIN: {{ $invoice->buyer_gstin ?? '-' }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-2">
                        <strong>Invoice date</strong><br>
                        {{ $invoice->invoice_date?->format('Y-m-d') ?? '—' }}
                    </div>
                    <div class="mb-2">
                        <strong>Due date</strong><br>
                        {{ $invoice->due_date?->format('Y-m-d') ?? '—' }}
                    </div>
                    <div>
                        <strong>Status</strong><br>
                        <span class="badge bg-light text-dark">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>GST</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->item_name }}</strong><br>
                                    <small class="text-muted">HSN {{ $item->hsn_code ?? '—' }}</small>
                                </td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>₹ {{ number_format($item->rate, 2) }}</td>
                                <td>₹ {{ number_format($item->discount, 2) }}</td>
                                <td>{{ number_format($item->gst_rate, fmod($item->gst_rate, 1) ? 2 : 0) }}%</td>
                                <td>₹ {{ number_format($item->cgst_amount, 2) }}</td>
                                <td>₹ {{ number_format($item->sgst_amount, 2) }}</td>
                                <td>₹ {{ number_format($item->igst_amount, 2) }}</td>
                                <td class="text-end">₹ {{ number_format($item->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted">Notes</h6>
                    <p>{{ $invoice->notes ?? 'No additional notes.' }}</p>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Subtotal</span>
                            <strong>₹ {{ number_format($invoice->sub_total, 2) }}</strong>
                        </div>
                        @if($invoice->total_cgst > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>CGST</span>
                                <strong>₹ {{ number_format($invoice->total_cgst, 2) }}</strong>
                            </div>
                        @endif
                        @if($invoice->total_sgst > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>SGST</span>
                                <strong>₹ {{ number_format($invoice->total_sgst, 2) }}</strong>
                            </div>
                        @endif
                        @if($invoice->total_igst > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>IGST</span>
                                <strong>₹ {{ number_format($invoice->total_igst, 2) }}</strong>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="fw-semibold">Grand total</span>
                            <strong>₹ {{ number_format($invoice->grand_total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 small text-muted">
                Secure public invoice link : <br>
                <a href="{{ $publicLink }}" target="_blank">{{ $publicLink }}</a>
            </div>
        </div>
    </div>

    <!-- Payments Section -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payments History</h5>
            @if(!Auth::user()->isClient())
            @if($invoice->amount_due > 0)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#paymentForm">
                    Add Payment
                </button>
            @endif
            @endif
        </div>
        <div class="card-body">
            @if($invoice->invoicePayments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mode</th>
                                <th>Reference</th>
                                <th>Comment</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->invoicePayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>{{ $payment->payment_mode }}</td>
                                    <td>{{ $payment->reference_no ?? '-' }}</td>
                                    <td>{{ $payment->comment ?? '-' }}</td>
                                    <td class="text-end">₹ {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">Total Paid</td>
                                <td class="text-end fw-bold">₹ {{ number_format($invoice->total_paid, 2) }}</td>
                            </tr>
                            <tr class="table-info">
                                <td colspan="4" class="text-end fw-bold">Remaining Due</td>
                                <td class="text-end fw-bold">₹ {{ number_format($invoice->amount_due, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No payments recorded yet.</p>
            @endif

            <!-- Add Payment Form -->
            <div class="collapse mt-4" id="paymentForm">
                <div class="border rounded p-3 bg-light">
                    <h6>Record New Payment</h6>
                    <form action="{{ route('invoices.payments.store', $invoice) }}" method="POST">
                        @csrf
                        <div class="row date-fields-row g-3">
                            <div class="col-md-3  date-field">
                                <label class="form-label">Date</label>
                                <input type="date" name="payment_date" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mode</label>
                                <select name="payment_mode" class="form-select form-select-sm" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="amount" class="form-control" step="0.01"
                                        max="{{ $invoice->amount_due }}" value="{{ $invoice->amount_due }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ref No / Trans ID</label>
                                <input type="text" name="reference_no" class="form-control form-control-sm"
                                    placeholder="Optional">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Comment</label>
                                <textarea name="comment" class="form-control form-control-sm" rows="2"
                                    placeholder="Optional notes"></textarea>
                            </div>
                            <div class="col-12" id="paymentFormDate">
                                <button type="submit" class="btn btn-sm btn-success">Save Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('pay')) {
                const paymentForm = document.getElementById('paymentForm');
                if (paymentForm) {
                    const bsCollapse = new bootstrap.Collapse(paymentForm, {
                        show: true
                    });
                    const paymentFormDate = document.getElementById('paymentFormDate');
                    paymentFormDate.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
@endsection
