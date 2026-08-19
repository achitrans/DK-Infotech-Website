<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #172033;
            margin: 0;
            padding: 16px;
            background: #eef2ff;
        }

        .sheet {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
            border-radius: 24px;
            padding: 36px 48px;
            box-shadow: 0 32px 80px rgba(15, 23, 42, 0.1);
            border: 1px solid #cbd5f5;
        }

        header {
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 28px;
            padding-bottom: 24px;
        }

        .brand-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .logo img {
            max-height: 60px;
            object-fit: contain;
        }

        .brand-info {
            text-align: right;
        }

        .brand-info .company {
            font-weight: 600;
            font-size: 18px;
            letter-spacing: 0.1em;
        }

        .brand-info .gst {
            font-size: 13px;
            color: #64748b;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .title-row h1 {
            margin: 0;
            font-size: 32px;
            letter-spacing: 0.16em;
            text-align: center;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-top: 20px;
        }

        .card {
            /* border: 1px solid #eef1f7;
            border-radius: 16px; */
            padding: 20px;
            /* background: #fefefe; */
            min-width: 0;
        }

        .card .title {
            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .card .value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            text-align: right;
        }

        .meta div {
            font-size: 14px;
            color: #475467;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 32px;
        }

        thead th {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-size: 13px;
        }

        th{
            text-align: left;
        }

        td {
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        th.rate,
        th.total {
            text-align: right;
        }

        td.rate,
        td.total {
            text-align: right;
        }

        .totals {
            /* margin-top: 28px; */
            margin-left: auto;
            max-width: 320px;
            font-size: 14px;
            /* border-top: 2px solid #e2e8f0; */
            padding-top: 12px;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-top: 0.9px solid #e2e8f0;
        }

        .totals .grand {
            font-size: 20px;
            font-weight: 600;
            color: #0d6efd;
            border-top: 1px solid #e1e4ed;
        }

        .amount-words {
            margin-top: 12px;
            font-size: 14px;
            color: #27272a;
        }

        .footer-note {
            font-size: 13px;
            color: #5f6571;
            line-height: 1.6;
            margin-top: 40px;
        }

        .footer-contact {
            margin-top: 26px;
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
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .footer-contact a {
            color: #172033;
            text-decoration: none;
            font-weight: 600;
        }

        @media print {
            body {
                background: transparent;
                padding: 8px;
            }

            .sheet {
                box-shadow: none;
                border-radius: 0;
                border: 1px solid #bababa;
                padding: 18px 24px;
            }
        }
    </style>
</head>
<body>
<div class="sheet">
    <header>
        <div class="brand-row">
            <div class="logo">
                <img src="{{ asset('logo.png') }}"  height="50" alt="{{ config('app.name', 'Company') }} logo" onerror="this.style.display='none'">
            </div>
            <div class="brand-info">
                <div class="company">{{ config('app.name', 'Your Company') }}</div>
                <div class="gst">GSTIN {{ config('app.gstin', '33AAAAA0000A1Z5') }}</div>
            </div>
        </div>
        <div class="title-row">
            <h1>Invoice</h1>
        </div>
        <div class="header-top">
            <div class="card">
                <div class="title">Bill To</div>
                <div class="value">{{ $invoice->buyer_name }}</div>
                <div>Address: {{ $invoice->billing_address ?? '-' }}</div>
                <div>GSTIN: {{ $invoice->buyer_gstin ?? '-' }}</div>
            </div>
            <div class="meta">
                <div><strong>Invoice #</strong><br>{{ $invoice->invoice_number }}</div>
                <div><strong>Date</strong><br>{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</div>
                <div><strong>Due</strong><br>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</div>
                {{-- <div><strong>Status</strong><br>{{ ucfirst($invoice->status) }}</div> --}}
            </div>
        </div>
    </header>

    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th class="rate">HSN</th>
            <th class="rate">Qty</th>
            <th class="rate">Rate</th>
            <th class="rate">Dis.</th>
            <th class="rate">CGST</th>
            <th class="rate">SGST</th>
            <th class="rate">IGST</th>
            <th class="total">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div class="value">{{ $item->item_name }}</div>
                    <div role="label" style="font-size:12px;color:#94a3b8;">
                        GST @ {{ number_format($item->gst_rate, fmod($item->gst_rate, 1) ? 2 : 0) }}%
                    </div>
                </td>
                <td class="rate">{{ $item->hsn_code ?? '-' }}</td>
                <td class="rate">{{ number_format($item->quantity, 2) }}</td>
                <td class="rate">₹{{ number_format($item->rate, 2) }}</td>
                <td class="rate">₹{{ number_format($item->discount, 2) }}</td>
                <td class="rate">₹{{ number_format($item->cgst_amount, 2) }}</td>
                <td class="rate">₹{{ number_format($item->sgst_amount, 2) }}</td>
                <td class="rate">₹{{ number_format($item->igst_amount, 2) }}</td>
                <td class="total">₹{{ number_format($item->total_amount, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>₹{{ number_format($invoice->sub_total, 2) }}</span></div>
        <div><span>CGST</span><span>₹{{ number_format($invoice->total_cgst, 2) }}</span></div>
        <div><span>SGST</span><span>₹{{ number_format($invoice->total_sgst, 2) }}</span></div>
        <div><span>IGST</span><span>₹{{ number_format($invoice->total_igst, 2) }}</span></div>
        <div class="grand"><span>Grand total</span><span>₹{{ number_format($invoice->grand_total, 2) }}</span></div>
        <div><span>{{ \App\Support\AmountInWords::convert($invoice->grand_total) }}</span></div>

    </div>

    <div class="footer-note">
        {{-- <strong>Amount (in words):</strong>
        <span class="mb-0">{{ \App\Support\AmountInWords::convert($invoice->grand_total) }}</span>
        <br> --}}
        Thank you for your business. This invoice is generated electronically and does not require a signature. Reply to the contact below if you need clarification or an update.
    </div>
    <div class="footer-contact">
        @if(env('COMPANY_PHONE'))
            <div>
                <span>Mobile</span>
                <a href="tel:{{ env('COMPANY_PHONE') }}">{{ env('COMPANY_PHONE') }}</a>
            </div>
        @endif
        @if(env('COMPANY_EMAIL'))
            <div>
                <span>Email</span>
                <a href="mailto:{{ env('COMPANY_EMAIL') }}">{{ env('COMPANY_EMAIL') }}</a>
            </div>
        @endif
        @if(env('COMPANY_ADDRESS'))
            <div>
                <span>Address</span>
                <div>{!! env('COMPANY_ADDRESS') !!}</div>
            </div>
        @endif
    </div>
</div>
</body>
</html>
