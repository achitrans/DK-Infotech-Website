@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Task Details</h4>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Task Name:</strong> {{ $task->task_name }}</li>
                <li class="list-group-item"><strong>Description:</strong> {{ $task->description }}</li>
                <li class="list-group-item"><strong>Assigned To:</strong> {{ $task->assignedTo?->name ?? ('User #'.$task->assigned_to) }}</li>
                <li class="list-group-item"><strong>Status:</strong> {{ $task->status }}</li>
                <li class="list-group-item"><strong>Start Date:</strong> {{ $task->start_date }}</li>
                <li class="list-group-item"><strong>Due Date:</strong> {{ $task->due_date }}</li>
                <li class="list-group-item"><strong>Document:</strong>
                    @if($task->doc_path)
                        <a href="{{ Storage::url($task->doc_path) }}" target="_blank">View Document</a>
                    @else
                        N/A
                    @endif
                </li>
                <li class="list-group-item"><strong>Created By:</strong> {{ $task->createdBy?->name ?? ('User #'.$task->created_by) }}</li>
                <li class="list-group-item"><strong>Updated By:</strong> {{ $task->updatedBy?->name ?? ('User #'.$task->updated_by) }}</li>
                <li class="list-group-item"><strong>Created At:</strong> {{ $task->created_at }}</li>
                <li class="list-group-item"><strong>Updated At:</strong> {{ $task->updated_at }}</li>
            </ul>
            <a href="{{ route('project_tasks.edit', $task->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('projects.show', $task->project_id) }}" class="btn btn-secondary">Back to Project</a>
        </div>
    </div>

@php
$rootComments = $task->comments()->whereNull('parent_id')->with('user')->latest()->get();
@endphp
@include('project_task_comments._create_form', ['task' => $task])
@include('project_task_comments._comments_list', ['comments' => $rootComments])
</div>
@endsection
