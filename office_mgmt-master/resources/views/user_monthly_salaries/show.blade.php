@extends('layouts.app')
@section('title', 'Monthly Salary Details')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Monthly Salary Details for {{ $user->name }} ({{ $salary->salary_month }}/{{ $salary->salary_year }})
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group mb-3">
                                <li class="list-group-item"><strong>Basic:</strong> {{ $salary->basic }}</li>
                                <li class="list-group-item"><strong>HRA:</strong> {{ $salary->hra }}</li>
                                <li class="list-group-item"><strong>Conveyance:</strong> {{ $salary->conveyance }}</li>
                                <li class="list-group-item"><strong>Special Allowance:</strong> {{ $salary->special_allowance }}</li>
                                <li class="list-group-item"><strong>Medical Allowance:</strong> {{ $salary->medical_allowance }}</li>
                                <li class="list-group-item"><strong>Other Allowance:</strong> {{ $salary->other_allowance }}</li>
                                <li class="list-group-item"><strong>Gross Salary:</strong> {{ $salary->gross_salary }}</li>
                                @if($salary->gross_deduction>0)
                                    <li class="list-group-item"><strong>Gross Deduction:</strong> {{ $salary->gross_deduction }}</li>
                                @endif
                                @if($salary->advance_total_deduction>0)
                                    <li class="list-group-item"><strong>Advance Deduction:</strong> {{ $salary->advance_total_deduction }}</li>
                                @endif

                                <li class="list-group-item"><strong>Net Salary:</strong> {{ $salary->net_salary }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group mb-3">
                                <li class="list-group-item"><strong>Total Days:</strong> {{ $salary->total_days }}</li>
                                <li class="list-group-item"><strong>Present Days:</strong> {{ $salary->present_days }}</li>
                                <li class="list-group-item"><strong>Paid Leaves:</strong> {{ $salary->paid_leaves }}</li>
                                <li class="list-group-item"><strong>Absent Days:</strong> {{ $salary->absent_days }}</li>
                                <li class="list-group-item"><strong>PF:</strong> {{ $salary->pf }}</li>
                                <li class="list-group-item"><strong>ESI:</strong> {{ $salary->esi }}</li>
                                <li class="list-group-item"><strong>Professional Tax:</strong> {{ $salary->professional_tax }}</li>
                                <li class="list-group-item"><strong>TDS:</strong> {{ $salary->tds }}</li>
                                <li class="list-group-item"><strong>LOP Days:</strong> {{ $salary->lop_days }}</li>
                                <li class="list-group-item"><strong>LOP Amount:</strong> {{ $salary->lop_amount }}</li>
                                <li class="list-group-item"><strong>Is Approved:</strong> {{ ucfirst($salary->is_approved) }}</li>
                                <li class="list-group-item"><strong>Approved At:</strong> {{ $salary->approved_at ? $salary->approved_at->format('Y-m-d') : '-' }}</li>
                                <li class="list-group-item"><strong>Approved By (User ID):</strong> {{ $salary->approved_by ?? '-' }}</li>
                                <li class="list-group-item"><strong>Payment Status:</strong> {{ ucfirst($salary->payment_status) }}</li>
                                <li class="list-group-item"><strong>Payment Date:</strong> {{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '-' }}</li>
                                <li class="list-group-item"><strong>Remarks:</strong> {{ $salary->remarks }}</li>
                                <li class="list-group-item"><strong>Payment Details:</strong> <br>
                                    <div style="margin-left: 20px">
                                    @if(count($salary->payment_details)>0)
                                        @foreach($salary->payment_details as $key=>$value)
                                            <b> {{ ucwords(str_replace('_',' ', $key)) }}:</b> {{ $value }} <br>
                                        @endforeach
                                    @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('user-monthly-salaries.index', ['month' => $salary->salary_month, 'year' => $salary->salary_year]) }}" class="btn btn-secondary btn-lg">Back to List</a>
                    </div>
                    @if(!empty($salary->advance_deductions))
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark">Advance Deductions</div>
                            <div class="card-body">
                                <p class="text-muted">Repayments that were applied when the salary was created.</p>
                                <ul class="list-group">
                                    @foreach($salary->advance_deductions as $deduction)
                                        <li class="list-group-item">
                                            <strong>Advance #{{ $deduction['advance_id'] }}</strong>: ₹{{ number_format($deduction['deducted_amount'], 2) }} deducted (remaining ₹{{ number_format($deduction['remaining'] ?? 0, 2) }}) – {{ ucwords(str_replace('_', ' ', $deduction['term_type'] ?? '')) }} term
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-3"><strong>Total Advance Deduction:</strong> ₹{{ number_format($salary->advance_total_deduction ?? 0, 2) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
