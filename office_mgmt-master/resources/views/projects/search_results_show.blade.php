@extends('layouts.app')
@section('title', 'Project Details')
@section('content')
@php
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-secondary',
        'in progress' => 'bg-primary',
        'on hold' => 'bg-warning',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
        'closed' => 'bg-dark',
    ];
    return $badges[$status] ?? 'bg-light';
}
@endphp
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ $project->name }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('meetings.create', ['project_id' => $project->id]) }}" class="btn btn-outline-primary" style="margin-right: 5px">
                <i class="fas fa-video me-1"></i> Schedule Meet
            </a>
            @if (!auth()->user()->isClient())
            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-outline-success">Edit</a>
            @endif
            <a href="{{ route('projects.index') }}" class="btn btn-outline-warning" style="margin-right: 10px">Back</a>
            @if (auth()->user()->type == 'client')
                <a href="{{ route('ivr.call.initiate', ['data' => \Illuminate\Support\Facades\Crypt::encrypt(json_encode(['to' => $project->user?->mobile, 'from' => $project->client?->mobile]))]) }}"
                   class="btn btn-outline-info ivr-call-btn"
                   data-confirm="Initiate this call?">Initiate Call</a>
            @else
                <a href="{{ route('ivr.call.initiate', ['data' => \Illuminate\Support\Facades\Crypt::encrypt(json_encode(['to' => $project->client?->mobile, 'from' =>  auth()->user()->mobile]))]) }}"
                   class="btn btn-outline-info ivr-call-btn"
                   data-confirm="Initiate this call?">Initiate Call</a>
            @endif
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Client Name:</strong> {{ $project->client?->name }}</div>
                <div class="col-md-4"><strong>Department:</strong> {{ $project->department }}</div>
                <div class="col-md-4"><strong>Employee:</strong> {{ $project->user?->name }}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Status:</strong> {{ $project->status }}</div>
                <div class="col-md-4"><strong>Budget:</strong> {{ $project->budget }}</div>
                <div class="col-md-4"><strong>Due Date:</strong> {{ $project->due_date }}</div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Start Date:</strong> {{ $project->start_date }}</div>
                <div class="col-md-4"><strong>End Date:</strong> {{ $project->end_date }}</div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-12"><strong>Description:</strong> {{ $project->description }}</div>
            </div>
        </div>
    </div>

    @if ($slug=='tasks')
        <div class="card mb-3">
        <div class="card-header">
            Project Tasks
{{--            @if (auth()->user()->isClient() || auth()->user()->isAdmin())--}}
            <a href="{{ route('project_tasks.create',[$project->id])}}" class="btn btn-md btn-primary float-right">Add Task</a>
{{--            @endif--}}
        </div>
        <div class="card-body table-responsive">
            <div class="mb-3">
                <form method="GET" class="form-inline">
                    <input type="hidden" name="milestone_status" value="{{ request('milestone_status', 'active') }}">
                    <label for="task_status" class="mr-2">Filter Tasks:</label>
                    <select name="task_status" id="task_status" class="form-control mr-2" onchange="this.form.submit()">
                        <option value="active" {{ request('task_status', 'active') == 'active' ? 'selected' : '' }}>Active (Pending, In Progress, On Hold)</option>
                        <option value="all" {{ request('task_status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('task_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in progress" {{ request('task_status') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on hold" {{ request('task_status') == 'on hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="cancelled" {{ request('task_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="closed" {{ request('task_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="completed" {{ request('task_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </form>
            </div>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Task Name</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($project->tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->task_name }}</td>
                        <td><span class="badge {{ getStatusBadge($task->status) }}">{{ $task->status }}</span></td>
                        <td>{{ $task->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $task->due_date?->format('Y-m-d') }}</td>
                        <td>
                            @if($task->doc_path)
{{--                                <a href="{{ \Illuminate\Support\Facades\Storage::url($task->doc_path) }}" target="_blank">View</a>--}}
                                <a href="{{ route('private.file.show', ['path' => encrypt($task->doc_path)]) }}" target="_blank">View</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('project_tasks.show', $task->id) }}" class="btn btn-xs btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('project_tasks.edit', $task->id) }}" class="btn btn-xs btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            @if (auth()->user()->isAdmin())
                            <form action="{{ route('project_tasks.destroy', $task->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-sm btn-danger" onclick="return confirm('Delete this task?')"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No tasks found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @elseif ($slug=='milestones')

        <div class="card mb-3">
        <div class="card-header">Add Project Milestones</div>
        <div class="card-body table-responsive">
            @if (!auth()->user()->isClient())
            <form method="POST" action="{{ route('project-milestones.store') }}" class="mb-3">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <div class="form-row row">
                    <div class="form-group col-md-6">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Milestone Title" required>
                    </div>
                    <div class="row date-fields-row col-md-3">
                        <div class="date-field" >
                            <label for="due_date">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="in progress">In Progress</option>
                            {{-- <option value="paused">Paused</option> --}}
                            <option value="completed">Completed</option>
                            <option value="on hold">On Hold</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Milestone Description"></textarea>
                    </div>
                    <div class="form-group col-md-3 d-flex align-items-end pt-2">
                        <button type="submit" class="btn btn-success btn-block">Add Milestone</button>
                    </div>
                </div>

            </form>
            @endif

        </div>
        </div>

        <div class="card mb-3">
        <div class="card-header">Filter Project Milestones</div>
        <div class="card-body table-responsive">
           <div class="mb-3">
                <form method="GET" class="form-inline">
                    <input type="hidden" name="task_status" value="{{ request('task_status', 'active') }}">
                    <label for="milestone_status" class="mr-2">Filter Milestones:</label>
                    <select name="milestone_status" id="milestone_status" class="form-control mr-2" onchange="this.form.submit()">
                        <option value="active" {{ request('milestone_status', 'active') == 'active' ? 'selected' : '' }}>Active (Pending, In Progress, On Hold)</option>
                        <option value="all" {{ request('milestone_status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ request('milestone_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in progress" {{ request('milestone_status') == 'in progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on hold" {{ request('milestone_status') == 'on hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('milestone_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </form>
            </div>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Completed Date</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($project->milestones as $milestone)
                    <tr>
                        <td>{{ $milestone->title }}</td>
                        <td><span class="badge {{ getStatusBadge($milestone->status) }}">{{ $milestone->status }}</span></td>
                        <td>{{ $milestone->due_date }}</td>
                        <td>{{ $milestone->completed_date }}</td>
                        <td>{{ $milestone->description }}</td>
                        <td>
                            <a href="{{ route('project-milestones.show', $milestone->id) }}" class="btn btn-xs btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('project-milestones.edit', $milestone->id) }}" class="btn btn-xs btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            @if (auth()->user()->isAdmin())
                            <form action="{{ route('project-milestones.destroy', $milestone->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-sm btn-danger" onclick="return confirm('Delete this milestone?')"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No milestones found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
    @if (auth()->user()->isClient())

        @if(strlen($tawkUrlSuffix)>5)
            <script type="text/javascript">
                var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
                (function(){
                    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                    s1.async=true;
                    s1.src='https://embed.tawk.to/{{$tawkUrlSuffix}}';
                    s1.charset='UTF-8';
                    s1.setAttribute('crossorigin','*');
                    s0.parentNode.insertBefore(s1,s0);
                })();
            </script>
        @endif

    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.ivr-call-btn').forEach((btn) => {
                btn.addEventListener('click', function(event) {
                    if (this.getAttribute('href') !== '#') {
                        event.preventDefault();
                        const message = this.getAttribute('data-confirm') || 'Initiate this call?';
                        if (!window.confirm(message)) {
                            return;
                        }
                        this.textContent = 'Calling...';
                        window.location = this.getAttribute('href');
                        this.setAttribute('href', '#');
                    }
                });
            });
        });
    </script>
@endsection
