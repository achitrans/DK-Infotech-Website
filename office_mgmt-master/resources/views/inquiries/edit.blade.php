{{-- form-row@extends('layouts.app') --}}
@extends('layouts.app')
@section('title', 'Edit Inquiry')
@section('content')
    <div class="container-fluid">
        <h1>Edit Inquiry</h1>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('inquiries.update', $inquiry->id) }}">
                    @csrf
                    @method('PUT')

                    <span class="text-warning fs-4">Personal Info</span>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $inquiry->name) }}" required>
                        </div>
                        <div class="form-group  col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $inquiry->email) }}">
                        </div>
                    </div>
                    <div class="row">

                        <div class="form-group col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $inquiry->phone) }}">
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label>State</label>
                            <input type="text" name="state" class="form-control"
                                value="{{ old('state', $inquiry->state) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label>City</label>
                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $inquiry->city) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control"
                                value="{{ old('subject', $inquiry->subject) }}">
                        </div>
                    </div>
                    <div class="form-group  col-md-12 mb-3">
                        <label>Message</label>
                        <textarea name="message" class="form-control" rows="6">{{ old('message', $inquiry->message) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label>Source</label>
                            <select name="source" class="form-control" required>
                                @foreach (\App\Models\Inquiry::sources() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('source', $inquiry->source) == $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group  col-md-6">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                @foreach (\App\Models\Inquiry::statuses() as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('status', $inquiry->status) == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row date-fields-row mb-3 ">
                        <div class="form-group  col-md-6  date-field">
                            <label>Follow Up Due</label>
                            <input type="date" name="follow_up_due" class="form-control"
                                value="{{ old('follow_up_due', $inquiry->follow_up_due ? date('Y-m-d', strtotime($inquiry->follow_up_due)) : '') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Update Inquiry</button>
                </form>
            </div>
        </div>
    </div>
@endsection
