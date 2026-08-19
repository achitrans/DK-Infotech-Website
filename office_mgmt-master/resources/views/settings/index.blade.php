@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Settings</h2>
        </div>
        <div class="card-body table-responsive">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr>
                        <td>{{ $setting->name }}</td>
                        <td>{{ $setting->value }}</td>
                        <td>
                            <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
