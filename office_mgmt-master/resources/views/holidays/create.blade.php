@extends('layouts.app')
@section('title', 'Add Holiday')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">Add Holiday</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('holidays.store') }}">
                        @csrf
                        <div class="row date-fields-row">
                        <div class="form-group  date-field">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                        </div>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach(\App\Models\Holiday::$types as $key => $value)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="is_optional" class="form-check-input" id="is_optional" value="1" {{ old('is_optional') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_optional">Optional Holiday</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="{{ route('holidays.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
