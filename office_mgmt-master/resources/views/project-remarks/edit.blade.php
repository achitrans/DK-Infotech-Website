@extends('layouts.app')
@section('title', 'Edit Project Remark')
@section('content')
<div class="container-fluid">
    <h1>Edit Remark of Project: {{ $remark->project->name }}</h1>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('project-remarks.update', $remark->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="project_id" value="{{ $remark->project_id }}">
                <div class="form-group">
                    <label>Remark</label>
                    <input type="text" name="remark_text" class="form-control" value="{{ old('remark_text', $remark->remark_text) }}" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="remark_type" class="form-control" required>
                        <option value="internal" {{ old('remark_type', $remark->remark_type) == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="issue" {{ old('remark_type', $remark->remark_type) == 'issue' ? 'selected' : '' }}>Issue</option>
                        <option value="feedback" {{ old('remark_type', $remark->remark_type) == 'feedback' ? 'selected' : '' }}>Feedback</option>
                        <option value="client feedback" {{ old('remark_type', $remark->remark_type) == 'client feedback' ? 'selected' : '' }}>Client Feedback</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Update Remark</button>
                <a href="{{ route('projects.show', $remark->project_id) }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
