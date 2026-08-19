@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Quotation</h5>
                <a href="{{ route('quotations.index') }}" class="btn btn-link btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('quotations.update', $quotation) }}">
                    @csrf
                    @method('PATCH')

                    <span class="text-warning fs-4">Quotation Info</span>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Quotation title</label>
                                <input id="title" name="title" type="text"
                                    value="{{ old('title', $quotation->title) }}"
                                    class="form-control @error('title') is-invalid @enderror" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Customer / Contact name</label>
                                <input id="name" name="name" type="text"
                                    value="{{ old('name', $quotation->name) }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="mobile">Mobile</label>
                                <input id="mobile" name="mobile" type="text"
                                    value="{{ old('mobile', $quotation->mobile) }}"
                                    class="form-control @error('mobile') is-invalid @enderror">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email"
                                    value="{{ old('email', $quotation->email) }}"
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="product_id">Product</label>
                                <select name="product_id" id="product_id"
                                    class="form-select @error('product_id') is-invalid @enderror" required>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id', $quotation->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="intro">Introduction</label>
                        <textarea id="intro" name="intro" class="form-control wysiwyg @error('intro') is-invalid @enderror"
                            rows="4">{{ old('intro', $quotation->intro) }}</textarea>
                        @error('intro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="4">{{ old('description', $quotation->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="terms">Terms</label>
                        <textarea id="terms" name="terms" class="form-control wysiwyg @error('terms') is-invalid @enderror"
                            rows="4">{{ old('terms', $quotation->terms) }}</textarea>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row date-fields-row">
                        <div class="col-md-6">
                            <div class="mb-3 date-field">
                                <label for="date" class="form-label">Quotation Date</label>
                                <input id="date" name="date" type="date"
                                    value="{{ old('date', $quotation->date) }}"
                                    class="form-control @error('date') is-invalid @enderror" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 date-field">
                                <label for="exp_date" class="form-label">Expiry Date</label>
                                <input id="exp_date" name="exp_date" type="date"
                                    value="{{ old('exp_date', $quotation->exp_date) }}"
                                    class="form-control @error('exp_date') is-invalid @enderror">
                                @error('exp_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.1.0/"
            }
        }
    </script>
    <script>
        var ckTextareaPlaceHolder = 'Enter Description';

        @if (old('description'))
            ckTextareaData = '{!! old('description') !!}';
        @else
            ckTextareaData = '{!! $product->description !!}';
        @endif
    </script>
    <script type="module" src="{{ asset('cke/ck-main.js') }}"></script>
@endsection
