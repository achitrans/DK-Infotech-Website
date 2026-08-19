@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add branch</h5>
            <a href="{{ route('branches.index') }}" class="btn btn-link btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('branches.store') }}">
                @csrf

                @include('branches.partials.form', ['branch' => new \App\Models\Branch, 'users' => $users])

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Create branch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
