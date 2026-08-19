@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Attendance Report</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center pt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Filter</div>
                    <div class="card-body">
                        <form method="GET" action="@if(auth()->user()->isEmployee()){{ route('attendances.employee.index') }}@else{{ route('attendances.index') }}@endif" class="mb-4">
                            <div class="row date-fields-row">
                                <div class="col-md-3">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="all" {{ request('user_id', 'all') == 'all' ? 'selected' : '' }}>
                                            All Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 date-field">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 date-field">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control"
                                        value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Report</span>
                        @if (!auth()->user()->isEmployee())
                            <button type="button" class="btn btn-warning text-white btn-sm" id="btnBulkEdit" data-bs-toggle="modal" data-bs-target="#bulkEditModal" disabled>
                                <i class="bi bi-pencil-square"></i> Bulk Update Selected
                            </button>
                        @endif
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    @if (!auth()->user()->isEmployee())
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="selectAllCheckboxes" title="Select All">
                                        </th>
                                    @endif
                                    <th>User Name</th>
                                    <th>Date</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Working Hours</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    @if (!auth()->user()->isEmployee())
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        @if (!auth()->user()->isEmployee())
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="attendance-select-cb" name="attendance_ids[]" value="{{ $attendance->id }}" form="bulkUpdateForm">
                                            </td>
                                        @endif
                                        <td>{{ $attendance->user->name ?? '-' }}</td>
                                        <td>{{ $attendance->attendance_date }}</td>
                                        <td>{{ $attendance->in_time }}</td>
                                        <td>{{ $attendance->out_time }}</td>

                                        @if ($attendance->working_hours > 8 && $attendance->status == 'present')
                                            <td class="text-success">{{ $attendance->working_hours }}</td>
                                        @else
                                            <td>{{ $attendance->working_hours }}</td>
                                        @endif

                                        @if ($attendance->status == 'absent')
                                            <td class="text-danger">{{ ucfirst($attendance->status) }}</td>
                                        @else
                                            <td class="text-success">{{ ucfirst($attendance->status) }}</td>
                                        @endif
                                        <td>{{ $attendance->remarks }}</td>
                                        @if (!auth()->user()->isEmployee())
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-attendance-btn"
                                                    data-id="{{ $attendance->id }}"
                                                    data-user="{{ $attendance->user->name ?? '-' }}"
                                                    data-date="{{ $attendance->attendance_date }}"
                                                    data-status="{{ $attendance->status }}"
                                                    data-in="{{ $attendance->in_time }}"
                                                    data-out="{{ $attendance->out_time }}"
                                                    data-hours="{{ $attendance->working_hours }}"
                                                    data-remarks="{{ $attendance->remarks }}"
                                                    data-action="{{ route('attendances.update', $attendance->id) }}"
                                                    data-bs-toggle="modal" data-bs-target="#singleEditModal">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isEmployee() ? 7 : 9 }}" class="text-center">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!auth()->user()->isEmployee())
        <!-- Single Edit Modal -->
        <div class="modal fade" id="singleEditModal" tabindex="-1" aria-labelledby="singleEditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="singleEditForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="singleEditModalLabel">Edit Attendance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee</label>
                                <input type="text" id="single_user_name" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="text" id="single_attendance_date" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" id="single_status" class="form-control" required>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="half day">Half Day</option>
                                    <option value="late">Late</option>
                                    <option value="leave">Leave</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">In Time</label>
                                    <input type="text" name="in_time" id="single_in_time" class="form-control" placeholder="HH:MM:SS (e.g. 09:00:00)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Out Time</label>
                                    <input type="text" name="out_time" id="single_out_time" class="form-control" placeholder="HH:MM:SS (e.g. 18:00:00)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Working Hours <small class="text-muted">(Leave blank to auto-calculate from times)</small></label>
                                <input type="number" step="0.1" name="working_hours" id="single_working_hours" class="form-control" placeholder="e.g. 8.0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Remarks</label>
                                <textarea name="remarks" id="single_remarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Edit Modal -->
        <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="bulkUpdateForm" method="POST" action="{{ route('attendances.bulk-update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkEditModalLabel">Bulk Edit Attendance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2">
                                <i class="bi bi-info-circle"></i> Updates will apply to all <strong id="selectedCountText">0</strong> selected records.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="keep">-- Keep Unchanged --</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="half day">Half Day</option>
                                    <option value="late">Late</option>
                                    <option value="leave">Leave</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">In Time</label>
                                    <input type="text" name="in_time" class="form-control" placeholder="Leave blank to keep unchanged">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Out Time</label>
                                    <input type="text" name="out_time" class="form-control" placeholder="Leave blank to keep unchanged">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Working Hours <small class="text-muted">(e.g. enter 8.0 for full day, or leave blank to auto-calculate)</small></label>
                                <input type="number" step="0.1" name="working_hours" class="form-control" placeholder="e.g. 8.0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Leave blank to keep unchanged"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning text-white">Apply to Selected</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAll = document.getElementById('selectAllCheckboxes');
                const checkboxes = document.querySelectorAll('.attendance-select-cb');
                const btnBulkEdit = document.getElementById('btnBulkEdit');
                const selectedCountText = document.getElementById('selectedCountText');

                function updateBulkButtonState() {
                    const checkedCount = document.querySelectorAll('.attendance-select-cb:checked').length;
                    if (btnBulkEdit) {
                        btnBulkEdit.disabled = checkedCount === 0;
                    }
                    if (selectedCountText) {
                        selectedCountText.textContent = checkedCount;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        updateBulkButtonState();
                    });
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (selectAll) {
                            selectAll.checked = checkboxes.length === document.querySelectorAll('.attendance-select-cb:checked').length;
                        }
                        updateBulkButtonState();
                    });
                });

                const editButtons = document.querySelectorAll('.edit-attendance-btn');
                editButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('singleEditForm').action = this.dataset.action;
                        document.getElementById('single_user_name').value = this.dataset.user;
                        document.getElementById('single_attendance_date').value = this.dataset.date;
                        document.getElementById('single_status').value = this.dataset.status;
                        document.getElementById('single_in_time').value = this.dataset.in || '';
                        document.getElementById('single_out_time').value = this.dataset.out || '';
                        document.getElementById('single_working_hours').value = this.dataset.hours || '';
                        document.getElementById('single_remarks').value = this.dataset.remarks || '';
                    });
                });
            });
        </script>
    @endif
@endsection
