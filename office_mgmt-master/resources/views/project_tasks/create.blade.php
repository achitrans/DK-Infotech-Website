@extends('layouts.app')
@section('title', 'Add Project Task')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Create Project Task</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('project_tasks.store', ['project_id' => $project_id]) }}" method="POST" enctype="multipart/form-data" class="prevent-double-submit">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project_id }}">
                <div class="mb-3">
                    <label for="task_name" class="form-label">Task Name</label>
                    <input type="text" class="form-control" id="task_name" name="task_name" value="{{ old('task_name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                </div>

                <div class="row date-fields-row">
                    <div class=" col-md-4 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date',\Carbon\Carbon::now()->toDateString()) }}" min="{{\Carbon\Carbon::now()->toDateString()}}">
                    </div>
                    <div class=" col-md-4 mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}"  min="{{\Carbon\Carbon::now()->toDateString()}}">
                    </div>
                    <div class=" col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            @foreach(\App\Models\ProjectTask::statuses() as $status)
                                <option value="{{ $status }}" {{ old('status', 'pending') == $status ? 'selected' : '' }}>{{ ucwords($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class=" col-md-12 mb-3">
                    <label for="doc_path" class="form-label">Document</label>
                    <input type="file" class="form-control" id="doc_path" name="doc_path">
                </div>
                </div>



                <button type="submit" class="btn btn-primary">Create Task</button>
            </form>
        </div>
    </div>
</div>
<script>
    // Prevent double submit for forms with class 'prevent-double-submit'
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.prevent-double-submit').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (this.dataset.submitted === 'true') {
                    e.preventDefault();
                    return false;
                }
                this.dataset.submitted = 'true';
                const btn = this.querySelector('button[type="submit"], input[type="submit"]');
                if (btn) btn.disabled = true;
            });
        });
    });
</script>
@endsection
