@extends('layouts.app')
@section('title', 'Edit Milestone')
@section('content')
<div class="container-fluid">
    <h1>Edit Milestone</h1>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('project-milestones.update', $milestone->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $milestone->title) }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $milestone->description) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $milestone->due_date) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Completed Date</label>
                        <input type="date" name="completed_date" class="form-control" value="{{ old('completed_date', $milestone->completed_date) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ old('status', $milestone->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in progress" {{ old('status', $milestone->status) == 'in progress' ? 'selected' : '' }}>In Progress</option>
                            {{-- <option value="paused" {{ old('status', $milestone->status) == 'paused' ? 'selected' : '' }}>Paused</option> --}}
                            <option value="completed" {{ old('status', $milestone->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on hold" {{ old('status', $milestone->status) == 'on hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Update Milestone</button>
                <a href="{{ route('project-milestones.show', $milestone->id) }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
