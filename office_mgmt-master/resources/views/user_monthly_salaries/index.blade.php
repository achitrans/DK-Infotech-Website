@extends('layouts.app')
@section('title', 'User Monthly Salaries')
@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('user-monthly-salaries.index') }}" class="form-inline justify-content-center">
                        <div class="form-group mr-2">
                            <label for="month" class="mr-2">Month</label>
                            <select name="month" id="month" class="form-control">
                                @foreach(range(1,12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label for="year" class="mr-2">Year</label>
                            <select name="year" id="year" class="form-control">
                                @for($y = date('Y')-2; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Show</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Employee List for {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Gross Salary</th>
                                    <th>Advance Deduction</th>
                                    <th>Net Salary</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    @php $salary = $monthlySalaries[$employee->id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $salary ? $salary->gross_salary : ($employee->salary->gross_salary ?? '-') }}</td>
                                        <td>{{ $salary ? $salary->advance_total_deduction : 0 }}</td>
                                        <td>{{ $salary ? $salary->net_salary : '-' }}</td>
                                        <td>
                                            @if($salary)
                                                <a href="{{ route('user-monthly-salaries.show', $salary->id) }}" class="btn btn-info btn-sm">Show</a>
                                                <a href="{{ route('user-monthly-salaries.edit', $salary->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @else
                                                <a href="{{ route('user-monthly-salaries.create', ['userId' => $employee->id, 'year' => $year, 'month' => $month]) }}" class="btn btn-success btn-sm">Create</a>
                                            @endif
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
