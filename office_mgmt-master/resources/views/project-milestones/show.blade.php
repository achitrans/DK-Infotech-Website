@extends('layouts.app')
@section('title', 'Milestone Details')
@section('content')
<div class="container-fluid">
    <h1>Milestone: {{ $milestone->title }}</h1>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Status:</strong> {{ $milestone->status }}</p>
            <p><strong>Due Date:</strong> {{ $milestone->due_date }}</p>
            <p><strong>Completed Date:</strong> {{ $milestone->completed_date }}</p>
            <p><strong>Description:</strong> {{ $milestone->description }}</p>
            <a href="{{ route('project-milestones.edit', $milestone->id) }}" class="btn btn-warning">Edit Milestone</a>
            <a href="{{ route('projects.show', $milestone->project_id) }}" class="btn btn-secondary">Back to Project</a>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">Milestone Remarks</div>
        <div class="card-body table-responsive">
            <form method="POST" action="{{ route('project-milestone-remarks.store') }}" class="mb-3">
                @csrf
                <input type="hidden" name="milestone_id" value="{{ $milestone->id }}">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <input type="text" name="remark_text" class="form-control" placeholder="Add a remark..." required>
                    </div>
                    <div class="form-group col-md-3">
                        <select name="remark_type" class="form-control" required>
                            <option value="internal">Internal</option>
                            <option value="issue">Issue</option>
                            <option value="feedback">Feedback</option>
                            <option value="client feedback">Client Feedback</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-primary btn-block">Add</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-sm pt-3">
                <thead>
                    <tr>
                        <th>Remark</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($milestone->remarks as $remark)
                    <tr>
                        <td>{{ $remark->remark_text }}</td>
                        <td>{{ $remark->remark_type }}</td>
                        <td>{{ $remark->user?->name ?? ('User #'.$remark->user_id) }}</td>
                        <td>
                            <form action="{{ route('project-milestone-remarks.destroy', $remark->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-sm btn-danger" onclick="return confirm('Delete this remark?')"> <i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No remarks found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
