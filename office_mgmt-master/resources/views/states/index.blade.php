@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">States</h5>
                        <a href="{{ route('states.create') }}" class="btn btn-primary btn-sm">Add State</a>
                    </div>
                    {{-- <div class="card-body"> --}}

                        <div class="card-bodytable-responsive">
                            <table class="table table-bordered table-hover">
                                <thead >
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">GST Code</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($states as $state)
                                        <tr>
                                            <td>{{ $state->name }}</td>
                                            <td>{{ $state->code }}</td>
                                            <td>{{ $state->gst_code ?? '-' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('states.edit', $state) }}"
                                                    class="btn btn-outline-primary btn-sm">Edit</a>
                                                <form action="{{ route('states.destroy', $state) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" type="submit"
                                                        onclick="return confirm('Delete this state?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No states have been defined
                                                yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
