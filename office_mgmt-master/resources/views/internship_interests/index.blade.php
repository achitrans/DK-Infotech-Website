@extends('layouts.app')
@section('title', 'Internship Interests')
@section('content')
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">Filter Applicants</div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('internship-interests.index') }}" class="form-row row">
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Name / Email / Phone</label>
                                <input type="text" name="q" class="form-control" placeholder="Search"
                                    value="{{ request('q') }}">
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    @foreach (\App\Models\InternshipInterest::$statuses as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Source</label>
                                <select name="source" class="form-control">
                                    <option value="">All</option>
                                    @foreach (\App\Models\InternshipInterest::$sources as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ request('source') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('internship-interests.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Applicants</span>
            </div>
            <div class="card-body table-responsive">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Pay. Status</th>
                            <th>Txn Id.</th>
                            <th>G. Txn Id.</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interests as $i)
                            <tr>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->name }}</td>
                                <td>{{ $i->email }} <br> <a class="btn btn-outline-primary btn-sm"
                                        href="tel:+91 {{ $i->phone ?? '' }}">+91 {{ $i->phone ?? '' }}</a></td>
                                <td>{{ \App\Models\InternshipInterest::$types[$i->type] ?? $i->type }}</td>
                                <td>{{ \App\Models\InternshipInterest::$sources[$i->source] ?? $i->source }}</td>
                                <td>{{ \App\Models\InternshipInterest::$statuses[$i->status] ?? $i->status }}</td>
                                <td>{{ $i->payment_status ?? 'N/A' }}</td>
                                <td>{{ $i->txn_id ?? 'N/A' }}</td>
                                <td>{{ $i->gateway_txn_id ?? 'N/A' }}</td>
                                <td>{{ $i->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('internship-interests.show', $i->id) }}"
                                        class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('internship-interests.edit', $i->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="{{ route('internship-interests.download', $i->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fa-solid fa-download"
                                            title="download"></i></a>
                                    {{-- <a href="{{ route('internship-interests.send.mail', $i->id) }}"
                                        class="btn btn-warning btn-sm">Send Mail</a> --}}
                                    <form action="{{ route('internship-interests.destroy', $i->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this entry?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No applicants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $interests->links() }}
            </div>
        </div>
    </div>
@endsection
