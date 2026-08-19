@extends('layouts.app')
@section('title', 'Milestone Remarks')
@section('content')
<div class="container-fluid">
    <h1>Milestone Remarks for Project: {{ $project->name }}</h1>
    @forelse($project->milestones as $milestone)
        <div class="border p-2 mb-2">
            <strong>{{ $milestone->title }}</strong>
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
@endsection
