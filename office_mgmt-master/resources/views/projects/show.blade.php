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
    <div class="card mb-3">
        <div class="card-header">Project Remarks</div>
        <div class="card-body table-responsive">
            <form method="POST" action="{{ route('project-remarks.store') }}" class="mb-3">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <div class="form-row row">
                    <div class="form-group col-md-7">
                        <label for="remark_text">Remark Text</label>
                        <input type="text" id="remark_text" name="remark_text" class="form-control" placeholder="Add a remark..." required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="remark_type">Remark Type</label>
                        <select id="remark_type" name="remark_type" class="form-control" required>
                            <option value="internal">Internal</option>
                            <option value="issue">Issue</option>
                            <option value="feedback">Feedback</option>
                            <option value="client feedback">Client Feedback</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Add Remark</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Remark</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($project->remarks as $remark)
                    <tr>
                        <td>{{ $remark->remark_text }}</td>
                        <td>{{ $remark->remark_type }}</td>
                        <td>{{ $remark->user?->name ?? ('User #'.$remark->user_id) }}</td>
                        <td>
                            <a href="{{ route('project-remarks.edit', $remark->id) }}" class="btn btn-xs btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('project-remarks.destroy', $remark->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-sm btn-danger" onclick="return confirm('Delete this remark?')"><i class="fas fa-trash"></i></button>
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
