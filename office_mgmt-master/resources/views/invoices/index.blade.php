@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">Invoices</h4>
                <small class="text-muted">Track billing and tax summaries for every client.</small>
            </div>
            <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">Create invoice</a>
        </div>
        {{-- Filter bar --}}
        <div class="card-body border-bottom pb-3">
            <form method="GET" action="{{ route('invoices.index') }}" class="row g-2 align-items-end">
                <div class="row date-fields-row">
                <div class="col-sm-3">
                    <label for="filter_status" class="form-label small mb-1">Status</label>
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach(['draft' => 'Draft', 'created' => 'Created', 'sent' => 'Sent', 'paid' => 'Paid', 'overdue' => 'Overdue', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3  date-field">
                    <label for="filter_date_from" class="form-label small mb-1">Date from</label>
                    <input type="date" id="filter_date_from" name="date_from"
                        class="form-control"
                        value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-sm-3  date-field">
                    <label for="filter_date_to" class="form-label small mb-1">Date to</label>
                    <input type="date" id="filter_date_to" name="date_to"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-lg btn-outline-info">Clear</a>
                </div>
            </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Invoice date</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->client?->name ?? $invoice->buyer_name }}</td>
                                <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst($invoice->status) }}</span>
                                </td>
                                <td class="text-end">₹ {{ number_format($invoice->grand_total, 2) }}</td>
                                <td class="text-end">

                                    <a href="{{ route('invoices.show', $invoice) }}"
                                        class="btn btn-square btn-s btn-outline-primary light ms-1" title="View"><i class="fas fa-eye"></i></a>

                                    @if(!Auth::user()->isClient())
                                        @if ($invoice->status !== 'paid')
                                                <a href="{{ route('invoices.show', ['invoice' => $invoice, 'pay' => 1]) }}"
                                                    class="btn btn-square btn-s btn-outline-primary light ms-1" title="Pay"><i class="fas fa-credit-card"></i></a>
                                        @endif
                                        <a href="{{ route('invoices.edit', $invoice) }}"
                                            class="btn btn-square btn-s btn-outline-primary light ms-1" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-square btn-s btn-outline-primary light ms-1"
                                            onclick="return confirm('Delete this invoice?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No invoices yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection
