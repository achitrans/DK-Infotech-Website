@extends('layouts.app')
@section('title', 'Offer Letters')
@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Offer Letters</h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="{{ route('offer-letters.create') }}" class="btn btn-primary">Create Offer Letter</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Interview ID</th>
                            <th>Position</th>
                            <th>CTC</th>
                            <th>Joining Date</th>
                            <th>Interview By</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offerLetters as $offer)
                            @php
                                $career = $offer->career;
                                $interviewBy = $offer->interview_by_name ?: ($offer->interviewer?->name ?? '-');
                            @endphp
                            <tr>
                                <td>{{ $career?->name ?? '-' }}</td>
                                <td>{{ $career?->interview_id ?? '-' }}</td>
                                <td>{{ $offer->position }}</td>
                                <td>₹{{ number_format((float) $offer->ctc, 2) }}</td>
                                <td>{{ $offer->date_of_joining?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $interviewBy }}</td>
                                <td>{{ $offer->created_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('offer-letters.print', $offer) }}" target="_blank" class="btn btn-sm btn-success">
                                        Print
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No offer letters found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $offerLetters->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
