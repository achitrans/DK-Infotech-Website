@extends('layouts.app')
@section('title', 'Careers')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Career</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('careers.create') }}" class="btn btn-primary">Apply Now</a>
            </div>
        </div>

        <div class="card p-3">
            <div class="card-header mb-2">Filter</div>
                <form method="GET" action="{{ route('career.index') }}">
                    <div class="row date-fields-row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Search</label>
                            <input type="search" name="search" class="form-control" placeholder="Name or mobile"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">All departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Office Location</label>
                            <select name="office_location" class="form-control">
                                <option value="">All locations</option>
                                @foreach ($officeLocations as $location)
                                    <option value="{{ $location }}"
                                        {{ request('office_location') === $location ? 'selected' : '' }}>{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 date-field">
                            <label class="form-label">Interview From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-12 col-md-3 date-field">
                            <label class="form-label">Interview To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-12 col-md-3 date-field">
                            <label class="form-label">From Date</label>
                            <input type="date" name="create_from_date" class="form-control" value="{{ request('create_from_date') }}">
                        </div>
                        <div class="col-12 col-md-3 date-field">
                            <label class="form-label">To Date</label>
                            <input type="date" name="create_to_date" class="form-control" value="{{ request('create_to_date') }}">
                        </div>

                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        <div class="card">

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email / Mobile</th>
                            <th>Full Address</th>
                            <th>Department</th>
                            <th>Skills</th>
                            <th>Office Loc..</th>
                            <th>Photo</th>
                            <th>Resume</th>
                            <th>Int.. Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($careers as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}  {{ $item->mobile ?? '-' }}</td>
                                <td>{{ $item->address ?? '-' }}, {{ $item->city ?? '-' }}, {{ $item->state->name ?? '-' }},
                                    {{ $item->pincode ?? '-' }}</td>
                                <td>{{ $item->departmentSkill->department ?? '-' }}</td>
                                <td>
                                    {{ implode(', ', is_array($item->skills) ? $item->skills : json_decode($item->skills ?? '[]', true)) }}
                                </td>

                                <td>{{ $item->office_location ?? '-' }}</td>
                                <td>
                                    @if ($item->photo)
                                        <img src="{{ Storage::url($item->photo) }}" alt="Photo" class="img-thumbnail"
                                            width="50">
                                    @else
                                        <span>No Photo</span>
                                    @endif

                                </td>
                                <td>
                                    @if ($item->resume)
                                        <a href="{{ Storage::url($item->resume) }}" target="_blank">View Resume</a>
                                    @else
                                        No Resume
                                    @endif
                                </td>
                                <td>
                                    {{-- @if ($item->interview_status == 'Accept')
                                        <span class="text-success">{{ ucfirst($item->status) }}</span>
                                    @else
                                        <span class="text-danger">{{ ucfirst($item->status) }}</span>
                                    @endif --}}
                                    {{ $item->interview_date }} <br>

                                    @if ($item->interview_status == 'Accept')
                                        <span class="text-success">{{ ucfirst($item->interview_status) }}</span>
                                    @elseif ($item->interview_status == 'Hold')
                                        <span class="text-warning">{{ ucfirst($item->interview_status) }}</span>
                                    @elseif ($item->interview_status == 'Reject')
                                        <span class="text-danger">{{ ucfirst($item->interview_status) }}</span>
                                    @else
                                        <span class="text-secondary">{{ ucfirst($item->interview_status ?? '-') }}</span>
                                    @endif

                                    {{ ucfirst($item->is_joined ? 'JOINED' : '') }}

                                </td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <a href="{{ route('career.edit', $item->id) }}" class="btn btn-square btn-s btn-outline-primary light ms-1">
                                        <i class="fas fa-edit" title="Edit"></i>
                                    </a>

                                    <form action="{{ route('career.destroy', $item->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-square btn-s btn-outline-primary light ms-1"
                                            onclick="return confirm('Want to delete\nAre you sure?')">
                                            <i class="fas fa-trash" title="Delete"></i>
                                        </button>
                                    </form>

                                    <a href="{{ route('meetings.create', ['interview_id' => $item->id]) }}" class="btn btn-square btn-s btn-outline-primary light ms-1" title="Schedule Google Meet">
                                        <i class="fas fa-video"></i>
                                    </a>

                                    <a href="{{ route('careers.interview', $item->id) }}" class="btn btn-square btn-s btn-outline-primary light ms-1">
                                        <i class="fas fa-user-check" title="interview"></i>
                                    </a>

                                    @if ($item->interview_status === 'Accept')
                                        <a href="{{ route('offer-letters.create', ['career_id' => $item->id]) }}" class="btn btn-square btn-s btn-outline-primary light ms-1">
                                            <i class="fas fa-file-alt" title="Offer Letter"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="card-footer">
                {{ $careers->appends(request()->query())->links() }}
            </div>

        </div>

    </div>
@endsection
