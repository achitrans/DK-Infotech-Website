@extends('layouts.app')
@section('title', 'Applicant Details')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Applicant: {{ $interest->name }}</h5>
            <div>
                <a href="{{ route('internship-interests.edit', $interest->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <a href="{{ route('internship-interests.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Email:</strong> {{ $interest->email }}</p>
                    <p><strong>Phone:</strong> {{ $interest->phone ?? '-' }}</p>
                    <p><strong>Type:</strong> {{ \App\Models\InternshipInterest::$types[$interest->type] ?? $interest->type }}</p>
                    <p><strong>Source:</strong> {{ \App\Models\InternshipInterest::$sources[$interest->source] ?? $interest->source }}</p>
                    <p><strong>Status:</strong> {{ \App\Models\InternshipInterest::$statuses[$interest->status] ?? $interest->status }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Degree:</strong> {{ $interest->degree ?? '-' }}</p>
                    <p><strong>University:</strong> {{ $interest->university ?? '-' }}</p>
                    <p><strong>Graduation Year:</strong> {{ $interest->graduation_year ?? '-' }}</p>
                    <p><strong>Availability (weeks):</strong> {{ $interest->availability_weeks ?? '-' }}</p>
                    <p><strong>Preferred Start:</strong> {{ optional($interest->start_date_preference)->toDateString() ?? '-' }}</p>
                </div>
            </div>

            <hr>
            <h6>Skills & Links</h6>
            <p>{{ $interest->skills ?? '-' }}</p>
            <p><strong>Portfolio:</strong> <a href="{{ $interest->portfolio_link }}" target="_blank">{{ $interest->portfolio_link }}</a></p>
            <p><strong>GitHub:</strong> <a href="{{ $interest->github_link }}" target="_blank">{{ $interest->github_link }}</a></p>
            <p><strong>LinkedIn:</strong> <a href="{{ $interest->linkedin }}" target="_blank">{{ $interest->linkedin }}</a></p>

            <hr>
            <h6>Resume</h6>
            @if($interest->resume_file)
                <a href="{{ asset('storage/' . $interest->resume_file) }}" class="btn btn-outline-primary" target="_blank">Download Resume</a>
            @else
                <p>No resume uploaded.</p>
            @endif

            <hr>
            <h6>Notes</h6>
            <p>{{ $interest->notes ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
