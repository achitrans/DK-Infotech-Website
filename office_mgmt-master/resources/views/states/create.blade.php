@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Add State</h5>
                    <a href="{{ route('states.index') }}" class="btn btn-link btn-sm">Back to list</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('states.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="code">Code</label>
                            <input id="code" type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" maxlength="5" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="gst_code">GST Code</label>
                            <input id="gst_code" type="text" name="gst_code" value="{{ old('gst_code') }}" class="form-control @error('gst_code') is-invalid @enderror" maxlength="5">
                            @error('gst_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save State</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection