@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Consolidated Attendance Report</h2>
        <div class="row justify-content-center pt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Filter</div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('attendances.report') }}" class="mb-4">
                            <div class="row date-fields-row">
                                <div class="col-md-4 date-field">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-4 date-field">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control"
                                        value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-4 align-items-end">
                                    <button type="submit" class="btn btn-primary">Show Report</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Report</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Total Present Days</th>
                                    <th>Total Absent Days</th>
                                    <th>Total Working Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report as $row)
                                    <tr>
                                        <td>{{ $row['user']->name }}</td>
                                        <td>{{ $row['total_present'] }}</td>
                                        <td>{{ $row['total_absent'] }}</td>
                                        <td>{{ $row['total_working_hours'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No data found for selected dates.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
