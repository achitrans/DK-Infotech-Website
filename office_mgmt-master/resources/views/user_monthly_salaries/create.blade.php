@extends('layouts.app')
@section('title', 'Create Monthly Salary')
@section('content')
    <div class="container py-4">
        <form method="POST"
              action="{{ route('user-monthly-salaries.store', ['userId' => $user->id, 'year' => $year, 'month' => $month]) }}">
            @csrf
            <div class="row justify-content-center">

                <h4 class="mb-3">Create Monthly Salary for {{ $user->name }} ({{ $month }}/{{ $year }})</h4>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Salary Components
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Basic</label>
                                    <input type="number" step="0.01" name="basic" id="basic" class="form-control"
                                           value="{{ old('basic', $userSalary->basic ?? 0) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>HRA</label>
                                    <input type="number" step="0.01" name="hra" id="hra" class="form-control"
                                           value="{{ old('hra', $userSalary->hra ?? 0) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Conveyance</label>
                                    <input type="number" step="0.01" name="conveyance" id="conveyance"
                                           class="form-control"
                                           value="{{ old('conveyance', $userSalary->conveyance ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Special Allowance</label>
                                    <input type="number" step="0.01" name="special_allowance" id="special_allowance"
                                           class="form-control"
                                           value="{{ old('special_allowance', $userSalary->special_allowance ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Medical Allowance</label>
                                    <input type="number" step="0.01" name="medical_allowance" id="medical_allowance"
                                           class="form-control"
                                           value="{{ old('medical_allowance', $userSalary->medical_allowance ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Other Allowance</label>
                                    <input type="number" step="0.01" name="other_allowance" id="other_allowance"
                                           class="form-control"
                                           value="{{ old('other_allowance', $userSalary->other_allowance ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Gross Salary</label>
                                    <input type="number" step="0.01" name="gross_salary" id="gross_salary"
                                           class="form-control"
                                           value="{{ old('gross_salary', $userSalary->gross_salary ?? 0) }}" required
                                           readonly>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Attendance
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Total Days</label>
                                    <input type="number" name="total_days" class="form-control"
                                           value="{{ old('total_days', $totalDays) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Present Days</label>
                                    <input type="number" name="present_days" class="form-control"
                                           value="{{ old('present_days', $presentDays) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Paid Leaves</label>
                                    <input type="number" name="paid_leaves" class="form-control"
                                           value="{{ old('paid_leaves', 0) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Absent Days</label>
                                    <input type="number" name="absent_days" class="form-control"
                                           value="{{ old('absent_days', $absentDays) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Paid Leave(s)</label>
                                    <input class="form-control" value="{{ $paidLeaves }}" readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Un-Paid Leave(s)</label>
                                    <input class="form-control" value="{{ $unpaidLeaves }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!isset($outstandingAdvances) || $outstandingAdvances->isEmpty())
                    {{-- no advances to show --}}
                @else
                    <div class="col-md-10">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                Outstanding Advance Repayments
                            </div>
                            <div class="card-body">
                                <p class="text-muted">These advances are approved and will be deducted automatically when you save this salary.</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                        <tr>
                                            <th>Term</th>
                                            <th>Requested</th>
                                            <th>Outstanding</th>
                                            <th>Scheduled Deduction</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($outstandingAdvances as $advance)
                                            @php
                                                $deduction = $advance->term_type === \App\Models\AdvanceSalary::TERM_FULL
                                                    ? $advance->outstanding_amount
                                                    : min($advance->deduction_value ?? 0, $advance->outstanding_amount);
                                            @endphp
                                            <tr>
                                                <td>{{ $advance->term_type === \App\Models\AdvanceSalary::TERM_FULL ? 'Full' : 'Fixed amount' }}</td>
                                                <td>{{ number_format($advance->amount, 2) }}</td>
                                                <td>{{ number_format($advance->outstanding_amount, 2) }}</td>
                                                <td>{{ number_format($deduction, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Deductions
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>PF</label>
                                    <input type="number" step="0.01" name="pf" id="pf" class="form-control"
                                           value="{{ old('pf', $userSalary->pf ?? 0) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>ESI</label>
                                    <input type="number" step="0.01" name="esi" id="esi"
                                           class="form-control" value="{{ old('esi', $userSalary->esi ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Professional Tax</label>
                                    <input type="number" step="0.01" name="professional_tax" id="professional_tax"
                                           class="form-control"
                                           value="{{ old('professional_tax', $userSalary->professional_tax ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>TDS</label>
                                    <input type="number" step="0.01" name="tds" id="tds"
                                           class="form-control" value="{{ old('tds', $userSalary->tds ?? 0) }}"
                                           required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Loss Of Pay Days</label>
                                    <input type="number" name="lop_days" class="form-control"
                                           value="{{ old('lop_days', 0) }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Loss Of Pay Amount</label>
                                    <input type="number" step="0.01" name="lop_amount" id="lop_amount"
                                           class="form-control" value="{{ old('lop_amount', 0) }}" required>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label>Gross Deduction</label>
                                    <input type="number" name="gross_deduction" id="gross_deduction" class="form-control" value="0" readonly required>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Payment
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Net Salary</label>
                                    <input type="number" step="0.01" name="net_salary" class="form-control"
                                           value="{{ old('net_salary', $userSalary->gross_salary ?? 0) }}" required
                                           readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Payment Status</label>
                                    <select name="payment_status" class="form-control" required>
                                        <option
                                            value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>
                                            Unpaid
                                        </option>
                                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>
                                            Paid
                                        </option>
                                        <option
                                            value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                    </select>
                                </div>
                                <div class="row date-fields-row">
                                <div class="col-md-6 form-group date-field">
                                    <label>Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control"
                                           value="{{ old('payment_date') }}">
                                </div>
                                </div>
                                @foreach(\App\Models\UserMonthlySalary::$payment_details_filed as $key => $value)
                                    <div class="col-md-6 form-group">
                                        <label>{{ $value }}</label>
                                        <input name="pd[{{ $key }}]" class="form-control"
                                               value="{{ old("pd.$key") }}">
                                    </div>
                                @endforeach
                                <div class="col-md-6 form-group">
                                    <label>Remarks</label>
                                    <input name="remarks" class="form-control" value="{{ old('remarks') }}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Save Monthly Salary</button>
                        <a href="{{ route('user-monthly-salaries.index', ['month' => $month, 'year' => $year]) }}"
                           class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </div>

            </div>

        </form>
    </div>


    <script>
        function updateGrossSalary() {
            const fields = [
                'basic',
                'hra',
                'conveyance',
                'special_allowance',
                'medical_allowance',
                'other_allowance'
            ];
            let sum = 0;
            fields.forEach(function (field) {
                let val = parseFloat(document.getElementById(field).value) || 0;
                sum += val;
            });
            document.getElementById('gross_salary').value = sum.toFixed(2);
            updateNetSalary();
        }

        function updateGrossDeduction() {
            const deductionFields = [
                'pf',
                'esi',
                'professional_tax',
                'tds',
                'lop_amount'
            ];
            let sum = 0;
            deductionFields.forEach(function (field) {
                let val = parseFloat(document.getElementById(field).value) || 0;
                sum += val;
            });
            document.getElementById('gross_deduction').value = sum.toFixed(2);
            updateNetSalary();
        }

        function updateNetSalary() {
            let gross = parseFloat(document.getElementById('gross_salary').value) || 0;
            let deduction = parseFloat(document.getElementById('gross_deduction').value) || 0;
            let net = gross - deduction;
            let netSalaryInput = document.getElementsByName('net_salary')[0];
            if (netSalaryInput) {
                netSalaryInput.value = net.toFixed(2);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            ['basic', 'hra', 'conveyance', 'special_allowance', 'medical_allowance', 'other_allowance'].forEach(
                function (id) {
                    document.getElementById(id).addEventListener('input', updateGrossSalary);
                });
            ['pf', 'esi', 'professional_tax', 'tds', 'lop_amount'].forEach(function (id) {
                document.getElementById(id).addEventListener('input', updateGrossDeduction);
            });

            // Also update net salary if gross or deduction is changed directly (edge case)
            document.getElementById('gross_salary').addEventListener('input', updateNetSalary);
            document.getElementById('gross_deduction').addEventListener('input', updateNetSalary);
            updateGrossSalary();
            updateGrossDeduction();
        });

    </script>
@endsection
