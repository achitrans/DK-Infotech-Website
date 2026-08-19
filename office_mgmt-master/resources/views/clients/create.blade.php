@extends('layouts.app')
@section('title', 'Add Client')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Add Client</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('clients.store') }}">
                            @csrf

                            <span class="text-warning fs-4">Basic Information</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                            </div>

                            <span class="text-warning fs-4">Personal Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-4">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group my-2 col-md-4">
                                    <label>Mobile</label>
                                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" placeholder="Enter mobile number" required>
                                </div>
                            </div>

                            <hr>
                            <span class="text-warning fs-4">Other Details</span>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Tawk Code</label>
                                    <input type="text" name="tawk_code" class="form-control" value="{{ old('tawk_code') }}" placeholder="Enter Tawk code">
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Password (Optional - default password generated if blank)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Leave empty for auto-generated">
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Save Client</button>
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
