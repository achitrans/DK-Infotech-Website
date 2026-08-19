@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Edit {{ $passwordVault->name }} Credentials</h5>
                    </div>
                    <a href="{{ route('password-vaults.show', $passwordVault) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
                <div class="card-body">
                    @include('password_vaults._form', [
                        'action' => route('password-vaults.update', $passwordVault),
                        'method' => 'PUT',
                        'buttonLabel' => 'Save changes',
                        'passwordVault' => $passwordVault,
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
