@extends('layouts.app')
@section('title', 'Inquiry Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Inquiry Details</h1>
            <a href="{{ route('meetings.create', ['inquiry_id' => $inquiry->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-video me-1"></i> Schedule Meet
            </a>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Name:</strong> {{ $inquiry->name }}</p>
                <p><strong>Email:</strong> {{ $inquiry->email }}</p>
                <p><strong>Phone:</strong> {{ $inquiry->phone }}</p>
                <p><strong>Subject:</strong> {{ $inquiry->subject }}</p>
                <p><strong>Message:</strong> {{ $inquiry->message }}</p>
                <p><strong>Source:</strong> {{ $inquiry->source }}</p>
                <p><strong>Status:</strong> {{ $inquiry->status }}</p>
                <p><strong>City:</strong> {{ $inquiry->city }}</p>
                <p><strong>State:</strong> {{ $inquiry->state }}</p>
                <p><strong>Created At:</strong> {{ $inquiry->created_at->diffForHumans() }}</p>
                <p><strong>Follow Up Due:</strong> {{ $inquiry->follow_up_due }}</p>
                <p><strong>User:</strong> {{ $inquiry->user->name }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Follow Ups</div>
            <div class="card-body">
                <form method="POST" action="{{ route('inquiries.addFollowUp', $inquiry->id) }}">
                    @csrf
                    <div class="row date-fields-row">

                        <div class="form-group col-md-8">
                            <input type="text" name="remarks" class="form-control" placeholder="Remarks " required>
                        </div>
                        <div class="form-group col-md-3  date-field">
                            <input type="date" name="follow_up_due" class="form-control" placeholder="Next follow up"
                                required>
                        </div>
                        <div class="form-group col-md-1">
                            <button type="submit" class="btn btn-primary btn-block">Add</button>
                        </div>
                    </div>
                </form>
                <hr>
                @forelse($inquiry->followUps as $followUp)
                    <div class="border p-2 mb-2">
                        <p class="mb-0"> <span class="text-muted">By User #{{ $followUp->user_id }}</span> ||
                            <strong>{{ $followUp->created_at->diffForHumans() }}</strong>: {{ $followUp->remarks }}
                            <br>
                            <span style="padding-left: 20px">Due: {{ $followUp->follow_up_date }}</span>
                        </p>


                    </div>
                @empty
                    <p>No follow ups found.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
