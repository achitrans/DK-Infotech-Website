@extends('layouts.app')
@section('title', 'Change Password')
@section('content')

    <div class="auth-form">
        <div class="text-center mb-4">
            <h3 class="mb-0">Change your password</h3>
        </div>

        <div class="col-12">
            <div class="border rounded-3 p-4">

                <form method="POST" action="{{ route('change-password') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control form-control-lg" required>
                        @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-lg" required>
                        @error('new_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-lg"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
