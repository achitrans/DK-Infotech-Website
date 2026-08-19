@extends('layouts.app')
@section('title','Edit Comment')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">Edit Comment</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('project-task-comments.update', $comment->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="comment">Comment</label>
                            <textarea name="comment" id="comment" rows="5" class="form-control" required>{{ old('comment', $comment->comment) }}</textarea>
                        </div>
                        <div class="form-group form-check mb-3">
                            <input type="checkbox" name="is_internal" value="1" class="form-check-input" id="is_internal" {{ old('is_internal', $comment->is_internal) ? 'checked' : '' }}>
                            <label for="is_internal" class="form-check-label">Internal (hidden from client)</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
