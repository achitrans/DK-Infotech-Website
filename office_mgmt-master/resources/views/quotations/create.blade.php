@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">New Quotation</h5>
                <a href="{{ url()->previous() }}" class="btn btn-link btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('quotations.store') }}">
                    @csrf
                    <span class="text-warning fs-4">Quotation Info</span>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="title">Quotation title</label>
                                <input id="title" name="title" type="text" value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Customer / Contact name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}"
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
                                <input id="mobile" name="mobile" type="text" value="{{ old('mobile') }}"
                                    class="form-control @error('mobile') is-invalid @enderror">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}"
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
                                    <option value="">Select a product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
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
                            rows="4">{{ old('intro') }}</textarea>
                        @error('intro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control  @error('description') is-invalid @enderror"
                            rows="4"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="terms">Terms</label>
                        <textarea id="terms" name="terms" class="form-control wysiwyg @error('terms') is-invalid @enderror"
                            rows="4">{{ old('terms') }}</textarea>
                        @error('terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row date-fields-row">
                        <div class="col-md-6">
                            <div class="mb-3 date-field" >
                                <label class="form-label" for="date">Quotation Date</label>
                                <input id="date" name="date" type="date" value="{{ old('date') }}"
                                    class="form-control @error('date') is-invalid @enderror" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 date-field">
                                <label class="form-label" for="exp_date">Expiry Date</label>
                                <input id="exp_date" name="exp_date" type="date" value="{{ old('exp_date') }}"
                                    class="form-control @error('exp_date') is-invalid @enderror">
                                @error('exp_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save quotation</button>
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
        var ckTextareaData = '';
        @if (old('description'))
            ckTextareaData = '{!! old('description') !!}';
        @endif
    </script>
    <script type="module" src="{{ asset('cke/ck-main.js') }}"></script>
    <script>
        (function() {
            const productSelect = document.getElementById('product_id');
            const introField = document.getElementById('intro');
            const productIntroUrl = "{{ route('products.intro', ['product' => '__PRODUCT_ID__']) }}";

            const fillIntro = () => {
                const productId = productSelect.value;
                if (!productId) {
                    return;
                }

                const url = productIntroUrl.replace('__PRODUCT_ID__', productId);
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const html = data.html_description || '';
                        if (introField.__quill) {
                            introField.__quill.clipboard.dangerouslyPasteHTML(html || '<p><br></p>');
                            introField.value = html;
                        } else {
                            introField.value = html;
                        }
                    });
            };

            productSelect.addEventListener('change', fillIntro);
            fillIntro();
        })();
    </script>
@endsection
