@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Estimates</h4>
            <a href="{{ route('estimates.create') }}" class="btn btn-sm btn-primary">Create Estimate</a>
        </div>
        {{-- Filter bar --}}
        <div class="card-body border-bottom pb-3">
            <form method="GET" action="{{ route('estimates.index') }}" class="row g-2 align-items-end">
                <div class="row date-fields-row">
                <div class="col-sm-3">
                    <label for="filter_status" class="form-label small mb-1">Status</label>
                    <select id="filter_status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['draft' => 'Draft', 'sent' => 'Sent', 'approved' => 'Approved', 'expired' => 'Expired'] as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                    <div class="col-sm-3  date-field">
                        <label for="filter_date_from" class="form-label small mb-1">Date from</label>
                        <input type="date" id="filter_date_from" name="date_from" class="form-control"
                            value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-sm-3  date-field">
                        <label for="filter_date_to" class="form-label small mb-1">Date to</label>
                        <input type="date" id="filter_date_to" name="date_to" class="form-control"
                            value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-auto align-self-end">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a href="{{ route('estimates.index') }}" class="btn btn-outline-info">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Estimate #</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th class="text-end">Grand Total</th>
                            <th>Status</th>
                            @if (!Auth::user()->isClient())
                                <th>Converted</th>
                            @endif
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estimates as $estimate)
                            <tr>
                                <td>{{ $estimate->estimate_number }}</td>
                                <td>{{ $estimate->estimate_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $estimate->client?->name ?? $estimate->buyer_name }}</td>
                                <td class="text-end">Rs {{ number_format($estimate->grand_total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $estimate->status === 'draft' ? 'secondary' : 'success' }}">
                                        {{ ucfirst($estimate->status) }}
                                    </span>
                                </td>

                                @if (!Auth::user()->isClient())
                                    <td>{{ $estimate->convertedInvoice ? 'Yes' : 'No' }}</td>
                                @endif
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <a href="{{ route('estimates.public', $estimate->public_token) }}"
                                             class="btn btn-square btn-s btn-outline-primary light ms-1" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                        @if (!Auth::user()->isClient())
                                            <a href="{{ route('estimates.edit', $estimate) }}"
                                                 class="btn btn-square btn-s btn-outline-primary light ms-1" title="Edit"><i class="fas fa-edit"></i></a>

                                            @if (!$estimate->convertedInvoice)
                                                <a href="{{ route('invoices.convert', $estimate) }}"
                                                    class="btn btn-square btn-s btn-outline-primary light ms-1" title="convert" >Convert</a>
                                            @endif
                                            <form action="{{ route('estimates.destroy', $estimate) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete {{ $estimate->estimate_number }} estimate?');"
                                                class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"  class="btn btn-square btn-s btn-outline-primary light ms-1" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No estimates yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $estimates->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
