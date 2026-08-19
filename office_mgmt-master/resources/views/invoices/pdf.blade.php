<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
        }

        .invoice-box {
            max-width: 650px;
            margin: 20px auto;
            padding: 18px 24px;
            border: 1px solid #eee;
            background: #fff;
            font-size: 13px;
        }

        .header {
            border-bottom: 1.5px solid #007bff;
            margin-bottom: 10px;
            padding-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .logo {
            font-size: 1.5em;
            color: #007bff;
            font-weight: bold;
        }

        .header .invoice-title {
            font-size: 1em;
            color: #333;
        }

        .info-table,
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table th,
        .info-table td,
        .payment-table th,
        .payment-table td {
            border: 1px solid #eee;
            padding: 5px 8px;
        }

        .info-table th {
            background: #f7f7f7;
            text-align: left;
            width: 24%;
        }

        .info-table td {
            background: #fafbfc;
        }

        .summary {
            margin-top: 20px;
            text-align: right;
        }

        .summary-table {
            width: 220px;
            float: right;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #eee;
            padding: 5px 8px;
        }

        .summary-table th {
            background: #f7f7f7;
            text-align: left;
        }

        .summary-table td {
            background: #fafbfc;
            text-align: right;
        }

        .status {
            font-weight: bold;
            color: #fff;
            background: #007bff;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
        }

        .status.paid {
            background: #28a745;
        }

        .status.unpaid {
            background: #dc3545;
        }

        .status.cancelled {
            background: #6c757d;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            color: #888;
            font-size: 11px;
        }

        h4 {
            margin: 12px 0 6px 0;
            font-size: 15px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">{{ env('APP_NAME', 'Self Study Library') }}</div>
            <div class="invoice-title">INVOICE</div>
        </div>
        <table class="info-table">
            <tr>
                <th>Invoice #</th>
                <td>{{ $invoice->invoice_number }}</td>
                <th>Date</th>
                <td>{{ $invoice->invoice_date }}</td>
            </tr>
            <tr>
                <th>Due Date</th>
                <td>{{ $invoice->due_date }}</td>
                <th>Status</th>
                <td><span class="status {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
            </tr>
        </table>
        <table class="info-table">
            <tr>
                <th>Student</th>
                <td>{{ $invoice->studentAdmission->student_name }}</td>
                <th>Guardian</th>
                <td>{{ $invoice->studentAdmission->guardian_name }}</td>
            </tr>
            <tr>
                <th>Seat</th>
                <td>{{ $invoice->studentAdmission->seat->seat_number ?? '-' }}</td>
                <th>Room</th>
                <td>{{ $invoice->studentAdmission->seat->room_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Slots</th>
                <td colspan="3">
                    @foreach($invoice->studentAdmission->slots as $slot)
                        {{ $slot->name }} ({{ $slot->start_time }}-{{ $slot->end_time }})@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
        </table>
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <th>Amount</th>
                    <td>₹{{ number_format($invoice->amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td>₹{{ number_format($invoice->discount, 2) }}</td>
                </tr>
                <tr>
                    <th>Net</th>
                    <td><strong>₹{{ number_format($invoice->net_amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
        <h4>Payments</h4>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Txn ID</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date }}</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_mode }}</td>
                        <td>{{ $payment->transaction_id }}</td>
                        <td>{{ $payment->remark }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">No payments yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">
            <p>Thank you for choosing {{ env('APP_NAME', 'Self Study Library') }}.<br>For queries, contact us at {{ env('APP_MOBILE', '') }}</p>
        </div>
    </div>
</body>

</html>
