@php
    // Expecting $comments collection of root comments
@endphp
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Comment</h5>
    </div>
    <div class="card-body">
        <ul class="list-unstyled">
            @foreach($comments as $c)
                <li class="mb-1">
                    <div class="border rounded p-2 row {{ $c->is_internal ? 'bg-light' : '' }}">
                        <div class="col-10">
                            <strong>{{ $c->user->name ?? 'User' }}: </strong> {!! nl2br(e($c->comment)) !!}
                        </div>
                        <div class="col-2">
                            <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

