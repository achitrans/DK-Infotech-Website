@extends('layouts.app')
@section('title', 'Add User')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Add User</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <span class="text-warning fs-4">Basic Information</span>

                            <div class="form-row row">

                                <div class="form-group my-2 col-md-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                            Suspended
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Barcode/RFID</label>
                                    <input type="text" name="barcode_rfid" class="form-control"
                                        value="{{ old('barcode_rfid') }}" placeholder="Enter barcode or RFID">
                                </div>
                            </div>

                            <span class="text-warning fs-4">Personal Details</span>

                            <div class="form-row row">

                                <div class="form-group my-2 col-md-6">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}"
                                        placeholder="Enter mobile number" required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Type</label>
                                    <select name="type" id="user_type" class="form-control" required>
                                        @foreach (\App\Models\User::$types as $key => $label)
                                            @if ($key !== 'client')
                                                <option value="{{ $key }}"
                                                    {{ old('type') == $key ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <span class="text-warning fs-4">Department & Work Details</span>

                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Department</label>
                                    <select name="department" id="department_select" class="form-control" required>
                                        @foreach (\App\Models\User::$departments as $key => $label)
                                            @if ($key !== 'client')
                                                <option value="{{ $key }}"
                                                    {{ old('department') == $key ? 'selected' : '' }}>{{ $label }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6 @if (Auth::user()->isAssociate()) d-none @endif">
                                    <label>Position</label>
                                    <select name="position" class="form-control">
                                        <option value="">Select Position</option>
                                        @foreach (\App\Models\User::$positions as $label)
                                            <option value="{{ $label }}"
                                                {{ old('position') == $label ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row row">
                                <div class="form-group my-2 col-md-6 @if (Auth::user()->isAssociate()) d-none @endif">
                                    <label>Work Location</label>
                                    <select name="work_location" class="form-control" required>
                                        @foreach (\App\Models\User::$workLocations as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('work_location') == $key ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Password</label>
                                    <input type="text" name="password" class="form-control"
                                        value="{{ env('DEFAULT_PASSWORD') }}" required>
                                </div>
                            </div>
                            <hr>

                            <div id="userSalary">
                                <span class="text-warning fs-4">Salary & Tawk Details (Not required for Clients)</span>

                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Basic</label>
                                        <input type="number" step="0.01" name="basic" class="form-control"
                                            value="{{ old('basic', 0) }}" required>
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>HRA</label>
                                        <input type="number" step="0.01" name="hra" class="form-control"
                                            value="{{ old('hra', 0) }}">
                                    </div>
                                </div>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Conveyance</label>
                                        <input type="number" step="0.01" name="conveyance" class="form-control"
                                            value="{{ old('conveyance', 0) }}">
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Special Allowance</label>
                                        <input type="number" step="0.01" name="special_allowance" class="form-control"
                                            value="{{ old('special_allowance', 0) }}">
                                    </div>
                                </div>
                                <div class="form-row row">

                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Medical Allowance</label>
                                        <input type="number" step="0.01" name="medical_allowance"
                                            class="form-control" value="{{ old('medical_allowance', 0) }}">
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Other Allowance</label>
                                        <input type="number" step="0.01" name="other_allowance" class="form-control"
                                            value="{{ old('other_allowance', 0) }}">
                                    </div>
                                </div>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Gross Salary</label>
                                        <input type="number" step="0.01" name="gross_salary" id="gross_salary"
                                            class="form-control" value="{{ old('gross_salary', 0) }}" required readonly>
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>PF</label>
                                        <input type="number" step="0.01" name="pf" id="pf"
                                            class="form-control" value="{{ old('pf', 0) }}">
                                    </div>
                                </div>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>ESI</label>
                                        <input type="number" step="0.01" name="esi" id="esi"
                                            class="form-control" value="{{ old('esi', 0) }}">
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Professional Tax</label>
                                        <input type="number" step="0.01" name="professional_tax"
                                            id="professional_tax" class="form-control"
                                            value="{{ old('professional_tax', 0) }}">
                                    </div>
                                </div>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>TDS</label>
                                        <input type="number" step="0.01" name="tds" id="tds"
                                            class="form-control" value="{{ old('tds', 0) }}">
                                    </div>
                                    <div class="row date-fields-row">
                                    <div class="form-group my-2 col-md-6 col-md-4 date-field">
                                        <label>Effective From</label>
                                        <input type="date" name="effective_from" class="form-control"
                                            value="{{ old('effective_from', date('Y-m-d')) }}" required>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-row row">
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Net Salary</label>
                                        <input type="text" id="net_salary" class="form-control"
                                            value="{{ old('net_salary', 0) }}" readonly>
                                    </div>
                                    <div class="form-group my-2 col-md-6 col-md-4">
                                        <label>Tawk Code</label>
                                        <input type="text" id="tawk_code" name="tawk_code" class="form-control"
                                            value="{{ old('tawk_code') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function calculateGrossSalary() {
            let basic = parseFloat(document.querySelector('[name="basic"]').value) || 0;
            let hra = parseFloat(document.querySelector('[name="hra"]').value) || 0;
            let conveyance = parseFloat(document.querySelector('[name="conveyance"]').value) || 0;
            let special = parseFloat(document.querySelector('[name="special_allowance"]').value) || 0;
            let medical = parseFloat(document.querySelector('[name="medical_allowance"]').value) || 0;
            let other = parseFloat(document.querySelector('[name="other_allowance"]').value) || 0;
            let gross = basic + hra + conveyance + special + medical + other;
            document.getElementById('gross_salary').value = gross.toFixed(2);
            return gross;
        }

        function calculateNetSalary() {
            let gross = calculateGrossSalary();
            let pf = parseFloat(document.getElementById('pf').value) || 0;
            let esi = parseFloat(document.getElementById('esi').value) || 0;
            let professional_tax = parseFloat(document.getElementById('professional_tax').value) || 0;
            let tds = parseFloat(document.getElementById('tds').value) || 0;
            let net = gross - pf - esi - professional_tax - tds;
            document.getElementById('net_salary').value = net.toFixed(2);
        }
        document.querySelectorAll(
            '[name="basic"], [name="hra"], [name="conveyance"], [name="special_allowance"], [name="medical_allowance"], [name="other_allowance"], #pf, #esi, #professional_tax, #tds'
        ).forEach(function(el) {
            el.addEventListener('input', calculateNetSalary);
        });

        function handleTypeChange() {
            var typeSelect = document.getElementById('user_type');
            var departmentSelect = document.getElementById('department_select');
            var userSalaryDiv = document.getElementById('userSalary');
            if (typeSelect.value === 'client' || typeSelect.value === 'associate') {
                if (typeSelect === 'client') {
                    departmentSelect.value = 'client';
                } else {
                    departmentSelect.value = 'associate';
                }
                userSalaryDiv.style.display = 'none';
            } else {
                userSalaryDiv.style.display = '';
            }
        }
        document.getElementById('user_type').addEventListener('change', handleTypeChange);
        window.addEventListener('DOMContentLoaded', function() {
            // Set default value 0 for numeric fields if empty
            document.querySelectorAll('input[type="number"]').forEach(function(el) {
                if (el.value === '') el.value = 0;
            });
            calculateNetSalary();
            handleTypeChange();
        });
    </script>
@endsection
