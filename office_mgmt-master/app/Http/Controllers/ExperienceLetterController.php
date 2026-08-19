<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\OfferLetter;
use App\Models\ExperienceLetter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceLetterController extends Controller
{

    public function index()
    {
        $users = ExperienceLetter::with('user')->get();
        // return $users;
        return view('experience_letter.index', compact('users'));
    }

    public function create()
    {
        $users = User::query()->where('department', 'intern')
            ->orWhere('type', '=', 'employee')
            ->get();
        return view('experience_letter.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required'],
            'position' => ['required', 'string'],
            'skill' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        ExperienceLetter::create($validated);

        return redirect()
            ->route('experience-letters.index')
            ->with('success', 'Experience letter created successfully.');
    }

    public function edit(ExperienceLetter $experienceLetter)
    {
        $users = User::where('department', '!=', 'admin')->get();

        return view('experience_letter.edit', compact(
            'experienceLetter',
            'users'
        ));
    }

    public function update(Request $request, ExperienceLetter $experienceLetter)
    {
        $validated = $request->validate([
            'user_id' => ['required'],
            'position' => ['required', 'string'],
            'skill' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'issue_date' => ['required', 'date'],
        ]);

        $experienceLetter->update($validated);

        return redirect()
            ->route('experience-letters.index')
            ->with('success', 'Experience letter updated successfully.');
    }


    public function print(ExperienceLetter $experienceLetter)
    {
        // return abort(404);

        $experienceLetter->loadMissing(['user']);
        return view('experience_letter.print', compact('experienceLetter'));
    }

    public function PrintCertificateLetter(ExperienceLetter $certificateLetter)
    {
        $certificateLetter->loadMissing(['user']);
        return view('certificate_letter.print', compact('certificateLetter'));
    }

    public function EmployeeExperienceLetter(ExperienceLetter $experienceLetter)
    {
        $experienceLetter = ExperienceLetter::with('user')->where('user_id', Auth::id())->firstOrFail();
        // return $experienceLetter;
        return view('docs.employee.experience-letter', compact('experienceLetter'));
    }
}
