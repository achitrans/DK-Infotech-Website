@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Expense Head</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
    <form action="{{ route('expense-heads.update', $head) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $head->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $head->description) }}</textarea>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $head->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('expense-heads.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
        </div>
    </div>

</div>
@endsection
