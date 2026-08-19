@extends('layouts.app')
@section('title', 'Projects')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Projects {{ $slug }}</h1>
            </div>
        </div>
        <div class="card px-4 py-2">
            <form action="{{ route('projects_{slug}', ['slug' => $slug]) }}" method="GET"
                class="row g-2 justify-content-between align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label text-muted mb-1">Search projects</label>
                    <input type="search" name="search" value="{{ request('search', '') }}" class="form-control"
                        placeholder="Project, client or employee" />
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-1">Search</button>
                </div>
                @if (request('search'))
                    <div class="col-auto">
                        <a href="{{ route('projects_{slug}', ['slug' => $slug]) }}"
                            class="btn btn-outline-secondary mb-1">Clear</a>
                    </div>
                    <div class="col-12 text-muted small">
                        Search results include projects beyond your assigned list.
                    </div>
                @endif
            </form>
        </div>
        <div class="card ">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Client Name</th>
                            <th>Pending Tasks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->name }}
                                    @if (auth()->user()->type == 'client')
                                        <a href="{{ route('ivr.call.initiate', ['data' => \Illuminate\Support\Facades\Crypt::encrypt(json_encode(['to' => $project->user?->mobile, 'from' => $project->client?->mobile]))]) }}"
                                            class="badge badge-outline-info ivr-call-btn"
                                            data-confirm="Initiate this call?">Initiate Call</a>
                                    @else
                                        <a href="{{ route('ivr.call.initiate', ['data' => \Illuminate\Support\Facades\Crypt::encrypt(json_encode(['to' => $project->client?->mobile, 'from' => auth()->user()->mobile]))]) }}"
                                            class="badge badge-outline-info ivr-call-btn"
                                            data-confirm="Initiate this call?">Initiate Call</a>
                                    @endif
                                </td>
                                <td>{{ $project->client?->name }}</td>
                                <td>{{ $project->tasks->whereIn('status', \App\Models\ProjectTask::statusesPending())->count() }}
                                </td>
                                <td>
                                    <a href="{{ url('dashboard/project/' . $slug . '/' . $project->id) }}"
                                        class="btn btn-sm btn-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- {{ $projects->links() }} --}}

            </div>
        </div>

    </div>
@endsection

@section('scripts')
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
