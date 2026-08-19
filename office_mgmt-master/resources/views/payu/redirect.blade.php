<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment Gateway</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a, #1e293b, #312e81);
            color: #fff;
            padding: 20px;
        }

        .payment-card {
            max-width: 600px;
            width: 100%;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,.3);
        }

        .spinner-border {
            width: 4rem;
            height: 4rem;
        }

        .title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 25px;
        }

        .description {
            color: #cbd5e1;
            margin-top: 15px;
            line-height: 1.7;
        }

        .secure-badge {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            border-radius: 30px;
            background: rgba(34,197,94,.15);
            color: #86efac;
            font-size: 14px;
        }

        #manualSubmit {
            display: none;
            margin-top: 30px;
        }

        .footer-note {
            margin-top: 25px;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body onload="submitPaymentForm()">

<div class="payment-card">

    <div class="spinner-border text-light" role="status"></div>

    <h2 class="title">Redirecting to Secure Payment Gateway</h2>

    <p class="description">
        Please wait while we securely connect you to our payment partner.
        Do not refresh, close this page, or press the back button during this process.
        You will be redirected automatically within a few seconds.
    </p>

    <div class="secure-badge">
        🔒 Secure SSL Encrypted Payment
    </div>

    <button
        id="manualSubmit"
        type="button"
        class="btn btn-primary btn-lg w-100">
        Continue to Payment
    </button>

    <div class="footer-note">
        If redirection does not happen automatically, click the button above.
    </div>

    <form action="{{ $url }}" method="post" name="payuForm" id="payuForm">
        @foreach($params as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

</div>

<script>
    function submitPaymentForm() {
        document.forms['payuForm'].submit();
    }

    // Show fallback button after 10 seconds
    setTimeout(function () {
        const btn = document.getElementById('manualSubmit');
        btn.style.display = 'block';

        btn.addEventListener('click', function () {
            document.getElementById('payuForm').submit();
        });
    }, 10000);
</script>

</body>
</html>
