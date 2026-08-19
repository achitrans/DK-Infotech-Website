@extends('layouts.app')
@section('title', 'Project Milestones')
@section('content')
<div class="container-fluid">
    <h1>Milestones for Project: {{ $project->name }}</h1>
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
        </div>
    @empty
        <p>No milestones found.</p>
    @endforelse
</div>
@endsection
