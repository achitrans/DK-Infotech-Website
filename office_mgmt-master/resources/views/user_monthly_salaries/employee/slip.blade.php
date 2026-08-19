<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ env('COMPANY_NAME') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .salary-slip {
            width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px 40px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        table th {
            background: #f0f0f0;
            color: #333;
        }

        /* borderless input fields */
        table input {
            width: 95%;
            padding: 4px;
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
        }

        .net-salary {
            font-weight: bold;
            font-size: 16px;
            text-align: right;
            margin-top: 15px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .footer div {
            text-align: center;
        }

        .btn-container {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            background: #007BFF;
            color: #fff;
            border: none;
            padding: 8px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #0056b3;
        }

        .add-btn {
            background: #28a745;
        }

        .add-btn:hover {
            background: #1e7e34;
        }

        /* Hide buttons when printing */
        @media print {
            .btn,
            .btn-container {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="salary-slip">
    <div class="header">
        <h2>{{ env('COMPANY_NAME') }}</h2>
        <p>Registered Office: {!! env('COMPANY_ADDRESS') !!}</p>
        <p>Email: {{ env('COMPANY_EMAIL') }} | Phone: {{ env('COMPANY_PHONE') }}</p>
    </div>

    <div class="section">
        <h3>Employee Details</h3>
        <table>
            <tr>
                <th>Employee Name</th>
                <td>{{ $salary->user->name }}</td>
                <th>Employee ID</th>
                <td>{{ $salary->user->employee_id }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $salary->user->position }}</td>
                <th>Department</th>
                <td>{{ ucfirst($salary->user->department) }}</td>
            </tr>
            <tr>
                <th>Date of Joining</th>
                <td></td>
                <th>Month</th>
                <td>{{ $salary->salary_month }}/{{ $salary->salary_year }}</td>
            </tr>
            <tr>
                <th>Bank A/C No.</th>
                <td>{{ $salary->user->kyc?->account_no }}</td>
                <th>IFSC.</th>
                <td>{{ $salary->user->kyc?->ifsc_code }}</td>
            </tr>
            <tr>
                <th>PAN No.</th>
                <td>{{ $salary->user->kyc?->pan_number }}</td>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th>Paid Leave(s)</th>
                <td>{{ $paidLeaves }}</td>
                <th>Un-Paid Leave(s)</th>
                <td>{{ $unpaidLeaves }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Earnings</h3>
        <table id="earningsTable">
            <tr>
                <th>Description</th>
                <th>Amount (₹)</th>
            </tr>
            <tr>
                <td>Basic Salary</td>
                <td>{{ $salary->basic }}</td>
            </tr>
        </table>

    </div>

    <div class="section">
        <h3>Deductions</h3>
        <table id="deductionsTable">
            <tr>
                <th>Description</th>
                <th>Amount (₹)</th>
            </tr>
            <tr>
                <td>Absent [ {{ $salary->lop_days }} Day(s) ]</td>
                <td>{{ $salary->lop_amount }}</td>
            </tr>
            <tr>
                <td><b>Total Deductions</b></td>
                <td><b>{{ $salary->gross_deduction }}</b></td>
            </tr>
        </table>

    </div>

    @if(!empty($salary->advance_deductions))
        <div class="section">
            <h3>Advance Repayments</h3>
            <table>
                <tr>
                    <th>Advance #</th>
                    <th>Term</th>
                    <th>Deducted</th>
                    <th>Remaining</th>
                </tr>
                @foreach($salary->advance_deductions as $deduction)
                    <tr>
                        <td>{{ $deduction['advance_id'] }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $deduction['term_type'] ?? '')) }}</td>
                        <td>₹ {{ number_format($deduction['deducted_amount'], 2) }}</td>
                        <td>₹ {{ number_format($deduction['remaining'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">Total Advance Deduction</th>
                    <th colspan="2">₹ {{ number_format($salary->advance_total_deduction ?? 0, 2) }}</th>
                </tr>
            </table>
        </div>
    @endif

    <div class="section">
        <h3>Salary Credit Info</h3>
        <table id="creditTable">
            <tr>
                <th>Month / Year</th>
                <th>Amount (₹)</th>
                <th>Payment Mode</th>
                <th>Ref No</th>
                <th>Date</th>
                <th>Remark</th>
            </tr>
            <tr>
                <td>{{ $salary->salary_month }}/{{ $salary->salary_year }}</td>
                <td>{{ $salary->net_salary}}</td>
                <td>{{ $salary->payment_details['payment_mode']}}</td>
                <td>{{ $salary->payment_details['ref_no']}}</td>
                <td>{{ $salary->payment_date }}</td>
                <td>{{ ucwords($salary->payment_status) }}</td>
            </tr>
        </table>

    </div>

    <p class="net-salary">Net Salary (In Hand): ₹ {{ $salary->net_salary }}</p>
    <p><b>In Words:</b> {{ $word }} Only</p>

    <div class="footer">
        <div>
            <img src="{{ asset('auth_sign.png') }}" alt="Authorised Sign" style="max-height: 60px;">
            <p>Employer’s Signature</p>
            <p>(Authorized Signatory)</p>
        </div>
        <div>

        </div>
    </div>

    <div class="btn-container">
        <button class="btn" onclick="window.print()">Print</button>
    </div>
</div>

</body>

</html>
