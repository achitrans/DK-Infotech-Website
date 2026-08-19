@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Users</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Filter User List</div>

            <div class="card-body">
                <div class="mb-3">
                    <form method="GET" action="{{ route('users.index') }}" class="form-inline">
                        <div class="row">
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Name"
                                    value="{{ request('name') }}">
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email"
                                    value="{{ request('email') }}">
                            </div>

                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Department</label>
                                <select name="department" class="form-control">
                                    <option value="">Department</option>
                                    @foreach (\App\Models\User::$departments as $key => $label)
                                        @if ($key !== 'client')
                                            <option value="{{ $key }}"
                                                {{ request('department') == $key ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="">Type</option>
                                    @foreach (\App\Models\User::$types as $key => $label)
                                        @if ($key !== 'client')
                                            <option value="{{ $key }}"
                                                {{ request('type') == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-2 mb-2 col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Status</option>
                                    @foreach (\App\Models\User::$status as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" name="search" class="btn btn-primary">Filter</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name & <br> ID</th>
                            <th>Email &<br>Mobile </th>
                            <th>Dept.</th>
                            <th>Type</th>
                            <th>Work Loc.</th>
                            <th>Status</th>
                            <th>Barcode <br>/ RFID</th>
                            <th>Salary <br>(Gross)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }} <br> {{ $user->employee_id ?? '-' }}</td>
                                <td>{{ $user->email }} <br> {{ $user->mobile ?? '-' }} </td>
                                <td>{{ $user->department }}</td>
                                <td>{{ $user->type }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $user->work_location == 'office' ? 'primary' : ($user->work_location == 'remote' ? 'success' : ($user->work_location == 'hybrid' ? 'warning' : 'info')) }}">
                                        {{ ucfirst($user->work_location) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColor =
                                            $user->status == 'active'
                                                ? 'success'
                                                : ($user->status == 'inactive'
                                                    ? 'secondary'
                                                    : 'danger');
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }}">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td>{{ $user->barcode_rfid ?? '-' }}</td>
                                <td>{{ $user->salary ? $user->salary->gross_salary : '-' }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning"> <i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            ondblclick="return confirm('Want to delete\n`Are you sure?')"
                                            onclick="return false"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('user-kyc.show', Crypt::encrypt($user->id)) }}"
                                            class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Kyc</a>
                                        <a href="{{ route('loginByPass', Crypt::encrypt($user->id)) }}"
                                            class="btn btn-sm btn-danger"><i class="fas fa-sign-in-alt"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
