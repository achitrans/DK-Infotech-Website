<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate {{ $estimate->estimate_number }}</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            /* background: #f5f6fb; */
            color: #172033;
            margin: 0;
            padding: 16px;
        }

        .sheet {
            max-width: 960px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(23, 32, 51, 0.15);
            padding: 40px 48px;
        }

        .sheet header {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #e1e4ed;
            padding-bottom: 16px;
            margin-bottom: 24px;
            gap: 18px;
        }

        .brand-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
        }

        .logo img {
            max-height: 52px;
            object-fit: contain;
        }

        .brand-info {
            text-align: right;
        }

        .brand-info .company {
            font-size: 18px;
            font-weight: 600;
        }

        .brand-info .gst {
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #8892a5;
        }

        .title-row {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .sheet h1 {
            margin: 0;
            font-size: 32px;
            letter-spacing: 0.12em;
            text-align: center;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 32px;
        }

        .bill-column {
            flex: 0 0 320px;
        }

        .card {
            /* border: 1px solid #eef1f7;
            border-radius: 16px; */
            padding: 20px;
            background: #fefefe;
            min-width: 0;
        }

        .card .title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8892a5;
            margin-bottom: 6px;
        }

        .card .value {
            font-size: 17px;
            font-weight: 600;
            color: #172033;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 6px;
            margin-top: 8px;
            text-align: right;
        }

        .meta div {
            font-size: 14px;
            color: #6b7280;
        }

        .section-heading {
            font-size: 15px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #8892a5;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 32px;
            border-bottom: 2px solid #e1e4ed;
        }

        thead th {
            text-align: left;
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #8892a5;
            padding-bottom: 12px;
            border-bottom: 1px solid #e1e4ed;
        }

        th.rate, th.total {
            text-align: right;
        }

        tbody tr + tr td {
            border-top: 1px solid #f0f2f7;
        }

        td {
            padding: 12px 0;
        }

        td.rate, td.total {
            text-align: right;
        }

        .totals {
            margin-left: auto;
            max-width: 320px;
            font-size: 14px;
            align-self: flex-end;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-top: 1px solid transparent;
        }

        .totals .grand {
            font-size: 20px;
            font-weight: 600;
            color: #0d6efd;
            border-top: 1px solid #e1e4ed;
            margin-top: 8px;
            padding-top: 12px;
        }

        .amount-words {
            margin-top: 8px;
            font-size: 14px;
            color: #172033;
        }

        .footer-note {
            font-size: 13px;
            color: #5f6571;
            line-height: 1.6;
            margin-top: 50px;
        }

        .footer-contact {
            margin-top: 24px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            font-size: 13px;
            color: #5f6571;
        }

        .footer-contact div span {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #8892a5;
            margin-bottom: 4px;
        }

        .footer-contact a {
            color: #172033;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .sheet {
                padding: 24px 20px;
            }

            .brand-row {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .brand-info {
                text-align: left;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 16px;
                border-bottom: 1px solid #eef1f7;
                padding-bottom: 12px;
            }

            td {
                padding: 6px 0;
            }

            td[role="label"] {
                font-size: 12px;
                text-transform: uppercase;
                color: #8892a5;
                display: block;
            }

            td.value {
                font-size: 16px;
                font-weight: 600;
            }
        }

        @media print {
            body {
                background: transparent;
                padding: 8px;
            }

            .sheet {
                box-shadow: none;
                background: transparent;
                border: 1px solid #bababa;
                border-radius: 0;
                margin: 0;
                padding: 15px;
            }
        }
    </style>

<body>
<div class="sheet">
    <header>
        <div class="brand-row">
            <div class="logo">
                <img src="{{ asset('logo.png') }}" height="50" alt="{{ config('app.name', 'Company') }} logo"
                     onerror="this.style.display='none'"/>
            </div>
            <div class="brand-info">
                <div class="company">{{ config('app.name', 'Your Company') }}</div>
                <div class="gst">GSTIN: {{ config('app.gstin', '33AAAAA0000A1Z5') }}</div>
            </div>
        </div>
        <div class="title-row">
            <h1>Estimate</h1>
            {{-- <div class="badge">{{ ucfirst($estimate->status) }}</div> --}}
        </div>
        <div class="header-top">
            <div class="bill-column">
                <div class="card">
                    <div class="title">Bill To</div>
                    <div>Name: {{ $estimate->buyer_name }}</div>
                    <div>M.No: {{ $estimate->buyer_mobile ?? '—' }}</div>
                    <div>GSTIN: {{ $estimate->buyer_gstin ?? '-' }}</div>
                </div>
            </div>
            <div class="meta">
                <div><strong>Estimate #</strong><br>{{ $estimate->estimate_number }}</div>
                <div><strong>Date</strong><br>{{ $estimate->estimate_date?->format('Y-m-d') ?? '-' }}</div>
                <div><strong>Expiry</strong><br>{{ $estimate->expiry_date?->format('Y-m-d') ?? '-' }}</div>
            </div>
        </div>
    </header>

    <div class="section-heading"></div>
    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th>HSN</th>
            <th class="rate">Qty</th>
            <th class="rate">Rate</th>
            <th class="rate">Dis.</th>
            <th class="rate">GST%</th>
            <th class="total">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($estimate->items as $item)
            <tr>
                <td>
                    <div class="value">{{ $item->item_name }}</div>
                    <div role="label" style="font-size:12px;color:#8892a5;">
                        Product: {{ $item->product?->name ?? 'Manual' }}</div>
                </td>
                <td>{{ $item->hsn_code ?? '-' }}</td>
                <td class="rate">{{ number_format($item->quantity, 2) }}</td>
                <td class="rate">Rs {{ number_format($item->rate, 2) }}</td>
                <td class="rate">Rs {{ number_format($item->discount, 2) }}</td>
                <td class="rate">{{ number_format($item->gst_rate, fmod($item->gst_rate, 1) ? 2 : 0) }}%</td>
                <td class="total">Rs {{ number_format($item->total_amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>Rs {{ number_format($estimate->sub_total, 2) }}</span></div>
        <div><span>Total tax</span><span>Rs {{ number_format($estimate->total_tax, 2) }}</span></div>
        <div class="grand"><span>Grand total</span><span>Rs {{ number_format($estimate->grand_total, 2) }}</span></div>
        <div><span>{{ \App\Support\AmountInWords::convert($estimate->grand_total) }}</span></div>
    </div>

    <div class="footer-note">
        {{-- <strong>Amount (in words):</strong>
        <span class="mb-0">{{ \App\Support\AmountInWords::convert($estimate->grand_total) }}</span>
        <br> --}}
        Thank you for considering our services. This estimate is valid until the expiry date mentioned above unless
        revoked earlier. For any clarifications, reply to this mail or call the contact listed below.
    </div>
    <div class="footer-contact">
        @if (env('COMPANY_PHONE'))
            <div>
                <span>Mobile</span>
                <a href="tel:{{ env('COMPANY_PHONE') }}">{{ env('COMPANY_PHONE') }}</a>
            </div>
        @endif
        @if (env('COMPANY_EMAIL'))
            <div>
                <span>Email</span>
                <a href="mailto:{{ env('COMPANY_EMAIL') }}">{{ env('COMPANY_EMAIL') }}</a>
            </div>
        @endif
        @if (env('COMPANY_ADDRESS'))
            <div>
                <span>Address</span>
                <div>{{ env('COMPANY_ADDRESS') }}</div>
            </div>
        @endif
    </div>
</div>
</body>
</html>
