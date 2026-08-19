@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $passwordVault->name }}</h5>
                        <small class="text-muted">Vault entry for {{ $passwordVault->username }}</small>
                    </div>
                    <span class="badge bg-{{ $passwordVault->is_shared ? 'success' : 'secondary' }}">
                        {{ $passwordVault->is_shared ? 'Shared' : 'Private' }}
                    </span>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Username</dt>
                        <dd class="col-sm-9"><strong>{{ $passwordVault->username }}</strong></dd>

                        <dt class="col-sm-3">Password</dt>
                        <dd class="col-sm-9"><code>{{ $passwordVault->password }}</code></dd>

                        <dt class="col-sm-3">URL</dt>
                        <dd class="col-sm-9">
                            @if($passwordVault->url)
                                <a href="{{ $passwordVault->url }}" target="_blank" rel="noreferrer">{{ $passwordVault->url }}</a>
                            @else
                                &mdash;
                            @endif
                        </dd>

                        <dt class="col-sm-3">Category</dt>
                        <dd class="col-sm-9">{{ $passwordVault->category ?? 'Unspecified' }}</dd>

                        <dt class="col-sm-3">Last used</dt>
                        <dd class="col-sm-9">
                            {{ optional($passwordVault->last_used_at)->format('d M Y h:i A') ?? 'Never' }}
                        </dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">
                            @if($passwordVault->notes)
                                {{ $passwordVault->notes }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection