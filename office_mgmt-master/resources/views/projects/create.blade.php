@extends('layouts.app')
@section('title', 'Add Project')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Add Project</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.store') }}">
                            @csrf
                            <span class="text-warning fs-4">Basic Information</span>
                            <div class=" row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>
                                </div>
                                <div class="form-group my-2 col-md-6">
                                    <label>Description</label>
                                    <textarea name="description" required class="form-control" rows="4">{{ old('description') }}</textarea>
                                </div>
                            </div>


                            <div class=" row">
                                <div class="form-group my-2 col-md-6">
                                    <label>Client</label>
                                    <select name="client_id" required class="form-control">
                                        <option value="">-- Select Client --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}
                                                ({{ $client->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (auth()->user()->isAdmin())
                                    <div class="form-group my-2 col-md-6">
                                        <label>Assign User</label>
                                        <select name="user_id" class="form-control" required>
                                            <option value="">-- Select User --</option>
                                            @foreach (\App\Models\User::employees()->get() as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                                    ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="row date-fields-row">

                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" required class="form-control"
                                            value="{{ old('start_date') }}">
                                    </div>
                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control"
                                            value="{{ old('end_date') }}">
                                    </div>
                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>Due Date</label>
                                        <input type="date" name="due_date" required class="form-control"
                                            value="{{ old('due_date') }}">
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            @foreach (\App\Models\Project::statusOptions() as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ old('status') == $key ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>Budget</label>
                                        <input type="number" step="1" name="budget" class="form-control"
                                            value="{{ old('budget', 0) }}">
                                    </div>
                                    <div class="form-group my-2 col-md-4 date-field">
                                        <label>Tawk Code</label>
                                        <input type="text" id="tawk_code" name="tawk_code" class="form-control"
                                            value="{{ old('tawk_code') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success w-25">Create Project</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
