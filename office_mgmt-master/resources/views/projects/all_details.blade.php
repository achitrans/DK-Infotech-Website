@extends('layouts.app')
@section('title', 'All Project Details')
@section('content')
<div class="container-fluid">
    <h1>All Projects, Remarks & Milestones</h1>
    @foreach($projects as $project)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <strong>{{ $project->name }}</strong> (ID: {{ $project->id }})
                <span class="float-right">Status: {{ $project->status }}</span>
            </div>
            <div class="card-body">
                <p><strong>Department:</strong> {{ $project->department }}</p>
                <p><strong>Employee:</strong> {{ $project->user->name ?? 'N/A' }}</p>
                <p><strong>Budget:</strong> {{ $project->budget }}</p>
                <p><strong>Description:</strong> {{ $project->description }}</p>
                <p><strong>Due Date:</strong> {{ $project->due_date }}</p>
                <p><strong>Start Date:</strong> {{ $project->start_date }}</p>
                <p><strong>End Date:</strong> {{ $project->end_date }}</p>
            </div>
            <div class="card-footer">
                <h5>Remarks</h5>
                @forelse($project->remarks as $remark)
                    <div class="border p-2 mb-2">
                        <strong>{{ $remark->remark_type }}</strong>: {{ $remark->remark_text }}
                        <span class="text-muted float-right">By User #{{ $remark->user_id }}</span>
                        <form method="POST" action="{{ route('project-remarks.destroy', $remark->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger ml-2">Delete</button>
                        </form>
                    </div>
                @empty
                    <p>No remarks found.</p>
                @endforelse
                <h5 class="mt-4">Milestones</h5>
                @forelse($project->milestones as $milestone)
                    <div class="border p-2 mb-2">
                        <strong>{{ $milestone->title }}</strong> ({{ $milestone->status }})<br>
                        <span>Due: {{ $milestone->due_date }} | Completed: {{ $milestone->completed_date }}</span>
                        <p>{{ $milestone->description }}</p>
                        <form method="POST" action="{{ route('project-milestones.destroy', $milestone->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger ml-2">Delete</button>
                        </form>
                        <div class="ml-3 mt-2">
                            <strong>Milestone Remarks:</strong>
                            @forelse($milestone->remarks as $mremark)
                                <div class="border p-1 mb-1">
                                    <strong>{{ $mremark->remark_type }}</strong>: {{ $mremark->remark_text }}
                                    <span class="text-muted float-right">By User #{{ $mremark->user_id }}</span>
                                    <form method="POST" action="{{ route('project-milestone-remarks.destroy', $mremark->id) }}" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ml-2">Delete</button>
                                    </form>
                                </div>
                            @empty
                                <p>No milestone remarks.</p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p>No milestones found.</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
