@extends('layouts.app')
@section('title', 'Edit Applicant')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit Application</div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('internship-interests.update', $interest->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $interest->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $interest->email) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" name="phone" class="form-control"
                                        value="{{ old('phone', $interest->phone) }}" required pattern="[6-9][0-9]{9}"
                                        maxlength="10" title="Please enter a valid 10-digit Indian mobile number.">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select">
                                        @foreach (\App\Models\InternshipInterest::$types as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ old('type', $interest->type) == $val ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Source</label>
                                    <select name="source" class="form-select">
                                        @foreach (\App\Models\InternshipInterest::$sources as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ old('source', $interest->source) == $val ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Graduation Year</label>
                                    <input type="number" name="graduation_year" class="form-control"
                                        value="{{ old('graduation_year', $interest->graduation_year) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Availability (weeks)</label>
                                    <input type="number" name="availability_weeks" class="form-control"
                                        value="{{ old('availability_weeks', $interest->availability_weeks) }}">
                                </div>
                                <div class="row date-fields-row">
                                <div class="col-md-4 mb-3  date-field">
                                    <label class="form-label">Preferred Start</label>
                                    <input type="date" name="start_date_preference" class="form-control"
                                        value="{{ old('start_date_preference', optional($interest->start_date_preference)->toDateString()) }}">
                                </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Skills</label>
                                <textarea name="skills" rows="3" class="form-control">{{ old('skills', $interest->skills) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">GitHub</label>
                                    <input type="url" name="github_link" class="form-control"
                                        value="{{ old('github_link', $interest->github_link) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" name="linkedin" class="form-control"
                                        value="{{ old('linkedin', $interest->linkedin) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Portfolio</label>
                                    <input type="url" name="portfolio_link" class="form-control"
                                        value="{{ old('portfolio_link', $interest->portfolio_link) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Resume (leave blank to keep current)</label>
                                <input type="file" name="resume_file" class="form-control">
                                @if ($interest->resume_file)
                                    <div class="mt-2"><a href="{{ asset('storage/' . $interest->resume_file) }}"
                                            target="_blank">Current Resume</a></div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        @foreach (\App\Models\InternshipInterest::$statuses as $val => $label)
                                            <option value="{{ $val }}"
                                                {{ old('status', $interest->status) == $val ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Consent</label>
                                    <select name="consent" class="form-select">
                                        <option value="1" {{ old('consent', $interest->consent) ? 'selected' : '' }}>
                                            Yes</option>
                                        <option value="0"
                                            {{ !old('consent', $interest->consent) ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $interest->notes) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('internship-interests.index') }}"
                                    class="btn btn-secondary mr-2">Cancel</a>
                                <button class="btn btn-primary">Save Changes</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
