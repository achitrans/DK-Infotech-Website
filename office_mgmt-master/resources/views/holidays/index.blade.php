@extends('layouts.app')
@section('title', 'Holidays')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Holiday List</span>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('holidays.create') }}" class="btn btn-success btn-sm">Add Holiday</a>
            @endif
        </div>
        <div class="card-body table-responsive">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        @if(auth()->user()->isAdmin())
                        <th>Optional?</th>
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @forelse($holidays as $holiday)
                    <tr>
                        <td>{{ $holiday->date->format('Y-m-d') }}</td>
                        <td>{{ $holiday->type }}</td>
                        <td>{{ $holiday->description }}</td>
                        @if(auth()->user()->isAdmin())
                        <td>{{ $holiday->is_optional ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this holiday?')">Delete</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No holidays found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
