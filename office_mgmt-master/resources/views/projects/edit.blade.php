@extends('layouts.app')
@section('title', 'Edit Project')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Project</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.update', $project->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" required>{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Client</label>
                            <select name="client_id" required class="form-control">
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }} ({{ $client->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" @if (!auth()->user()->isAdmin()) style="display: none;" @endif>
                            <label>Assign User</label>
                            <select name="user_id" class="form-control" required>
                                <option >-- Select User --</option>
                                @foreach(\App\Models\User::employees()->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $project->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row date-fields-row">

                            <div class="form-group col-md-4 date-field">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', $project->start_date) }}">
                            </div>
                            <div class="form-group col-md-4 date-field">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date) }}">
                            </div>
                            <div class="form-group col-md-4 date-field">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control" required value="{{ old('due_date', $project->due_date) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                @foreach(\App\Models\Project::statusOptions() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $project->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Budget</label>
                            <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $project->budget) }}">
                        </div>
                        <div class="form-group">
                            <label>Tawk Code</label>
                            <input type="text" id="tawk_code" name="tawk_code" class="form-control" value="{{ old('tawk_code', $project->tawk_code) }}" >
                        </div>
                        <button type="submit" class="btn btn-success">Update Project</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
