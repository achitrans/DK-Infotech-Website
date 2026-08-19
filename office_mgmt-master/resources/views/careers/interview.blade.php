@extends('layouts.app')
@section('title', 'Careers')
@section('content')

    <div class="container-fluid">

        <div class="row my-5">

            @php
                $hasInterview = $career->interview_date && $career->interview_time && $career->interview_type;
                $resultExists = $career->interview_status && $career->joining_on;
            @endphp

            <form
                action="{{ $hasInterview ? route('interview.reschedule', $career->id) : route('interview.schedule', $career->id) }}"
                method="post">
                @csrf

                @if ($hasInterview)
                    @method('PUT')
                @endif

                <div class="card">

                    @if ($hasInterview)
                        <div class="col-md-12 p-3 d-flex">
                            <div>Interview Schedule Details :: </div> &nbsp;&nbsp;
                            <p><strong class="text-primary"> ID : </strong> {{ $career->interview_id }}</p> &nbsp; | &nbsp;
                            <p><strong class="text-primary"> Date : </strong> {{ $career->interview_date }}</p> &nbsp; |
                            @if (data_get($career->others, 'interview_link'))
                                <p><strong class="text-primary"> Link : </strong>
                                    {{ data_get($career->others, 'interview_link') }}</p> &nbsp; |
                            @endif
                            &nbsp;
                            <p><strong class="text-primary"> Time : </strong>
                                {{ \Carbon\Carbon::parse($career->interview_time)->format('H:i') }}
                            </p> &nbsp; | &nbsp;
                            <p><strong class="text-primary"> Mode : </strong>
                                {{ ucwords(str_replace('-', ' ', $career->interview_type)) }}
                            </p>
                        </div>
                    @endif

                    <div class="card-header">
                        {{ $hasInterview ? 'Reschedule Interview' : 'Schedule Interview' }}
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="row date-fields-row">
                                <div class="col-md-4 mb-2 date-field">
                                    <label>Interview Date</label>
                                    <input type="date" name="interview_date" class="form-control"
                                        min="{{ date('Y-m-d') }}"
                                        value="{{ old('interview_date', $career->interview_date) }}">
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Interview Time</label>
                                <input type="time" name="interview_time" class="form-control"
                                    value="{{ old('interview_time') }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label>Interview Mode</label>
                                <select name="interview_mode" class="form-control">
                                    @foreach (\App\Models\Career::interviewTypes() as $mode)
                                        <option value="{{ $mode }}"
                                            {{ old('interview_mode', $career->interview_type) == $mode ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('-', ' ', $mode)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label>Interview Link</label>
                                <input type="url" name="interview_link" class="form-control"
                                    value="{{ old('interview_link', data_get($career->others, 'interview_link', '')) }}">
                            </div>

                            <div class="col-md-12 mt-3">
                                <button class="btn {{ $hasInterview ? 'btn-primary' : 'btn-success' }}">
                                    {{ $hasInterview ? 'Reschedule' : 'Schedule' }}
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <div class="col-md-12 mt-4">
                <form action="{{ route('interview.result', $career->id) }}" method="post">
                    @csrf

                    <div class="card">

                        @if ($resultExists)
                            <div class="col-md-12 p-3 d-flex">
                                <div>Interview Results Details :: </div> &nbsp;&nbsp;
                                <p
                                    class="{{ $career->interview_status === 'Accept' ? 'text-success' : ($career->interview_status === 'Reject' ? 'text-danger' : 'text-warning') }}">
                                    <strong class="text-primary"> Status :</strong>
                                    {{ $career->interview_status }}
                                </p> &nbsp; | &nbsp;
                                <p><strong class="text-primary"> Joining Date : </strong> {{ $career->joining_on }}</p>
                                &nbsp; | &nbsp;
                                <p><strong class="text-primary"> Is_Joined : </strong>
                                    {{ $career->is_joined == 1 ? 'YES' : 'NO' }}</p>
                            </div>
                        @endif

                        <div class="card-header">Interview Results Update </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-4 mb-2">
                                    <label>Interview Status</label>
                                    <select name="interview_status" class="form-control">
                                        <option value="Accept"
                                            {{ old('interview_status', $career->interview_status) == 'Accept' ? 'selected' : '' }}>
                                            Accept</option>
                                        <option value="Hold"
                                            {{ old('interview_status', $career->interview_status) == 'Hold' ? 'selected' : '' }}>
                                            Hold</option>
                                        <option value="Reject"
                                            {{ old('interview_status', $career->interview_status) == 'Reject' ? 'selected' : '' }}>
                                            Reject</option>
                                    </select>
                                </div>

                                <div class="row date-fields-row">
                                    <div class="col-md-4 mb-2 date-field">
                                        <label>Joining Date</label>
                                        <input type="date" name="joining_on" class="form-control"
                                            min="{{ date('Y-m-d') }}"
                                            value="{{ old('joining_on', $career->joining_on) }}">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Is Joined?</label>
                                    <select name="is_joined" class="form-control">
                                        <option value="0"
                                            {{ old('is_joined', $career->is_joined) == 0 ? 'selected' : '' }}>
                                            No
                                        </option>
                                        <option value="1"
                                            {{ old('is_joined', $career->is_joined) == 1 ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4 mt-3">
                                    <button class="btn btn-success">Update Results</button>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>

            </div>


        </div>
    </div>

@endsection
