@php
    $method = strtoupper($method ?? 'POST');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label class="form-label">Entry name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $passwordVault->name) }}" required>
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username', $passwordVault->username) }}" required>
            @error('username')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <input type="text" name="password" class="form-control" value="{{ old('password', $passwordVault->password) }}" required>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">URL</label>
            <input type="url" name="url" class="form-control" value="{{ old('url', $passwordVault->url) }}">
            @error('url')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $passwordVault->category) }}">
            @error('category')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $passwordVault->notes) }}</textarea>
        @error('notes')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3 d-flex align-items-end">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="is_shared" name="is_shared"
                    value="1" {{ old('is_shared', $passwordVault->is_shared ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_shared">Show to other users</label>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
    </div>
</form>
