<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employment Certificate Letter</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 40px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #3c5a99;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .left-header h2 {
            color: #00b0f0;
            font-size: 2rem;
            margin: 0;
        }

        .left-header h2 a {
            color: #00b0f0;
        }

        .left-header p {
            margin: 2px 0;
            font-size: 15px;
            font-weight: 500;
            color: #000;
        }

        .right-header img {
            width: 220px;
            height: auto;
        }

        h1 {
            color: #00b0f0;
            text-align: center;
            font-size: 24px;
            text-decoration: none;
            margin-bottom: 30px;
        }

        /* Buttons */
        .controls {
            text-align: right;
            margin-bottom: 20px;
        }

        .controls button {
            padding: 10px 15px;
            font-size: 14px;
            margin-left: 10px;
            background-color: #4caf50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .controls button:hover {
            background-color: #45a049;
        }

        .container {
            width: 210mm;
            min-height: 297mm;
            padding: 40px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        main h1 {
            font-size: 24px;
            color: #43d17a;
            margin-bottom: 30px;
        }

        main p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .ceo {
            color: #a042c3;
        }

        .signature {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #999;
            width: 200px;
        }

        /* Print Styles */
        @media print {
            body {
                background: none;
                padding: 0;
            }

            .controls {
                display: none;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
            }
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .details-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 15px;
        }

        .logo-cell {
            width: 150px;
            text-align: center;
            vertical-align: middle;
        }

        .logo-cell img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>

<body>
    <!-- Buttons -->
    <div class="controls">
        <button onclick="printPage()">🖨️ Print</button>
    </div>

    <div class="container" id="letter">
        <header>
            <div class="left-header">
                <h2><a href="{{ env('COMPANY_NAME') }}">{{ env('COMPANY_NAME') }}</a></h2>
                <p>{!! nl2br(env('COMPANY_ADDRESS')) !!} ,
                    Mob:- <a href="tel:+{{ env('COMPANY_PHONE') }}">{{ env('COMPANY_PHONE') }}</a> ,
                    <a href="{{ env('COMPANY_WEBSITE') }}">{{ env('COMPANY_WEBSITE') }}</a> ,
                    <a href="mailto:{{ env('COMPANY_EMAIL') }}">{{ env('COMPANY_EMAIL') }}</a>
                </p>
            </div>
            <div class="right-header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </div>
        </header>

        @php
            $data = \App\Models\ExperienceLetter::where('user_id', auth()->id())->first();
        @endphp

        <main>
            <h1>TO WHOMSOEVER IT MAY CONCERN</h1>

            <p>This is to certify that {{ auth()->user()->name ?? '---' }} successfully completed an internship at
                {{ env('COMPANY_NAME') }} from
                {{ $data->start_date ?? '--' }} to {{ $data->end_date ?? '--' }} in the
                {{ $data->position ?? '---' }}.</p>


            <p>During the internship period, {{ auth()->user()->name ?? '---' }} actively contributed to various
                projects and
                responsibilities involving:</p>

            @if (!empty($data->skill))
                {{ strtoupper(str_replace(',', ', ', $data->skill)) }}
            @else
                ---
            @endif

            <p style="margin-top: 10px">{{ auth()->user()->name ?? '---' }} demonstrated excellent dedication,
                initiative, and problem-solving
                abilities throughout the internship. The work performed reflected a strong commitment to learning,
                professionalism, and teamwork. {{ auth()->user()->name ?? '---' }} consistently met assigned objectives
                and made valuable contributions to the organization.</p>

            <p>Based on performance and conduct during the internship, {{ auth()->user()->name ?? '---' }} has shown
                the potential to excel in future professional endeavors. We appreciate the contributions made and extend
                our best wishes for continued success in all future pursuits.</p>
            </p>

            <P>You are expected to complete e-documentation formalities before you join on
                {{ auth()->user()->career?->joining_date }}. Please
                {{ env('COMPANY_NAME') }}
                {{ env('COMPANY_ADDRESS') }}, Mob:- {{ env('COMPANY_PHONE') }}
                {{ env('COMPANY_WEBSITE') }} {{ env('COMPANY_EMAIL') }}
            </p>

            <div>
                For <strong>{{ env('COMPANY_NAME') }}</strong>
                <p><strong class="ceo">{{ env('COMPANY_AUTHORISED_PERSON') }}</strong><br>CEO</p>
                <img src="{{ asset('images/auth_sign.png') }}" alt="Authorised Sign">
                <div class="signature">Signature</div>
            </div>

        </main>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>
