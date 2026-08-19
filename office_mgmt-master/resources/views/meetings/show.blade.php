@extends('layouts.app')
@section('title', 'Meeting Details - ' . $meeting->title)

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-video me-2"></i> {{ $meeting->title }}
                        </h4>
                        <div>
                            @if(auth()->user()->isAdmin() || $meeting->created_by == auth()->id())
                                <a href="{{ route('meetings.edit', $meeting->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('meetings.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <!-- Google Meet Join Banner -->
                        <div class="p-4 mb-4 rounded bg-light border border-success d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div>
                                <h5 class="text-success mb-1">
                                    <i class="fas fa-video me-2"></i> Google Meet Room Ready
                                </h5>
                                <p class="mb-0 text-muted">
                                    Scheduled for {{ $meeting->start_time->format('d M Y, h:i A') }} ({{ $meeting->start_time->diffForHumans() }})
                                </p>
                            </div>
                            <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
                                @if($meeting->meet_link)
                                    <input type="text" id="meetLinkInput" class="form-control" value="{{ $meeting->meet_link }}" readonly style="width: 250px;">
                                    <button class="btn btn-outline-secondary" onclick="copyMeetLink()" title="Copy Link">
                                        <i class="far fa-copy"></i>
                                    </button>
                                    <a href="{{ $meeting->meet_link }}" target="_blank" class="btn btn-success px-4">
                                        <i class="fas fa-external-link-alt me-1"></i> Join Meet
                                    </a>
                                @else
                                    <span class="badge bg-secondary fs-6 me-2">Meet Link Pending API</span>
                                    @if(auth()->user()->isAdmin() && !$googleConnected)
                                        <a href="{{ route('google.connect') }}" class="btn btn-sm btn-dark text-nowrap">
                                            <i class="fab fa-google me-1"></i> Connect Google Calendar
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <h5 class="text-warning fs-5 fw-bold border-bottom pb-2">Meeting Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-35 text-muted">Subject:</th>
                                        <td><strong>{{ $meeting->title }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Date:</th>
                                        <td>{{ $meeting->start_time->format('l, d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Time:</th>
                                        <td>{{ $meeting->start_time->format('h:i A') }} - {{ $meeting->end_time->format('h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Duration:</th>
                                        <td>{{ $meeting->start_time->diffInMinutes($meeting->end_time) }} Minutes</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Status:</th>
                                        <td>
                                            @if($meeting->status == 'scheduled')
                                                <span class="badge bg-success">Scheduled</span>
                                            @elseif($meeting->status == 'completed')
                                                <span class="badge bg-primary">Completed</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Host / Creator:</th>
                                        <td>{{ $meeting->creator->name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-warning fs-5 fw-bold border-bottom pb-2">Attendees & Actions</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-35 text-muted">Client:</th>
                                        <td>
                                            @if($meeting->client)
                                                <strong>{{ $meeting->client->name }}</strong>
                                                <br><small class="text-muted">{{ $meeting->client->email }} | {{ $meeting->client->mobile }}</small>
                                            @else
                                                <span class="text-muted">No Client Assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Guest Emails:</th>
                                        <td>
                                            @if(is_array($meeting->attendees) && count($meeting->attendees) > 0)
                                                <ul class="ps-3 mb-0">
                                                    @foreach($meeting->attendees as $email)
                                                        <li>{{ $email }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Related Module:</th>
                                        <td>
                                            @if($meeting->project)
                                                <span class="badge bg-info text-white">Project: {{ $meeting->project->name }}</span>
                                            @elseif($meeting->inquiry)
                                                <span class="badge bg-warning text-dark">Inquiry #{{ $meeting->inquiry->id }}</span>
                                            @elseif($meeting->interview)
                                                <span class="badge bg-secondary text-white">Interview: {{ $meeting->interview->name }}</span>
                                            @else
                                                <span class="text-muted">General Meeting</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>

                                @if($meeting->client && !empty($meeting->client->mobile))
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp Invitation</h6>
                                        <p class="small text-muted mb-2">Send Google Meet link directly to {{ $meeting->client->name }} ({{ $meeting->client->mobile }}).</p>
                                        <form method="POST" action="{{ route('meetings.send-whatsapp', $meeting->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fab fa-whatsapp me-1"></i> Send Google Meet Link via WhatsApp
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($meeting->description)
                            <hr class="my-4">
                            <h5 class="text-warning fs-5 fw-bold">Description / Notes</h5>
                            <p class="bg-light p-3 rounded text-secondary">{{ nl2br(e($meeting->description)) }}</p>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function copyMeetLink() {
            var copyText = document.getElementById("meetLinkInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            alert("Google Meet link copied to clipboard: " + copyText.value);
        }
    </script>
@endsection
