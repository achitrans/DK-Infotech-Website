@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit branch</h5>
            <a href="{{ route('branches.index') }}" class="btn btn-link btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('branches.update', $branch) }}">
                @csrf
                @method('PATCH')

                @include('branches.partials.form', compact('branch', 'users'))

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
