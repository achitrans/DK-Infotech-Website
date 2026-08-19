@extends('layouts.app')
@section('title', 'Project Remarks')
@section('content')
<div class="container-fluid">
    <h1>Remarks for Project: {{ $project->name }}</h1>
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
</div>
@endsection
