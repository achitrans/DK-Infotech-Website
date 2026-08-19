@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Branches</h5>
            <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">Add branch</a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input name="search" value="{{ request('search', $search ?? '') }}" placeholder="Search branches" class="form-control" />
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('branches.index') }}" class="btn btn-link">Reset</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Display</th>
                        <th>Code</th>
                        <th>Manager</th>
                        <th>Contact</th>
                        <th>User/Mgr</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>{{ $branch->display_name }}</td>
                            <td>{{ $branch->code }}</td>
                            <td>{{ $branch->manager_name ?? '—' }}</td>
                            <td>
                                <div>{{ $branch->mobile ?? '—' }}</div>
                                <div>{{ $branch->email ?? '—' }}</div>
                            </td>
                            <td>{{ $branch->user?->name ?? '—' }}</td>
                            <td>{{ $branch->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="text-end">
                                <a href="{{ route('branches.show', $branch) }}" class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-info btn-sm">Edit</a>
                                {{-- <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Delete this branch?')">Delete</button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No branches yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $branches->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
