<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fc;
            min-height: 100vh;
        }

        .verification-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #0d6efd, #084298);
            color: #fff;
            padding: 30px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: #fff;
            border-radius: 50%;
            padding: 10px;
        }

        .btn-verify {
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
        }

        .info-box {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row justify-content-center">

            <div class="col-lg-7">

                <div class="card shadow-lg verification-card">

                    <div class="card-header-custom text-center">

                        {{-- Company Logo --}}
                        <img src="{{ asset('images/logo-black.webp') }}" class="company-logo mb-3" alt="Company Logo">

                        <h2 class="mb-2">
                            Certificate Verification Portal
                        </h2>

                        <p class="mb-0">
                            Verify the authenticity of company-issued certificates.
                        </p>

                    </div>

                    <div class="card-body p-4">

                        <div class="alert alert-info text-center">
                            Enter your Employee ID or Mobile Number below to verify your certificate.
                            <div>
                                <form action="{{ route('barcode.search') }}" method="GET">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Employee ID
                                        </label>

                                        <input type="text" name="employee_id" class="form-control form-control-lg"
                                            placeholder="Example: EMP001">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Registered Mobile Number
                                        </label>

                                        <input type="text" name="phone" class="form-control form-control-lg"
                                            placeholder="Example: 0000000000">
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-verify w-100">
                                        Verify Certificate
                                    </button>

                                </form>

                            </div>

                            <div class="card-footer text-center text-muted">
                                <div class="col-12 text-start">
                                    &copy; {{ date('Y') }} {{ env('APP_NAME') }}
                                    All Rights Reserved.
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

</body>

</html>
