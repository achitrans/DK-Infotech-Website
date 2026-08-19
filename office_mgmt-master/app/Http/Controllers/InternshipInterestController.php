<?php

namespace App\Http\Controllers;

use App\Mail\InternshipOfferMail;
use App\Models\InternshipInterest;
use App\Models\GraduationCourse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InternshipInterestController extends Controller
{
    // Public form for applicants
    public function create()
    {
        $types = InternshipInterest::$types;
        $sources = InternshipInterest::$sources;
        $graduationCourse = GraduationCourse::all();
        $data = InternshipInterest::all();
        return view('internship_interests.create', compact('types', 'sources', 'graduationCourse', 'data'));
    }

    // Store applicant submission
    public function store(Request $request)
    {


        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'type' => 'nullable|in:internship,training',
            'degree' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'start_date_preference' => 'nullable|date',
            'availability_weeks' => 'nullable|integer',
            'skills' => 'nullable|string|max:200',
            'portfolio_link' => 'nullable|url|max:200',
            'github_link' => 'nullable|url|max:200',
            'linkedin' => 'nullable|url|max:200',
            'notes' => 'nullable|string|max:200',
            'source' => 'nullable|in:website,referral,campus,email,other',
            'consent' => 'sometimes|boolean',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx',
            'student_type' => 'required|in:college,personal',
            'graduation_course' => 'required_if:student_type,college|nullable|string|max:255',
            'semester' => 'required_if:student_type,college|nullable',
            'college' => 'required_if:student_type,college|nullable|string|max:255',
            'roll_no' => 'required_if:student_type,college|nullable',
            'parent_relation' => 'nullable',
            'parent_name' => 'required_if:student_type,college|nullable|string|max:255',
            'date_of_joining' => 'required|date'
        ]);

        if ($request->hasFile('resume_file')) {
            $data['resume_file'] = $request->file('resume_file')->store('internship_resumes', 'public');
        }

        $data['user_id'] = Auth::check() ? Auth::id() : null;
        $data['ip_address'] = $request->ip();
        $data['consent'] = (bool) ($data['consent'] ?? false);
        $data['branch_id'] = $this->branchContext->currentBranchId();

        $interest = InternshipInterest::create($data);


        // send mail to user

        /*
        $pdf = Pdf::loadView('confirm_letter.print', [
            'student' => $interest
        ]);

        $pdfPath = storage_path("app/internship_{$interest->id}.pdf");

        $pdf->save($pdfPath);

        Mail::to($interest->email)->send(
            new InternshipOfferMail(
                $interest->name,
                $interest->position ?? 'Intern',
                $pdfPath
            )
        );
        */

        return redirect()->route('payu.checkout', $interest->id);
    }

    // Resume a dropped transaction
    public function resume(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
        ]);

        $interest = InternshipInterest::where('email', $request->email)
            ->where('phone', $request->phone)
            ->where('name', $request->name)
            ->where('payment_status', 'pending')
            ->first();

        if ($interest) {
            return redirect()->route('payu.checkout', $interest->id);
        }

        return redirect()->back()->withErrors(['resume_error' => 'No pending transaction found with the provided details. Please submit a fresh request.'])->withInput();
    }

    // Admin listing
    public function index(Request $request)
    {
        $query = InternshipInterest::query();
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }
        $interests = $query->orderByDesc('created_at')->simplePaginate(20)->withQueryString();

        return view('internship_interests.index', compact('interests'));
    }

    public function show($id)
    {
        $interest = InternshipInterest::findOrFail($id);

        return view('internship_interests.show', compact('interest'));
    }

    public function edit($id)
    {
        $interest = InternshipInterest::findOrFail($id);

        return view('internship_interests.edit', compact('interest'));
    }

    public function update(Request $request, $id)
    {
        $interest = InternshipInterest::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'degree' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer',
            'position' => 'nullable|string|max:255',
            'start_date_preference' => 'nullable|date',
            'availability_weeks' => 'nullable|integer',
            'skills' => 'nullable|string|max:200',
            'portfolio_link' => 'nullable|url|max:200',
            'github_link' => 'nullable|url|max:200',
            'linkedin' => 'nullable|url|max:200',
            'cover_letter' => 'nullable|string|max:200',
            'source' => 'nullable|in:website,referral,campus,email,other',
            'status' => 'nullable|in:new,reviewed,shortlisted,interviewed,offered,rejected',
            'notes' => 'nullable|string|max:200',
            'consent' => 'sometimes|boolean',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        if ($request->hasFile('resume_file')) {
            // delete old file if exists
            if ($interest->resume_file) {
                Storage::disk('public')->delete($interest->resume_file);
            }
            $data['resume_file'] = $request->file('resume_file')->store('internship_resumes', 'public');
        }

        $data['consent'] = (bool) ($data['consent'] ?? false);
        $interest->update($data);

        return redirect()->route('internship-interests.index')->with('success', 'Interest updated.');
    }

    public function destroy($id)
    {
        $interest = InternshipInterest::findOrFail($id);
        $interest->delete();

        return redirect()->route('internship-interests.index')->with('success', 'Interest deleted.');
    }

    public function download($id)
    {
        $confirmLetter = InternshipInterest::findOrFail($id);

        return view('certificate_letter.intern-confirm-letter', compact('confirmLetter'));
    }
}
