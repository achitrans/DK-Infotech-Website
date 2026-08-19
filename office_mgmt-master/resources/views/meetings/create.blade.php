@extends('layouts.app')
@section('title', 'Schedule Google Meeting')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-calendar-plus me-2"></i> Schedule New Google Meeting
                        </h4>
                        <a href="{{ route('meetings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Meetings
                        </a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('meetings.store') }}">
                            @csrf

                            <span class="text-warning fs-5 fw-bold">1. Meeting Type & Attendees</span>

                            <!-- Primary Meeting For Switch -->
                            <div class="row g-3 my-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Meeting For <span class="text-danger">*</span></label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="meeting_for" id="for_client"
                                            value="client"
                                            {{ old('meeting_for', $meetingFor ?? 'client') == 'client' ? 'checked' : '' }}
                                            autocomplete="off">
                                        <label class="btn btn-outline-primary py-2" for="for_client">
                                            <i class="fas fa-user-tie me-1"></i> Client Meeting
                                        </label>

                                        <input type="radio" class="btn-check" name="meeting_for" id="for_other"
                                            value="other"
                                            {{ old('meeting_for', $meetingFor ?? 'client') == 'other' ? 'checked' : '' }}
                                            autocomplete="off">
                                        <label class="btn btn-outline-primary py-2" for="for_other">
                                            <i class="fas fa-users me-1"></i> Other / Internal Meeting
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Section -->
                            <div id="clientSection" class="p-3 border rounded bg-light mb-3">
                                <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-tie me-1"></i> Client Details &
                                    Project</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Select Client</label>
                                        <select name="client_id" id="client_select" class="form-control select2">
                                            <option value="">-- Select Client (Optional) --</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ old('client_id', $selectedClientId ?? '') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }} ({{ $client->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Client Project</label>
                                        <select name="project_id" id="project_select" class="form-control select2">
                                            <option value="">-- Select Project (Optional) --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}"
                                                    data-client-id="{{ $project->client_id }}"
                                                    {{ old('project_id', $selectedProjectId ?? '') == $project->id ? 'selected' : '' }}>
                                                    {{ $project->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Section -->
                            <div id="otherSection" class="p-3 border rounded bg-light mb-3" style="display: none;">
                                <h6 class="text-primary fw-bold mb-3"><i class="fas fa-users me-1"></i> Inquiry & Candidate
                                    Interview Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Attach Inquiry (Optional)</label>
                                        <select name="inquiry_id" id="inquiry_select" class="form-control select2">
                                            <option value="">-- None --</option>
                                            @foreach ($inquiries as $inquiry)
                                                <option value="{{ $inquiry->id }}"
                                                    {{ old('inquiry_id', $selectedInquiryId ?? '') == $inquiry->id ? 'selected' : '' }}>
                                                    Inquiry #{{ $inquiry->id }} - {{ $inquiry->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Attach Candidate Interview (Optional)</label>
                                        <select name="interview_id" id="interview_select" class="form-control select2">
                                            <option value="">-- None --</option>
                                            @foreach ($interviews as $interview)
                                                <option value="{{ $interview->id }}"
                                                    {{ old('interview_id', $selectedInterviewId ?? '') == $interview->id ? 'selected' : '' }}>
                                                    {{ $interview->name }} ({{ $interview->post }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @if (!auth()->user()->isClient())
                                <!-- Additional Guest Emails -->
                                <div class="row g-3 my-2">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Additional Guest Emails (Comma
                                            Separated)</label>
                                        <input type="text" name="additional_attendees" class="form-control"
                                            value="{{ old('additional_attendees') }}"
                                            placeholder="e.g. john@example.com, sara@example.com">
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4">
                            <span class="text-warning fs-5 fw-bold">2. Meeting Details</span>
                            <div class="row g-3 my-2">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Meeting Subject / Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" id="meeting_title" class="form-control"
                                        value="{{ old('title') }}" placeholder="e.g. Project Requirement Discussion"
                                        required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Description / Agenda</label>
                                    <textarea name="description" id="meeting_description" class="form-control" rows="3"
                                        placeholder="Enter meeting agenda or notes...">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <hr class="my-4">
                            <span class="text-warning fs-5 fw-bold">3. Date & Time</span>
                            <div class="row g-3 my-2">

                                <div class="row date-fields-row">
                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label class="form-label fw-semibold">Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="date" class="form-control"
                                            value="{{ old('date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Start Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" name="time" class="form-control"
                                        value="{{ old('time', '10:00') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Duration (Minutes) <span
                                            class="text-danger">*</span></label>
                                    <select name="duration" class="form-control" required>
                                        <option value="30" {{ old('duration', '30') == '30' ? 'selected' : '' }}>30
                                            Minutes</option>
                                        <option value="45" {{ old('duration') == '45' ? 'selected' : '' }}>45 Minutes
                                        </option>
                                        <option value="60" {{ old('duration') == '60' ? 'selected' : '' }}>1 Hour
                                        </option>
                                        <option value="90" {{ old('duration') == '90' ? 'selected' : '' }}>1.5 Hours
                                        </option>
                                        <option value="120" {{ old('duration') == '120' ? 'selected' : '' }}>2 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            @if (!$googleConnected)
                                <div class="alert alert-warning d-flex align-items-center justify-content-between my-3">
                                    <div>
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Google Calendar is not connected yet.</strong> Meetings scheduled now will
                                        be saved, but Google Meet links will only generate after connecting Google Calendar.
                                    </div>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('google.connect') }}" class="btn btn-sm btn-dark text-nowrap">
                                            <i class="fab fa-google me-1"></i> Connect Google Calendar
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-1"></i> A <strong>Google Meet video link</strong> will
                                    automatically be created via Google Calendar API and sent to the client via WhatsApp.
                                </div>
                            @endif

                            <div class="form-check form-switch my-3 p-3 bg-light rounded border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="send_notifications"
                                    id="send_notifications" value="1"
                                    {{ old('send_notifications', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="send_notifications">
                                    <i class="fas fa-bell me-1 text-warning"></i> Send Google Calendar Email & WhatsApp
                                    Notifications to Attendees
                                </label>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-video me-1"></i> Schedule & Generate Google Meet
                                </button>
                                <a href="{{ route('meetings.index') }}" class="btn btn-secondary px-4">Cancel</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('meeting_title');
            const descInput = document.getElementById('meeting_description');

            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    this.dataset.userEdited = 'true';
                });
            }
            if (descInput) {
                descInput.addEventListener('input', function() {
                    this.dataset.userEdited = 'true';
                });
            }

            function autoDraftTitleAndDescription() {
                if (!titleInput || !descInput) return;

                let title = '',
                    desc = '';

                const selectedProjVal = typeof $ !== 'undefined' ? $('#project_select').val() : document
                    .getElementById('project_select')?.value;
                const selectedClientVal = typeof $ !== 'undefined' ? $('#client_select').val() : document
                    .getElementById('client_select')?.value;
                const selectedInquiryVal = typeof $ !== 'undefined' ? $('#inquiry_select').val() : document
                    .getElementById('inquiry_select')?.value;
                const selectedInterviewVal = typeof $ !== 'undefined' ? $('#interview_select').val() : document
                    .getElementById('interview_select')?.value;

                if (selectedProjVal) {
                    const text = $('#project_select option:selected').text().trim();
                    title = `Project Meeting - ${text}`;
                    desc = `Discussion and progress update on project ${text}.`;
                } else if (selectedClientVal) {
                    const text = $('#client_select option:selected').text().split('(')[0].trim();
                    title = `Client Meeting - ${text}`;
                    desc = `Meeting with client ${text}.`;
                } else if (selectedInquiryVal) {
                    const text = $('#inquiry_select option:selected').text().trim();
                    title = `Inquiry Discussion - ${text}`;
                    desc = `Discussion regarding ${text}.`;
                } else if (selectedInterviewVal) {
                    const text = $('#interview_select option:selected').text().trim();
                    title = `Candidate Interview - ${text}`;
                    desc = `Interview with candidate ${text}.`;
                }

                if (title && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
                    titleInput.value = title;
                }
                if (desc && (!descInput.value || descInput.dataset.userEdited !== 'true')) {
                    descInput.value = desc;
                }
            }

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true
                });

                $('#inquiry_select').on('change', function() {
                    if ($(this).val()) {
                        $('#interview_select').val(null).trigger('change');
                    }
                    autoDraftTitleAndDescription();
                });

                $('#interview_select').on('change', function() {
                    if ($(this).val()) {
                        $('#inquiry_select').val(null).trigger('change');
                    }
                    autoDraftTitleAndDescription();
                });

                $('#client_select, #project_select').on('change', function() {
                    autoDraftTitleAndDescription();
                });
            }

            const forClientRadio = document.getElementById('for_client');
            const forOtherRadio = document.getElementById('for_other');
            const clientSection = document.getElementById('clientSection');
            const otherSection = document.getElementById('otherSection');

            function toggleSections() {
                if (forClientRadio.checked) {
                    clientSection.style.display = 'block';
                    otherSection.style.display = 'none';
                    if (typeof $ !== 'undefined') {
                        $('#inquiry_select').val(null).trigger('change');
                        $('#interview_select').val(null).trigger('change');
                    }
                } else {
                    clientSection.style.display = 'none';
                    otherSection.style.display = 'block';
                    if (typeof $ !== 'undefined') {
                        $('#client_select').val(null).trigger('change');
                        $('#project_select').val(null).trigger('change');
                    }
                }
            }

            function filterProjectsByClient() {
                const clientSelect = document.getElementById('client_select');
                const projectSelect = document.getElementById('project_select');
                if (!clientSelect || !projectSelect) return;
                const selectedClientId = clientSelect.value;
                const projectOptions = projectSelect.querySelectorAll('option');

                projectOptions.forEach(opt => {
                    if (opt.value === '') {
                        opt.style.display = 'block';
                        return;
                    }
                    const optClientId = opt.getAttribute('data-client-id');
                    if (!selectedClientId || optClientId === selectedClientId) {
                        opt.style.display = 'block';
                    } else {
                        opt.style.display = 'none';
                        if (opt.selected) {
                            if (typeof $ !== 'undefined') {
                                $('#project_select').val(null).trigger('change');
                            } else {
                                projectSelect.value = '';
                            }
                        }
                    }
                });
            }

            forClientRadio.addEventListener('change', toggleSections);
            forOtherRadio.addEventListener('change', toggleSections);
            document.getElementById('client_select').addEventListener('change', filterProjectsByClient);

            // Initial trigger
            toggleSections();
            filterProjectsByClient();
        });
    </script>
@endsection
