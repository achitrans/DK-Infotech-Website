@extends('layouts.app')
@section('title', 'Download Employee Data')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <h2>Documents</h2>
                <a href="{{ route('docs.employee.id-card') }}" class="btn btn-outline-success btn-lg my-3 mr-3" target="_blank"> Id
                    Card</a>
                <a href="{{ route('docs.employee.offer-letter') }}" class="btn btn-outline-warning btn-lg my-3 mr-3"
                    target="_blank">Offer Letter</a>

                <a href="{{ route('docs.employee.experience-letter') }}" class="btn btn-outline-primary btn-lg my-3 mr-3"
                    target="_blank">Experience Letter</a>

                <a href="{{ route('docs.employee.certificate-letter') }}" class="btn btn-outline-info btn-lg my-3 mr-3"
                    target="_blank">Certificate Letter</a>
            </div>
        </div>
    </div>
@endsection
