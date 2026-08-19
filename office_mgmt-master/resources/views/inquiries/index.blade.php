@extends('layouts.app')
@section('title', 'Inquiries')
@section('head')
    <style>
        select{
            padding: 8px !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Inquiries</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('inquiries.create') }}" class="btn btn-primary mb-3 float-right">Add Inquiry</a>
            </div>
        </div>


        <!-- Filter Form -->
        <form method="GET" action="" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row date-fields-row">
                        <div class=" mb-2 col-md-3">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                placeholder="Name">
                        </div>
                        <div class=" mb-2 col-md-3">
                            <label>Phone No.</label>
                            <input type="text" name="phone" value="{{ request('phone') }}" class="form-control"
                                placeholder="Phone">
                        </div>

                        <div class=" mb-2 col-md-3">
                            <label>State</label>
                            <select name="state" class="form-control">
                                <option value="">State</option>
                                @foreach (\App\Models\State::all() as $state)
                                    <option value="{{ $state->name }}" {{ request('state') == $state->name ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" mb-2 col-md-3">
                            <label>City</label>
                            <input type="text" name="city" value="{{ request('city') }}" class="form-control"
                                placeholder="City">
                        </div>
                        <div class=" mb-2 col-md-3">
                            <label>Source</label>
                            <select name="source" class="form-control">
                                <option value="">Source</option>
                                @foreach (\App\Models\Inquiry::sources() as $key => $label)
                                    <option value="{{ $key }}" {{ request('source') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" mb-2 col-md-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Status</option>
                                @foreach (\App\Models\Inquiry::statuses() as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" mb-2 col-md-3  date-field">
                            <label>Follow Up Date</label>
                            <input type="date" name="follow_up_due" value="{{ request('follow_up_date') }}"
                                class="form-control" placeholder="Follow Up Date" title="Follow Up Date">
                        </div>
                        <div class=" mb-2 col-md-3  date-field">
                            <label>Inquiry Date</label>
                            <input type="date" name="inquiry_date" value="{{ request('inquiry_date') }}"
                                class="form-control" placeholder="Inquiry Date" title="Inquiry Date">
                        </div>
                        <div class=" mb-2 col-md-12">
                            <button type="submit" class="btn btn-secondary btn-block">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body p-0 table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Follow Up</th>
                            <th>User</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->id }}</td>
                                <td>{{ $inquiry->created_at->format('Y-m-d') }}</td>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->phone }}</td>
                                <td>{{ $inquiry->source }}</td>
                                <td>{{ substr($inquiry->message,0,30) }}</td>
                                <td>{{ $inquiry->status }}</td>
                                <td>{{ $inquiry->follow_up_due }}</td>
                                <td>{{ $inquiry->user->name }}</td>
                                <td>
                                    <a href="{{ route('inquiries.show', $inquiry->id) }}" class="btn btn-info btn-sm"><i
                                            class="fas fa-eye"></i></a>
                                    <a href="{{ route('inquiries.edit', $inquiry->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No inquiries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $inquiries->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
