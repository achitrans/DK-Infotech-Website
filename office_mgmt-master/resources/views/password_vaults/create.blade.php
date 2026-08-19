@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Credentials </h5>
                </div>
                <div class="card-body">
                    @include('password_vaults._form', [
                        'action' => route('password-vaults.store'),
                        'method' => 'POST',
                        'buttonLabel' => 'Create',
                        'passwordVault' => $passwordVault,
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
