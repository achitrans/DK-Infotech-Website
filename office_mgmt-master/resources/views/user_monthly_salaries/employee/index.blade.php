@extends('layouts.app')
@section('title', 'User Monthly Salaries')
@section('content')
<div class="container py-4">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Monthly Salary List</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Month/Year</th>
                                    <th>Gross Salary</th>
                                    <th>Advance Deduction</th>
                                    <th>Net Salary</th>
                                    <th>Slip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        <td>{{ $item->salary_month}}/{{ $item->salary_year }}</td>
                                        <td>{{ $item->gross_salary }}</td>
                                        <td>{{ $item->advance_total_deduction ?? 0 }}</td>
                                        <td>{{ $item->net_salary }}</td>
                                        <td>
                                            <a href="{{ route('salaries.slip', $item->id) }}" class="btn btn-info btn-sm">Show</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
