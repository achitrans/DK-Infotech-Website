@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Dashboard</h1>

        @if (auth()->user()->isEmployee())
            @php
                $now = \Carbon\Carbon::now();
                $inHour = (int) \App\Models\Setting::where('name', 'attendance_in_time')->value('value') ?? 9;
                $outHour = (int) \App\Models\Setting::where('name', 'attendance_out_time')->value('value') ?? 22;
                $showAttendanceButtons = $now->hour >= $inHour && $now->hour < $outHour;
            @endphp
            <div class="mb-3">
                @if ($showAttendanceButtons)
                    @if (!$attendance)
                        <form method="POST" action="{{ route('attendance.mark-in') }}" class="attendance-form">
                            @csrf
                            <input type="hidden" name="latitude" class="lat-input">
                            <input type="hidden" name="longitude" class="lon-input">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-outline-primary m-2 p-3">
                                    <i class="fa fa-sign-in-alt me-2"></i>
                                    Attendance Mark In
                                </button>

                            </div>
                        </form>

                    @elseif(!$attendance->out_time && $attendance->in_time)
                        <form method="POST" action="{{ route('attendance.mark-out') }}" class="attendance-form">
                            @csrf
                            <input type="hidden" name="latitude" class="lat-input">
                            <input type="hidden" name="longitude" class="lon-input">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-outline-primary m-2 p-3">
                                    <i class="fa fa-sign-in-alt me-2"></i>
                                    Attendance Mark Out
                                </button>
                            </div>

                        </form>
                    @else
                        <span class="badge bg-primary text-white p-1 rounded-pill"><i class="fa-solid fa-user-clock"></i> </span>
                        <p class="text-success d-inline">Attendance completed for today.
                            In Time: {{ $attendance->in_time }} | Out Time: {{ $attendance->out_time }} | Working Hours:
                            {{ round($attendance->working_hours, 2) }}</p>
                    @endif
                @else
                    <span class="badge bg-primary text-white p-1 rounded-pill"><i class="fa-solid fa-user-clock"></i> </span>
                    <p class="text-danger d-inline">Attendance can be marked only between {{ $inHour }}:00 and
                        {{ $outHour }}
                        :00.</p>
                @endif
            </div>
        @endif

        <div class="row">

            @if (auth()->user()->isEmployee())
                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-uppercase mb-1">Attendance Summary <a href="{{ route('attendances.employee.index') }}"><i class="fa fa-arrow-alt-circle-right "></i></a> </h6>
                                    <div>
                                        @if ($attendance)
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill fs-6 fw-bold">
                                                <i class="fa-solid fa-circle-check me-1"></i> Present Today
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-1 rounded-pill fs-6 fw-bold">
                                                <i class="fa-solid fa-circle-xmark me-1"></i> Absent Today
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Total</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $attendanceCount['total'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold text-truncate" style='width:3.5rem;'>Present</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $attendanceCount['present'] }}  </span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Absent</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $attendanceCount['absent'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Leave</span>
                                        <span
                                            class="fw-bold text-warning fs-6 mt-1 d-block">{{ $attendanceCount['leave'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Projects Summary</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Assigned</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $project['assigned'] }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Pending</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $project['pending'] }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Completed</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $project['completed'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-rotate"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Today Follow Up</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Assigned</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $project['assigned'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Tasks Summary</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Assigned</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $taskCount['total'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Pending</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $taskCount['pending'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Completed</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $taskCount['completed'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Progress</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $taskCount['Progress'] }}</span>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Cancelled</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $taskCount['cancelled'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Hold</span>
                                        <span
                                            class="fw-bold text-warning fs-6 mt-1 d-block">{{ $taskCount['hold'] }}</span>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Closed</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $taskCount['closed'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->user()->isAdmin())

                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Projects Summary</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-5">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Total Projects</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $project['total'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Pending Mail/SMS Queue</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-5">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Pending Jobs</span>
                                        <span class="fw-bold fs-6 mt-1 d-block">{{ $pendingJobsCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-ban"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Failed Mail/SMS (Today)</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Failed Jobs (Today)</span>
                                        <span class="fw-bold {{ $failedJobsTodayCount > 0 ? 'text-danger' : 'text-success' }} fs-6 mt-1 d-block">{{ $failedJobsTodayCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->user()->isClient())
                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Projects Summary</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">

                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Pending</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $project['pending'] }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Completed</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $project['completed'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Tasks Summary</h6>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Pending</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $taskCount['pending'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Completed</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $taskCount['completed'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                  <div class="col-xl-4 col-sm-6 mb-4">
                    <div class="card border border-primary-subtle shadow-sm h-100 rounded-3 overflow-hidden bg-white">
                        <div class="p-3 bg-primary-subtle bg-opacity-25 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center">
                                <div
                                    class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm">
                                   <i class="fa-solid fa-sack-dollar"></i>
                                </div>
                                <h6 class="fw-bold text-uppercase mb-1">Invoice Summary</h6>

                            </div>
                        </div>

                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Invoices</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $metaData['client_id']['invoices'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Total</span>
                                        <span
                                            class="fw-bold text-success fs-6 mt-1 d-block">{{ $metaData['client_id']['total'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Paid</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $metaData['client_id']['paid'] }}</span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-primary border rounded p-2">
                                        <span class="d-block fw-bold">Due</span>
                                        <span
                                            class="fw-bold text-danger fs-6 mt-1 d-block">{{ $metaData['client_id']['due'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


        </div>

        <!-- Meetings & Calendar Widget Section -->
        <div class="row mt-4">
            <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-calendar-alt me-2"></i> Meetings & Schedule Calendar
                </h4>
                <div>
                    @if(auth()->user()->isAdmin())
                        @if(!($googleConnected ?? false))
                            <a href="{{ route('google.connect') }}" class="btn btn-sm btn-outline-danger me-2">
                                <i class="fab fa-google me-1"></i> Connect Google Calendar
                            </a>
                        @else
                            <span class="badge bg-success me-2 py-2 px-3" title="Connected Google Account">
                                <i class="fab fa-google me-1"></i> {{ $googleEmail ?? 'Connected' }}
                            </span>
                            <a href="{{ route('google.disconnect') }}" class="btn btn-outline-secondary btn-sm me-2" onclick="return confirm('Disconnect Google Calendar account?')">
                                Disconnect
                            </a>
                        @endif
                    @endif
                    <a href="{{ route('meetings.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Schedule Meeting
                    </a>
                </div>
            </div>

            <!-- Left: Calendar Widget -->
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar me-2 text-primary"></i> Monthly Meeting Calendar</h6>
                        <span class="badge bg-primary fs-6 px-3" id="calendarMonthYear"></span>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-danger">Sun</th>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th class="text-primary">Sat</th>
                                    </tr>
                                </thead>
                                <tbody id="calendarBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Upcoming Meetings List -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary-subtle  d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-video me-2 text-success"></i> My Scheduled Meetings</h6>
                        <a href="{{ route('meetings.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">View All</a>
                    </div>
                    <div class="card-body p-3" style="max-height: 420px; overflow-y: auto;">
                        @forelse($upcomingMeetings as $meeting)
                            <div class="p-3 mb-3 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $meeting->title }}</h6>
                                    @if($meeting->status == 'scheduled')
                                        <span class="badge bg-success">Scheduled</span>
                                    @elseif($meeting->status == 'completed')
                                        <span class="badge bg-primary">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </div>
                                <p class="small text-muted mb-2">
                                    <i class="far fa-calendar-alt me-1 text-primary"></i> {{ $meeting->start_time->format('d M Y') }}
                                    <i class="far fa-clock ms-2 me-1 text-primary"></i> {{ $meeting->start_time->format('h:i A') }} - {{ $meeting->end_time->format('h:i A') }}
                                </p>
                                @if($meeting->client)
                                    <div class="small text-secondary mb-2">
                                        <i class="fas fa-user-tie me-1"></i> Client: <strong>{{ $meeting->client->name }}</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <a href="{{ route('meetings.show', $meeting->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>
                                    @if($meeting->meet_link)
                                        <a href="{{ $meeting->meet_link }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-video me-1"></i> Join Meet
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-2 text-secondary"></i>
                                <p class="mb-0">No upcoming meetings scheduled.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const meetingDates = @json(($allMeetings ?? collect())->pluck('start_time')->map(fn($d) => $d->format('Y-m-d'))->toArray());

            function renderDashboardCalendar() {
                const today = new Date();
                let currentMonth = today.getMonth();
                let currentYear = today.getFullYear();

                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                const monthYearEl = document.getElementById('calendarMonthYear');
                if (monthYearEl) {
                    monthYearEl.innerText = monthNames[currentMonth] + ' ' + currentYear;
                }

                const firstDay = new Date(currentYear, currentMonth, 1).getDay();
                const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                const calendarBody = document.getElementById('calendarBody');
                if (!calendarBody) return;
                calendarBody.innerHTML = '';

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    let row = document.createElement('tr');
                    for (let j = 0; j < 7; j++) {
                        let cell = document.createElement('td');
                        cell.style.height = '48px';
                        cell.style.verticalAlign = 'top';

                        if (i === 0 && j < firstDay) {
                            cell.innerHTML = '';
                        } else if (date > daysInMonth) {
                            cell.innerHTML = '';
                        } else {
                            let monthStr = String(currentMonth + 1).padStart(2, '0');
                            let dayStr = String(date).padStart(2, '0');
                            let dateStr = `${currentYear}-${monthStr}-${dayStr}`;

                            let isToday = (date === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
                            let hasMeeting = meetingDates.includes(dateStr);

                            let badgeHtml = hasMeeting ? '<span class="d-block badge bg-success mt-1" style="font-size:9px;"><i class="fas fa-video me-1"></i>Meet</span>' : '';
                            let todayBg = isToday ? 'badge bg-primary rounded-circle px-2 py-1' : 'fw-bold text-dark';

                            cell.innerHTML = `<span class="${todayBg}">${date}</span>${badgeHtml}`;
                            date++;
                        }
                        row.appendChild(cell);
                    }
                    calendarBody.appendChild(row);
                    if (date > daysInMonth) break;
                }
            }
            renderDashboardCalendar();

            const geoLocationStatus = "{{ $geoLocationStatus ?? 'off' }}";

            document.querySelectorAll('.attendance-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (form.dataset.locationCaptured === 'true') {
                        return; // Allow submission
                    }

                    e.preventDefault();

                    const btn = form.querySelector('button[type="submit"]');
                    const originalText = btn.innerText;
                    btn.innerText = 'Capturing Location...';
                    btn.disabled = true;

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                form.querySelector('.lat-input').value = position.coords
                                    .latitude;
                                form.querySelector('.lon-input').value = position.coords
                                    .longitude;
                                form.dataset.locationCaptured = 'true';
                                form.submit();
                            },
                            function(error) {
                                console.warn("Geolocation error:", error.message);
                                btn.innerText = originalText;
                                btn.disabled = false;

                                if (geoLocationStatus === 'on') {
                                    alert("Error: Location is required to mark attendance. Please enable location services and try again. (" +
                                        error.message + ")");
                                } else {
                                    // Still submit if status is off, server will handle validation based on settings
                                    form.dataset.locationCaptured = 'true';
                                    form.submit();
                                }
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        console.warn("Geolocation is not supported by this browser.");
                        btn.innerText = originalText;
                        btn.disabled = false;

                        if (geoLocationStatus === 'on') {
                            alert(
                                "Error: Geolocation is not supported by your browser. Location is required to mark attendance."
                            );
                        } else {
                            form.dataset.locationCaptured = 'true';
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection
