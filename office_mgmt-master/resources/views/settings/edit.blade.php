@extends('layouts.app')
@section('title', 'Edit Setting')
@section('content')

<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Edit Setting: {{ $setting->name }}</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.update', $setting->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="value" class="form-label">Value</label>
                    @if(is_array($setting->possible_values) && count($setting->possible_values) > 0)
                        <select name="value" id="value" class="form-control">
                            @foreach($setting->possible_values as $option)
                                <option value="{{ $option }}" {{ $setting->value == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="value" id="value" class="form-control" value="{{ old('value', $setting->value) }}">
                    @endif
                </div>
                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
