@extends('layouts.app')
@section('title', 'Google Meetings')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (!$googleConnected)
                    <div class="alert alert-warning d-flex align-items-center justify-content-between shadow-sm mb-4"
                        role="alert">
                        <div>
                            <i class="fas fa-exclamation-triangle fs-4 me-2"></i>
                            <strong>Google Account Disconnected:</strong> Connect Google Account to enable automatic Google
                            Meet video room creation for meetings.
                        </div>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('google.connect') }}" class="btn btn-sm btn-dark text-nowrap">
                                <i class="fab fa-google me-1"></i> Connect Now
                            </a>
                        @endif
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-video me-2"></i> Google Meetings
                        </h4>
                        <div>
                            @if (auth()->user()->isAdmin())
                                @if (!$googleConnected)
                                    <a href="{{ route('google.connect') }}" class="btn btn-outline-danger me-2"
                                        title="Connect Google Calendar Account">
                                        <i class="fab fa-google me-1"></i> Connect Google Calendar
                                    </a>
                                @else
                                    <span class="badge bg-success me-2 py-2 px-3" title="Connected Google Account">
                                        <i class="fab fa-google me-1"></i> {{ $googleEmail ?? 'Connected' }}
                                    </span>
                                    <a href="{{ route('google.disconnect') }}" class="btn btn-outline-secondary btn-sm me-2"
                                        onclick="return confirm('Disconnect Google Calendar account?')"
                                        title="Disconnect Google Calendar">
                                        Disconnect
                                    </a>
                                @endif
                            @endif
                            @if ($googleConnected)
                                <a href="{{ route('meetings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Schedule Meeting
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('meetings.index') }}" class="row g-3 mb-4">
                            <div class="row date-fields-row">

                                <div class="form-group my-2 col-md-3 date-field">

                                    <input type="text" name="title" class="form-control"
                                        placeholder="Search by Title..." value="{{ request('title') }}">
                                </div>
                                <div class="form-group my-2 col-md-3 date-field">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                            Scheduled</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>
                                <div class="form-group my-2 col-md-3 date-field">
                                    <input type="date" name="date" class="form-control"
                                        value="{{ request('date') }}">
                                </div>

                                <div class="form-group my-2 col-md-3">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Date & Time</th>
                                        <th>Client / Guest</th>
                                        <th>Related To</th>
                                        <th>Google Meet Link</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($meetings as $meeting)
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $meeting->title }}</strong>
                                                @if ($meeting->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($meeting->description, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                                {{ $meeting->start_time->format('d M Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $meeting->start_time->format('h:i A') }} -
                                                    {{ $meeting->end_time->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if ($meeting->client)
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-user me-1 text-info"></i>
                                                        {{ $meeting->client->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($meeting->project)
                                                    <span class="badge bg-info text-white">Project:
                                                        {{ $meeting->project->name }}</span>
                                                @elseif($meeting->inquiry)
                                                    <span class="badge bg-warning text-dark">Inquiry
                                                        #{{ $meeting->inquiry->id }}</span>
                                                @elseif($meeting->interview)
                                                    <span class="badge bg-secondary text-white">Interview:
                                                        {{ $meeting->interview->name }}</span>
                                                @else
                                                    <span class="text-muted">General</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($meeting->meet_link)
                                                    <a href="{{ $meeting->meet_link }}" target="_blank"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-video me-1"></i> Join Meet
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Pending API</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($meeting->status == 'scheduled')
                                                    <span class="badge-status badge-status-success"><i
                                                            class="fas fa-clock"></i> Scheduled</span>
                                                @elseif($meeting->status == 'completed')
                                                    <span class="badge-status badge-status-info"><i
                                                            class="fas fa-check-circle"></i> Completed</span>
                                                @else
                                                    <span class="badge-status badge-status-danger"><i
                                                            class="fas fa-times-circle"></i> Cancelled</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('meetings.show', $meeting->id) }}"
                                                    class="btn btn-sm btn-info text-white me-1" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if ($meeting->client && !empty($meeting->client->mobile))
                                                    <form method="POST"
                                                        action="{{ route('meetings.send-whatsapp', $meeting->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success me-1"
                                                            title="Send Meet Link via WhatsApp">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if (auth()->user()->isAdmin() || $meeting->created_by == auth()->id())
                                                    <a href="{{ route('meetings.edit', $meeting->id) }}"
                                                        class="btn btn-sm btn-warning me-1" title="Edit Meeting">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('meetings.destroy', $meeting->id) }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to cancel this meeting?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Cancel Meeting">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                                No scheduled Google Meetings found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
