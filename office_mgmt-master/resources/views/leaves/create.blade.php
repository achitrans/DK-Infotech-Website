@extends('layouts.app')
@section('title', 'Apply Leave')
@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">Apply for Leave</div>
            <div class="card-body">
                <form method="POST" action="{{ route('leaves.store') }}">
                    @csrf

                    <div class="row date-fields-row">
                        <div class="my-2 mb-3 col-md-6 date-field">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" min="{{ now()->addDay()->toDateString() }}"
                                value="{{ old('from_date') }}" required>
                        </div>
                        <div class="my-2 mb-3 col-md-6 date-field">
                            <label class="form-label">From Date</label>
                            <input type="date" name="to_date" class="form-control" min="{{ now()->addDay()->toDateString() }}"
                                value="{{ old('to_date') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="leave_type" class="form-label">Leave Type</label>
                        <select class="form-control" id="leave_type" name="leave_type" required>
                            @foreach (\App\Models\Leave::types() as $type)
                                <option value="{{ $type }}" {{ old('leave_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason') }}"
                            maxlength="255">
                    </div>
                    <button type="submit" class="btn btn-success">Apply Leave</button>
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
