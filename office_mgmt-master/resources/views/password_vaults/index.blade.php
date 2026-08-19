@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Password Vault</h5>
                    </div>
                    <a href="{{ route('password-vaults.create') }}" class="btn btn-success btn-sm">New entry</a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('password-vaults.index') }}" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Keyword</label>
                            <input
                                type="search"
                                name="search"
                                value="{{ old('search', $search) }}"
                                class="form-control"
                                placeholder="Filter by name, username, URL or category">
                        </div>
                        <div class="col-auto align-self-end">
                            <button type="submit" class="btn btn-primary">Apply filter</button>
                            <a href="{{ route('password-vaults.index') }}" class="btn btn-outline-primary ms-2">Clear</a>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Category</th>
                                    <th>Last used</th>
                                    <th>Type</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vaults as $vault)
                                    <tr>
                                        <td>{{ $vault->name }}</td>
                                        <td>{{ $vault->username }}</td>
                                        <td>{{ $vault->category ?? '—' }}</td>
                                        <td>{{ optional($vault->last_used_at)->format('d M Y, h:i A') ?? 'Never' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $vault->is_shared ? 'success' : 'secondary' }}">
                                                {{ $vault->is_shared ? 'Shared' : 'Private' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('password-vaults.show', $vault) }}" class="btn btn-square btn-s btn-outline-primary light ms-1" title="Show"><i class="fas fa-eye"></i></a>
                                            @if ($vault->user_id === auth()->id())
                                                <a href="{{ route('password-vaults.edit', $vault) }}" class="btn btn-square btn-s btn-outline-primary light ms-1" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No credentials found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        {{ $vaults->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
