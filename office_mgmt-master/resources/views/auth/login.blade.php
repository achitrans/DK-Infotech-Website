@extends('layouts.auth')

@section('title', 'Login')

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
                <img class="w-100 img-fluid" src="assets/images/login.png" alt="">
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6 mx-auto align-self-center">
        <div class="auth-form">
            <div class="text-center mb-4">
                <h3 class="mb-0">Sign In</h3>
                <p class="mb-0">Log in to continue your journey!</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="text" class="form-control form-control-lg" id="email" name="email"
                        placeholder="Enter your email" required autofocus>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" tabindex="-1">Forgot
                            password?</a>

                    </div>
                    <input type="password" class="form-control form-control-lg ic-password" id="password" name="password"
                        placeholder="Enter your password" required>

                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember-me" name="remember">
                    <label class="form-check-label" for="remember-me">Remember me</label>
                </div>
                <div class="d-grid">
                    <button class="btn btn-dark btn-lg fw-medium" type="submit">Sign In</button>
                </div>
            </form>
        </div>
    </div>
@endsection
