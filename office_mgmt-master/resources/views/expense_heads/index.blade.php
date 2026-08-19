@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Expense Heads</h3>
        <a href="{{ route('expense-heads.create') }}" class="btn btn-primary">Add Head</a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($heads as $head)
            <tr>
                <td>{{ $head->name }}</td>
                <td>{{ $head->description }}</td>
                <td>{{ $head->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('expense-heads.edit', $head) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('expense-heads.destroy', $head) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this head?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $heads->links() }}

        </div>
    </div>
</div>
@endsection
