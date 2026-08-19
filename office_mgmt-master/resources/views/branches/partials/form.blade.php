<span class="text-warning fs-4">Basic Info</span>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="display_name" class="form-label">Display name</label>
            <input id="display_name" name="display_name" type="text"
                value="{{ old('display_name', $branch->display_name ?? '') }}"
                class="form-control @error('display_name') is-invalid @enderror" required>
            @error('display_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="legal_name" class="form-label">Legal name</label>
            <input id="legal_name" name="legal_name" type="text"
                value="{{ old('legal_name', $branch->legal_name ?? '') }}"
                class="form-control @error('legal_name') is-invalid @enderror">
            @error('legal_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr>

<span class="text-warning fs-4">Tax Info</span>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="gstin" class="form-label">GSTIN</label>
            <input id="gstin" name="gstin" type="text" value="{{ old('gstin', $branch->gstin ?? '') }}"
                class="form-control @error('gstin') is-invalid @enderror">
            @error('gstin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="pan" class="form-label">PAN</label>
            <input id="pan" name="pan" type="text" value="{{ old('pan', $branch->pan ?? '') }}"
                class="form-control @error('pan') is-invalid @enderror">
            @error('pan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="tan" class="form-label">TAN</label>
            <input id="tan" name="tan" type="text" value="{{ old('tan', $branch->tan ?? '') }}"
                class="form-control @error('tan') is-invalid @enderror">
            @error('tan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="code" class="form-label">Code</label>
            <input id="code" name="code" type="text" value="{{ old('code', $branch->code ?? '') }}"
                class="form-control @error('code') is-invalid @enderror" required>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr>

<span class="text-warning fs-4">Contact Info</span>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile</label>
            <input id="mobile" name="mobile" type="text" value="{{ old('mobile', $branch->mobile ?? '') }}"
                class="form-control @error('mobile') is-invalid @enderror">
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="whatsapp_number" class="form-label">Whatsapp number</label>
            <input id="whatsapp_number" name="whatsapp_number" type="text"
                value="{{ old('whatsapp_number', $branch->whatsapp_number ?? '') }}"
                class="form-control @error('whatsapp_number') is-invalid @enderror">
            @error('whatsapp_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr>

<span class="text-warning fs-4">Manager Info</span>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $branch->email ?? '') }}"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="manager_name" class="form-label">Manager</label>
            <input id="manager_name" name="manager_name" type="text"
                value="{{ old('manager_name', $branch->manager_name ?? '') }}"
                class="form-control @error('manager_name') is-invalid @enderror">
            @error('manager_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr>

<span class="text-warning fs-4">Address Info</span>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="manager_phone" class="form-label">Manager phone</label>
            <input id="manager_phone" name="manager_phone" type="text"
                value="{{ old('manager_phone', $branch->manager_phone ?? '') }}"
                class="form-control @error('manager_phone') is-invalid @enderror">
            @error('manager_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="state" class="form-label">State</label>
            <input id="state" name="state" type="text" value="{{ old('state', $branch->state ?? '') }}"
                class="form-control @error('state') is-invalid @enderror">
            @error('state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input id="city" name="city" type="text" value="{{ old('city', $branch->city ?? '') }}"
                class="form-control @error('city') is-invalid @enderror">
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="pincode" class="form-label">Pincode</label>
            <input id="pincode" name="pincode" type="text"
                value="{{ old('pincode', $branch->pincode ?? '') }}"
                class="form-control @error('pincode') is-invalid @enderror">
            @error('pincode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<hr>
<span class="text-warning fs-4">User Assignment</span>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $branch->address ?? '') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="user_id" class="form-label">Assigned user</label>
            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                <option value="">-- pick user --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ old('user_id', $branch->user_id ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                {{ old('is_active', isset($branch) ? $branch->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active?</label>
        </div>
    </div>
</div>
