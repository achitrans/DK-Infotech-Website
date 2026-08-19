@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $branch->display_name }}</h5>
            <div>
                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-link btn-sm">Edit</a>
                <a href="{{ route('branches.index') }}" class="btn btn-link btn-sm">Back</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Legal name</dt>
                <dd class="col-sm-9">{{ $branch->legal_name ?? '—' }}</dd>

                <dt class="col-sm-3">Code</dt>
                <dd class="col-sm-9">{{ $branch->code }}</dd>

                <dt class="col-sm-3">Manager</dt>
                <dd class="col-sm-9">{{ $branch->manager_name ?? '—' }} ({{ $branch->manager_phone ?? '—' }})</dd>

                <dt class="col-sm-3">Contact</dt>
                <dd class="col-sm-9">
                    <div>{{ $branch->mobile ?? '—' }}</div>
                    <div>{{ $branch->whatsapp_number ?? '—' }}</div>
                    <div>{{ $branch->email ?? '—' }}</div>
                </dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $branch->address ?? '—' }}<br>{{ implode(', ', array_filter([$branch->city, $branch->state, $branch->pincode])) }}</dd>

                <dt class="col-sm-3">GSTIN / PAN</dt>
                <dd class="col-sm-9">{{ $branch->gstin ?? '—' }} / {{ $branch->pan ?? '—' }}</dd>

                <dt class="col-sm-3">Assigned user</dt>
                <dd class="col-sm-9">{{ $branch->user?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $branch->is_active ? 'Active' : 'Inactive' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
