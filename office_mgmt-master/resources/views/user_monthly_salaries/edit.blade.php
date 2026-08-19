@extends('layouts.app')
@section('title', 'Edit Monthly Salary')
@section('content')
    <div class="container py-4">
        <form method="POST" action="{{ route('user-monthly-salaries.update', $salary->id) }}">
            @csrf
            @method('PUT')
            <div class="row justify-content-center">
                <h4 class="mb-3">Update Monthly Salary for {{ $user->name }} ({{ $salary->salary_month }}
                    /{{ $salary->salary_year }})</h4>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white"> Salary Components </div>
                        <div class="card-body row">
                            <div class="col-md-6 form-group">
                                <label>Basic</label>
                                <input type="number" step="0.01" name="basic" id="basic" class="form-control"
                                       value="{{ old('basic', $salary->basic) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>HRA</label>
                                <input type="number" step="0.01" name="hra" id="hra" class="form-control"
                                       value="{{ old('hra', $salary->hra) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Conveyance</label>
                                <input type="number" step="0.01" name="conveyance" id="conveyance" class="form-control"
                                       value="{{ old('conveyance', $salary->conveyance) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Special Allowance</label>
                                <input type="number" step="0.01" name="special_allowance" id="special_allowance"
                                       class="form-control"
                                       value="{{ old('special_allowance', $salary->special_allowance) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Medical Allowance</label>
                                <input type="number" step="0.01" name="medical_allowance" id="medical_allowance"
                                       class="form-control"
                                       value="{{ old('medical_allowance', $salary->medical_allowance) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Other Allowance</label>
                                <input type="number" step="0.01" name="other_allowance" id="other_allowance"
                                       class="form-control"
                                       value="{{ old('other_allowance', $salary->other_allowance) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Gross Salary</label>
                                <input type="number" step="0.01" name="gross_salary" id="gross_salary"
                                       class="form-control" value="{{ old('gross_salary', $salary->gross_salary) }}"
                                       required readonly>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white"> Attendance </div>
                        <div class="card-body row">
                            <div class="col-md-6 form-group">
                                <label>Total Days</label>
                                <input type="number" name="total_days" class="form-control"
                                       value="{{ old('total_days', $salary->total_days) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Present Days</label>
                                <input type="number" name="present_days" class="form-control"
                                       value="{{ old('present_days', $salary->present_days) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Paid Leaves</label>
                                <input type="number" name="paid_leaves" class="form-control"
                                       value="{{ old('paid_leaves', $salary->paid_leaves) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Absent Days</label>
                                <input type="number" name="absent_days" class="form-control"
                                       value="{{ old('absent_days', $salary->absent_days) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white"> Deduction Components </div>
                        <div class="card-body row">
                            <div class="col-md-6 form-group">
                                <label>PF</label>
                                <input type="number" step="0.01" name="pf" id="pf" class="form-control"
                                       value="{{ old('pf', $salary->pf) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>ESI</label>
                                <input type="number" step="0.01" name="esi" id="esi" class="form-control"
                                       value="{{ old('esi', $salary->esi) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Professional Tax</label>
                                <input type="number" step="0.01" name="professional_tax" id="professional_tax"
                                       class="form-control"
                                       value="{{ old('professional_tax', $salary->professional_tax) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>TDS</label>
                                <input type="number" step="0.01" name="tds" id="tds" class="form-control"
                                       value="{{ old('tds', $salary->tds) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Loss Of Pay Days</label>
                                <input type="number" name="lop_days" class="form-control"
                                       value="{{ old('lop_days', $salary->lop_days) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Loss Of Pay Amount</label>
                                <input type="number" step="0.01" name="lop_amount" id="lop_amount" class="form-control"
                                       value="{{ old('lop_amount', $salary->lop_amount) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Gross Deduction</label>
                                <input type="number" name="gross_deduction" id="gross_deduction" class="form-control" value="0" readonly required>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white"> Payment Components </div>
                        <div class="card-body row">
                            <div class="col-md-6 form-group">
                                <label>Net Salary</label>
                                <input type="number" step="0.01" name="net_salary" id="net_salary" class="form-control"
                                       value="{{ old('net_salary', $salary->net_salary) }}" required readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Payment Status</label>
                                <select name="payment_status" class="form-control" required>
                                    <option
                                        value="unpaid" {{ old('payment_status', $salary->payment_status) == 'unpaid' ? 'selected' : '' }}>
                                        Unpaid
                                    </option>
                                    <option
                                        value="paid" {{ old('payment_status', $salary->payment_status) == 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option
                                        value="pending" {{ old('payment_status', $salary->payment_status) == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                </select>
                            </div>
                            <div class="row date-fields-row">
                            <div class="col-md-6 form-group date-field">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control"
                                       value="{{ old('payment_date', $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '') }}">
                            </div>
                            </div>
                            @foreach(\App\Models\UserMonthlySalary::$payment_details_filed as $key => $value)
                                <div class="col-md-6 form-group">
                                    <label>{{ $value }}</label>
                                    <input name="pd[{{ $key }}]" class="form-control"
                                           value="{{ old("pd.$key", $salary->payment_details[$key]) }}">
                                </div>
                            @endforeach
                            <div class="col-md-6 form-group">
                                <label>Remarks</label>
                                <input name="remarks" class="form-control" value="{{ old('remarks', $salary->remarks) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Update Monthly Salary</button>
                        <a href="{{ route('user-monthly-salaries.index', ['month' => $salary->salary_month, 'year' => $salary->salary_year]) }}"
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
            let netSalaryInput = document.getElementById('net_salary');
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
