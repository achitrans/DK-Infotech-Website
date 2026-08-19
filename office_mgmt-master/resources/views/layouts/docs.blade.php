<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>@yield('letter_name') - {{ env('COMPANY_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --ink: #1f2937;
            --muted: #6b7280;
            --brand: #0ea5e9;
            --paper: #ffffff;
            --bg: #eef2f7;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            line-height: 1.55;
            height: 100%;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 18px;
        }

        .btn {
            appearance: none;
            border: none;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 4px;
            background: #111827;
            color: white;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            background: var(--paper);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
            display: flex;
            flex-direction: column;
            padding: 20mm 18mm;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            border-bottom: 3px solid var(--brand);
            padding-bottom: 14px;
        }

        .brand h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: .3px;
        }

        .brand p {
            margin: 4px 0 0;
            font-size: 12.5px;
            color: var(--muted);
        }

        .header img.logo {
            width: 180px;
            height: auto;
        }

        .title {
            text-align: center;
            margin: 22px 0 14px;
        }

        .title h2 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .title .sub {
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin: 18px 0 8px;
            font-size: 14px;
        }

        .meta strong {
            font-weight: 600;
        }

        .body {
            flex: 1;
        }

        .body p {
            margin: 12px 0;
            font-size: 15px;
            text-align: justify;
        }

        .sign-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 60px;
        }

        .sign {
            width: 200px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-size: 14px;
            text-align: center;
            position: relative;
        }

        .sign img {
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 160px;
            height: auto;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-top: 20px;
        }

        @page {
            size: A4;
            margin: 14mm;
        }

        @media print {
            body {
                background: var(--paper);
            }

            .sheet {
                box-shadow: none;
                margin: 0;
                width: auto;
                min-height: auto;
            }

            .toolbar {
                display: none !important;
            }

            .footer {
                position: fixed;
                bottom: 14mm;
                left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="toolbar no-print">
    <button class="btn" onclick="window.print()">Print</button>
</div>

<section class="sheet">
    <header class="header">
        <div class="brand">
            <h1>{{ env('COMPANY_NAME') }} - {{ $city ?? '' }}</h1>
            <p>{!! env('COMPANY_ADDRESS') !!}</p>
        </div>
        <img src="{{ asset('logo.png') }}" alt="{{ env('COMPANY_NAME') }} Logo" class="logo" />
    </header>

    <div class="title">
        <h2>@yield('letter_name')</h2>
    </div>

    <div class="title">
        <div class="sub">To Whomsoever It May Concern</div>
    </div>

    <div class="meta">
        <div><strong>Date:</strong> 16 June, 2025</div>
        <div><strong>Ref. No.:</strong> DKIS/INT/2025/____</div>
    </div>

    <div class="body">
        <p>
            This is to certify that <strong>[Student’s Full Name]</strong>, a student of <strong>[College/University Name]</strong>, has successfully completed <strong>5 months</strong> of a <strong>virtual internship program</strong> in <strong>Full Stack (HTML, CSS, JAVA, PHP)</strong> at <strong>{{ env('COMPANY_NAME') }}</strong> from <strong>01/04/2025</strong> to <strong>02/09/2025</strong>.
        </p>

        <p>
            We were truly amazed by his/her showcased skills and invaluable contributions to the tasks and projects throughout the internship. He/She worked sincerely with dedication, discipline, and enthusiasm, consistently producing quality outcomes and collaborating effectively with team members.
        </p>

        <p>
            The knowledge, skills, and practical exposure gained during this internship will support his/her future academic and professional endeavors. We wish <strong>[Student’s Name]</strong> continued success in all future pursuits.
        </p>
    </div>

    <div class="sign-row">
        <div class="sign">
            <img src="{{ asset('auth-sign.png') }}" alt="Auth Signature" />
            <strong>Authorized Signature</strong><br />
            {{ env('COMPANY_NAME') }}
        </div>
    </div>

    <div class="footer">
        {{ env('COMPANY_NAME') }} • Phone: {{ env('COMPANY_PHONE') }}
    </div>
</section>

</body>

</html>

