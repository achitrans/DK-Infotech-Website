@extends('layouts.app')
@section('title', 'Edit Leave')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Edit Leave</div>
        <div class="card-body">
            <form method="POST" action="{{ route('leaves.admin.update', $leave->id) }}">
                @csrf
                @method('PUT')
                <div class="row date-fields-row">
                <div class="mb-3  date-field">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $leave->from_date->toDateString() }}" required>
                </div>
                </div>
                <div class="row date-fields-row">
                <div class="mb-3  date-field">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $leave->to_date->toDateString() }}" required>
                </div>
                </div>
                <div class="mb-3">
                    <label for="leave_type" class="form-label">Leave Type</label>
                    <select class="form-control" id="leave_type" name="leave_type" required>
                        @foreach(\App\Models\Leave::types() as $type)
                            <option value="{{ $type }}" {{ $leave->leave_type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        @foreach(\App\Models\Leave::statuses() as $status)
                            <option value="{{ $status }}" {{ $leave->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <input type="text" class="form-control" id="reason" name="reason" value="{{ $leave->reason }}" maxlength="255">
                </div>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $leave->remarks }}" maxlength="255">
                </div>
                <button type="submit" class="btn btn-primary">Update Leave</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('from_date').addEventListener('change', function() {
    const fromDate = this.value;
    const toDateInput = document.getElementById('to_date');
    toDateInput.min = fromDate;
    if (toDateInput.value < fromDate) {
        toDateInput.value = fromDate;
    }
});
</script>
@endsection
