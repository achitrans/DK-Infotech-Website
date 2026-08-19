@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

    <div class="col-xl-6 col-lg-6 order-lg-1">
        <div class="auth-info text-center">
            <div class="mb-5 mx-auto col-xxl-6">
                <div class="brand-logo mb-3">
                   <img src="{{ asset('logo.jpeg') }}" alt="" srcset="">
                </div>
                <p class="info-text">The CRM dashboard visualizes customer-related metrics and trends over time, providing
                    valuable insights for better decision-making.</p>
            </div>
            <div class="auth-media">
                <img class="w-100 img-fluid" src="{{ asset('assets/images/login.png') }}" alt="">
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6 mx-auto align-self-center">

        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="auth-form">
            <div class="text-center mb-4">
                <h3 class="mb-0">Reset your password</h3>
                <p class="mb-0">Enter your registered email or mobile to receive an OTP over email and WhatsApp!</p>
            </div>

            @if (session('otp_sent'))
                <div class="col-12">
                    <div class="border rounded-3 p-4">
                        <p class="fw-semibold mb-3">Step 2: Enter the code and set a new password</p>

                        <form method="POST" action="{{ route('password.reset') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="identifier_reset" class="form-label">Email address or mobile</label>
                                <input type="text" name="identifier" id="identifier_reset" class="form-control form-control-lg"
                                    value="{{ old('identifier') }}" placeholder="name@example.com or 9876543210" required
                                    readonly>
                                @error('identifier')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="otp" class="form-label">OTP</label>
                                <input type="text" name="otp" id="otp" class="form-control form-control-lg"
                                    placeholder="000000" required>
                                @error('otp')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg"
                                    placeholder="Enter your new password" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control form-control-lg" placeholder="Confirm new password" required>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-dark" type="submit">Reset password</button>
                            </div>
                        </form>

                        <p class="text-muted small mt-3 mb-0">The code expires in 10 minutes.</p>
                    </div>
                </div>
            @else
                <div class="col-12">
                    <div class="border rounded-3 p-4">
                        <p class="fw-semibold mb-3">Step 1: Request a one-time code</p>
                        <form method="POST" action="{{ route('password.otp') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="identifier_request" class="form-label">Email address or mobile</label>
                                <input type="text" name="identifier" id="identifier_request" class="form-control form-control-lg"
                                    value="{{ old('identifier') }}" placeholder="name@example.com or 9876543210" required>
                                @error('identifier')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button class="btn btn-dark w-100" type="submit">Send OTP</button>
                        </form>
                        <p class="text-muted small mt-3 mb-0">You can request another code after two minutes if you still
                            need
                            it.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">Back to login</a>
        </div>
    </div>
@endsection
