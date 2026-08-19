@extends('layouts.app')
@section('title', 'Add Inquiry')
@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Add Inquiry</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('inquiries.store') }}">
                    @csrf

                    <span class="text-warning fs-4">Personal Info</span>

                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone') }}">
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="city">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control"
                                value="{{ old('city') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="state">State <span class="text-danger">*</span></label>
                            <select name="state" id="state" class="form-control" required>
                                <option value="" disabled selected>Select State</option>
                                @foreach (\App\Models\State::all() as $state)
                                    <option value="{{ $state->name }}"
                                        {{ old('state') == $state->name ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control"
                                value="{{ old('subject') }}" required>
                        </div>
                    </div>
                    <div class="form-group col-md-12 mb-3 ">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control" rows="6">{{ old('message') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="source">Source <span class="text-danger">*</span></label>
                            <select name="source" id="source" class="form-control" required>
                                <option value="" disabled selected>Select Source</option>
                                @foreach (\App\Models\Inquiry::sources() as $key => $label)
                                    <option value="{{ $key }}" {{ old('source') == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach (\App\Models\Inquiry::statuses() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row date-fields-row">
                    <div class="form-group col-md-6 mb-3  date-field">
                        <label for="follow_up_due">Follow Up Due</label>
                        <input type="date" name="follow_up_due" id="follow_up_due" class="form-control"
                            value="{{ old('follow_up_due') }}">
                    </div>
                    </div>
                    <button type="submit" class="btn btn-success">Create Inquiry</button>
                </form>
            </div>
        </div>
    </div>
@endsection
