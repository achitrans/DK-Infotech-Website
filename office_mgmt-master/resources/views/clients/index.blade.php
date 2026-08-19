@extends('layouts.app')
@section('title', 'Clients')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Clients</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('clients.create') }}" class="btn btn-outline-primary">Add Client</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Filter Client List</div>
            <div class="card-body">
                <div class="mb-3">
                    <form method="GET" action="{{ route('clients.index') }}" class="form-inline">
                        <div class="row">
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Name"
                                    value="{{ request('name') }}">
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email"
                                    value="{{ request('email') }}">
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Status</option>
                                    @foreach (\App\Models\User::$status as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-4">
                                <button type="submit" name="search" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email & Mobile</th>
                            <th>Company & GSTIN</th>
                            <th>Status</th>
                            <th>Tawk Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->email }} <br> {{ $client->mobile ?? '-' }}</td>
                                <td>
                                    {{ $client->kycClient->business_name ?? '-' }} <br>
                                    <small class="text-muted">GSTIN: {{ $client->kycClient->business_gstin ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusColor = $client->status == 'active' ? 'success' : ($client->status == 'inactive' ? 'secondary' : 'danger');
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }}">{{ ucfirst($client->status) }}</span>
                                </td>
                                <td>{{ $client->tawk_code ?? '-' }}</td>
                                <td class="d-flex" >
                                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-square btn-s btn-outline-primary light" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-square btn-s btn-outline-primary light"
                                                ondblclick="return confirm('Are you sure you want to delete this client?')"
                                                onclick="return false" title="Delete"><i class="fa fa-trash"></i></button>
                                        </form>
                                        <a href="{{ route('client-kyc.show', Crypt::encrypt($client->id)) }}" title="KYC" class="btn btn-square btn-s btn-outline-primary light">
                                            <i class="fas fa-fingerprint"></i>
                                        </a>
                                        <a href="{{ route('loginByPass', Crypt::encrypt($client->id)) }}" title="Login" class="btn btn-square btn-s btn-outline-primary light">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
