@extends('layouts.app')
@section('title', 'Create Experience Letter')
@section('content')

    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Experience Letter</h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="{{ route('experience-letters.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if (auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('experience-letters.store') }}">
                        @csrf
                        @method('POST')

                        <div class="row date-fields-row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assign Users</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
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
                                <label class="form-label">Skills You Learned</label>
                                <input type="text" name="skill" class="form-control" value="{{ old('skills') }}"
                                    required>
                                @error('skills')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" name="duration" class="form-control" placeholder="example: 2 month"
                                    value="{{ old('duration') }}" required>
                                @error('duration')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3  date-field">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3  date-field">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                    required>
                                @error('end_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3  date-field">
                                <label class="form-label">Date of Issue</label>
                                <input type="date" name="issue_date" class="form-control"
                                    value="{{ old('issue_date') }}" required>
                                @error('issue_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-success">Create</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
