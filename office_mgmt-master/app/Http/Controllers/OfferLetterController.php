<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\OfferLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfferLetterController extends Controller
{
    public function index()
    {
        $offerLetters = OfferLetter::with(['career', 'interviewer', 'creator'])
            ->simplePaginate(15);


        return view('offer_letters.index', compact('offerLetters'));
    }

    public function create()
    {
        $candidates = Career::query()
            ->where('interview_status', 'Accept')
            ->orderByDesc('interview_date')
            ->orderByDesc('id')
            ->get(['id', 'name', 'email', 'mobile', 'interview_id', 'interview_date']);

        $interviewers = User::query()
            ->where('type', '!=', 'client')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'department']);

        return view('offer_letters.create', compact('candidates', 'interviewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'career_id' => [
                'required',
                Rule::unique('offer_letters', 'career_id'),
                Rule::exists('careers', 'id')->where(fn($query) => $query->where('interview_status', 'Accept')),
            ],
            'position' => ['required', 'string', 'max:255'],

            'interview_by_user_id' => [
                'nullable',
                'integer',
                'required_without:interview_by_name',
                Rule::exists('users', 'id'),
            ],
            'interview_by_name' => ['nullable', 'string', 'max:255', 'required_without:interview_by_user_id'],

            'ctc' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'stipend' => ['nullable', 'string', 'max:2000'],
            'date_of_joining' => ['required', 'date'],
        ]);

        $offerLetter = OfferLetter::create([
            'career_id' => (int) $validated['career_id'],
            'position' => $validated['position'],
            'interview_by_user_id' => $validated['interview_by_user_id'] ?? null,
            'interview_by_name' => $validated['interview_by_name'] ?? null,
            'ctc' => $validated['ctc'],
            'salary' => $validated['salary'] ?? null,
            'stipend' => $validated['stipend'] ?? null,
            'date_of_joining' => $validated['date_of_joining'],
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('offer-letters.index')
            ->with('success', 'Offer letter created successfully.')
            ->with('offer_letter_id', $offerLetter->id);
    }

    public function print(OfferLetter $offerLetter)
    {
        // return abort(404);

        $offerLetter->loadMissing(['career', 'interviewer', 'creator']);

        return view('offer_letters.print', compact('offerLetter'));
    }
}
