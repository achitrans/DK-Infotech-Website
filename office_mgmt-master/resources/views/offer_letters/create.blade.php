@extends('layouts.app')
@section('title', 'Create Offer Letter')
@section('content')
    @php
        $selectedCareerId = old('career_id', request('career_id'));
    @endphp
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Offer Letter</h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="{{ route('offer-letters.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('offer-letters.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Candidate (Career: Accept)</label>
                            <select name="career_id" class="form-control" required>
                                <option value="">Select candidate</option>
                                @foreach ($candidates as $candidate)
                                    <option value="{{ $candidate->id }}"
                                        {{ (string) $selectedCareerId === (string) $candidate->id ? 'selected' : '' }}>
                                        {{ $candidate->name }} ({{ $candidate->interview_id ?? 'No Interview ID' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('career_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position') }}"
                                required>
                            @error('position')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Interview By (User)</label>
                            <select name="interview_by_user_id" class="form-control">
                                <option value="">Select interviewer</option>
                                @foreach ($interviewers as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('interview_by_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('interview_by_user_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Interview By (Override Name)</label>
                            <input type="text" name="interview_by_name" class="form-control"
                                value="{{ old('interview_by_name') }}" placeholder="Optional">
                            @error('interview_by_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">CTC</label>
                            <input type="number" step="0.01" min="0" name="ctc" class="form-control"
                                value="{{ old('ctc') }}" required>
                            @error('ctc')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" step="0.01" min="0" name="salary" class="form-control"
                                value="{{ old('salary') }}">
                            @error('salary')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stipend</label>
                            <textarea name="stipend" id="" cols="30" rows="10" class="form-control">{{ old('stipend') }}</textarea>
                            @error('stipend')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row date-fields-row">

                            <div class="form-group my-2 col-md-6 date-field">
                                <label class="form-label">Date of Joining</label>
                                <input type="date" name="date_of_joining" class="form-control"
                                    value="{{ old('date_of_joining') }}" required>
                                @error('date_of_joining')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
