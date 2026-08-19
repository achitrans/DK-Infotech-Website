@extends('layouts.app')
@section('title', 'Experience Letters')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Experience Letters</h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="{{ route('experience-letters.create') }}" class="btn btn-primary">Create Experience Letter</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name | Email</th>
                            <th>Department|Skills</th>
                            <th>Time Period</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $users)
                            <tr>
                                <td>{{ $users?->user->name ?? '-' }} | {{ $users?->user->email ?? '-' }}</td>
                                <td>{{ $users?->position ?? '-' }}| skills : ({{ $users?->skill ?? '-' }})</td>
                                <td> {{ $users?->duration ?? '-' }} ({{ $users?->start_date ?? '-' }} |
                                    {{ $users?->end_date ?? '-' }})</td>
                                <td>

                                    <a href="{{ route('experience-letters.edit', $users) }}" title="edit" class="btn btn-square btn-s btn-outline-primary light ms-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('experience-letters.print', $users) }}" target="_blank"
                                        class="btn btn-sm btn-success m-1">
                                        Experience
                                    </a>
                                    <a href="{{ route('certificate-letters.print', $users) }}" target="_blank"
                                        class="btn btn-sm btn-info">
                                        Certificate
                                    </a>


                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-danger">No experience letters found!😊</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
