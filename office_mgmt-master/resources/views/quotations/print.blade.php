<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quotation</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
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
            font-size: 1.2rem;
            margin: 0;
        }
        .left-header h2 a{
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #f3f6ff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dae1f4;
            min-height: 90px;
        }

        .summary-label {
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #4a5c7a;
            margin-bottom: 9px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 600;
            color: #1d2c58;
        }

        .section-block {
            margin-bottom: 30px;
        }

        .section-heading {
            font-size: 18px;
            color: #00b0f0;
            margin-bottom: 12px;
        }

        .section-content {
            border: 1px solid #dcdcdc;
            padding: 9px 18px;
            border-radius: 6px;
            background: #ffffff;
            min-height: 70px;
        }
    </style>
</head>

<body>
    <!-- Buttons -->
    <div class="controls">
        <button onclick="printPage()">🖨️ Print</button>
    </div>

    @php
        use Carbon\Carbon;
        $quotationDate = Carbon::parse($quotation->date)->format('d M, Y');
        $validUntil = $quotation->exp_date ? Carbon::parse($quotation->exp_date)->format('d M, Y') : null;
    @endphp

    <div class="container" id="letter">
        <header>
            <div class="left-header">
                <h2><a href="{{ env('COMPANY_NAME') }}">{{ env('COMPANY_NAME') }}</a></h2>
                <p>{{ env('COMPANY_ADDRESS')}} ,
                    Mob:- <a href="tel:+{{env('COMPANY_PHONE')}}">{{env('COMPANY_PHONE')}}</a> ,
                    <a href="{{ env('COMPANY_WEBSITE') }}">{{ env('COMPANY_WEBSITE') }}</a> ,
                    <a href="mailto:{{env('COMPANY_EMAIL')}}">{{env('COMPANY_EMAIL')}}</a>
                </p>
            </div>
            <div class="right-header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </div>
        </header>
        <main>
            <h1>Quotation</h1>

            <div class="summary-grid">
                <div class="summary-card">
                    <p class="summary-label">Title</p>
                    <p class="summary-value">{{ $quotation->title }}</p>
                </div>
                <div class="summary-card">
                    <p class="summary-label">Customer</p>
                    <p class="summary-value">{{ $quotation->name }}</p>
                </div>
                <div class="summary-card">
                    <p class="summary-label">Product</p>
                    <p class="summary-value">{{ optional($quotation->product)->name ?? 'Product removed' }}</p>
                </div>
                <div class="summary-card">
                    <p class="summary-label">Valid until</p>
                    <p class="summary-value">{{ $validUntil ?? 'No expiry' }}</p>
                </div>
            </div>

            <table class="details-table">
                <tr>
                    <td>Quotation Date</td>
                    <td>{{ $quotationDate }}</td>
                </tr>
                <tr>
                    <td>Contact</td>
                    <td>
                        {{ $quotation->mobile ?? 'Not provided' }}
                        @if($quotation->email)
                            <br>
                            <small>{{ $quotation->email }}</small>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Branch</td>
                    <td>{{ optional($quotation->branch)->name ?? 'Main branch' }}</td>
                </tr>
            </table>

            @if($quotation->intro)
            <section class="section-block">
                    <h2 class="section-heading">Introduction</h2>
                    <div class="section-content">
                        {!! $quotation->intro !!}
                    </div>
                </section>
            @endif

            @if($quotation->description)
                <section class="section-block">
                    <h2 class="section-heading">Description</h2>
                    <div class="section-content">
                        {!! $quotation->description !!}
                    </div>
                </section>
            @endif

            @if($quotation->terms)
                <section class="section-block">
                    <h2 class="section-heading">Terms</h2>
                    <div class="section-content">
                        {!! $quotation->terms !!}
                    </div>
                </section>
            @endif
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
