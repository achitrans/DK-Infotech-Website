<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add Comment</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('project-task-comments.store', $task->id) }}" class="mb-3 prevent-double-submit">
            @csrf
            <input type="hidden" name="parent_id" id="parent_id" value="">
            <div class="form-group">
                <label for="comment">Comment</label>
                <textarea name="comment" id="comment" rows="3" class="form-control" required>{{ old('comment') }}</textarea>
            </div>
            {{--    <div class="form-group form-check">--}}
            {{--        <input type="checkbox" name="is_internal" value="1" class="form-check-input" id="is_internal" {{ old('is_internal') ? 'checked' : '' }}>--}}
            {{--        <label for="is_internal" class="form-check-label">Internal (hidden from client)</label>--}}
            {{--    </div>--}}
            <button type="submit" class="btn btn-primary btn-sm mt-2">Post Comment</button>
        </form>
    </div>
</div>

<script>
    // Simple reply handler to set parent_id (expects elements with data-reply-id)
    document.addEventListener('click', function (e) {
        if (e.target.matches('[data-reply-id]')) {
            document.getElementById('parent_id').value = e.target.getAttribute('data-reply-id');
            document.getElementById('comment').focus();
        }
    });
</script>

<script>
    // Prevent double submit for forms with class 'prevent-double-submit'
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.prevent-double-submit').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (this.dataset.submitted === 'true') {
                    e.preventDefault();
                    return false;
                }
                this.dataset.submitted = 'true';
                const btn = this.querySelector('button[type="submit"], input[type="submit"]');
                if (btn) btn.disabled = true;
            });
        });
    });
</script>
