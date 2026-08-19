<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\DepartSkill;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\WhatsappService;
use App\Mail\JobApplicationReceivedMail;
use Illuminate\Support\Facades\Mail;

class CareerController extends Controller
{
    public function getSkills($id)
    {
        $department = DepartSkill::find($id);

        if (!$department) {
            return response()->json([]);
        }

        return response()->json($department->skills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email:rfc,dns|max:100',
            'mobile'                  => 'required|string|digits:10',
            'address'                 => 'required|string|max:500',
            'city'                    => 'required|string|max:100',
            'office_location'         => 'required|string|max:100',
            'pincode'                 => 'required|string|digits:6',
            'state_id'                => 'required|exists:states,id',
            'skills'                  => 'required|array',
            'skills.*'                => 'string|max:255',
            'department_skills_id'    => 'required|exists:department_skills,id',
            'photo'                   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'resume'                  => 'nullable|mimes:pdf,doc,docx|max:4096',
        ]);

        $validated['skills'] = isset($validated['skills']) ? json_encode($validated['skills']) : null;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('career/photos', 'public');
        }

        if ($request->hasFile('resume')) {
            $validated['resume'] = $request->file('resume')->store('career/resumes', 'public');
        }

        $career = Career::create($validated);

        // Prepare Notification Text
        $messageText = "Dear {$career->name},\n\n"
            . "Thank you for applying for a position at our company. We are pleased to confirm that we have received your application and truly appreciate your interest in joining our team.\n\n"
            . "Our hiring team is currently reviewing your qualifications and skills. We will contact you soon regarding the next steps in the selection process. If your profile matches our requirements, you may be invited for an interview or further assessment. You can also learn more about us here: ".env('COMPANY_WEBSITE')."\n\n"
            . "In the meantime, please feel free to reach out if you have any questions.\n\n"
            . "*Stay Connected With Us:*\n"
            . "Instagram: https://www.instagram.com/dkinfotechsolutions\n"
            . "Facebook: https://www.facebook.com/dkinfotechsolution\n"
            . "LinkedIn: https://linkedin.com/company/dkinfotechsolutions\n\n"
            . "For updates and announcements, you may also join our WhatsApp channel:\n"
            . "https://whatsapp.com/channel/0029Vb6A5wSKbYMTxfLT4i2C\n\n"
            . "We appreciate the time and effort you put into your application and wish you the very best.\n\n"
            . "Warm regards,\n"
            . env('COMPANY_NAME');

        // Send Email Notification
        try {
            \App\Jobs\SendEmailJob::dispatch($career->email, new JobApplicationReceivedMail($career, $messageText));
        } catch (\Exception $e) {
            \Log::error('Job Application Email dispatch failed: ' . $e->getMessage());
        }

        // Send WhatsApp Notification
        (new WhatsappService())->sendMessageAsync($career->mobile, $messageText);

        return redirect()->back()->with('success', 'Your job form has been submitted successfully!');
    }

    public function index(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'create_from_date' => 'nullable|date',
            'create_to_date' => 'nullable|date|after_or_equal:from_date',
            
        ]);

        $query = Career::with([
            'state:id,name',
            'departmentSkill:id,department'
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_skills_id', $request->input('department_id'));
        }

        if ($request->filled('office_location')) {
            $query->where('office_location', $request->input('office_location'));
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate || $toDate) {
            $from = $fromDate ? Carbon::parse($fromDate)->startOfDay() : null;
            $to = $toDate ? Carbon::parse($toDate)->endOfDay() : null;

            if ($from && $to) {
                $query->whereBetween('interview_date', [$from, $to]);
            } elseif ($from) {
                $query->where('interview_date', '>=', $from);
            } elseif ($to) {
                $query->where('interview_date', '<=', $to);
            }
            unset($from, $to);
        }

        $createFromDate = $request->input('create_from_date');
        $createToDate = $request->input('create_to_date');

        if ($createFromDate || $createToDate) {
            $from = $createFromDate ? Carbon::parse($createFromDate)->startOfDay() : null;
            $to = $createToDate ? Carbon::parse($createToDate)->endOfDay() : null;

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }
            unset($from, $to);
        }

        $careers = $query->simplePaginate();

        $departments = DepartSkill::select('id', 'department')
            ->orderBy('department')
            ->get();

        $officeLocations = Career::whereNotNull('office_location')
            ->distinct()
            ->orderBy('office_location')
            ->pluck('office_location');

        return view('careers.index', compact('careers', 'departments', 'officeLocations'));
    }

    public function edit($id)
    {
        $career = Career::findOrFail($id);
        $states = State::all();
        $departments = DepartSkill::all();

        return view('careers.edit', compact('career', 'states', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $career = Career::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:15',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'state_id' => 'required|exists:states,id',
            'office_location' => 'required|string',
            'department_skills_id' => 'required|exists:department_skills,id',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:4096',
            'status' => 'nullable|string|in:active,inactive'
        ]);

        $validated['skills'] = $request->skills ?? null;

        if ($request->hasFile('photo')) {

            if ($career->photo && Storage::disk('public')->exists($career->photo)) {
                Storage::disk('public')->delete($career->photo);
            }
            $validated['photo'] = $request->file('photo')
                ->store('career/photos', 'public');
        }

        if ($request->hasFile('resume')) {

            if ($career->resume && Storage::disk('public')->exists($career->resume)) {
                Storage::disk('public')->delete($career->resume);
            }

            $validated['resume'] = $request->file('resume')
                ->store('career/resumes', 'public');
        }

        $career->update($validated);

        return redirect()
            ->route('career.index')
            ->with('success', 'Your job form has been updated successfully!');
    }

    public function destroy($id)
    {
        $career = Career::findOrFail($id);
        $career->delete();

        return redirect()
            ->route('career.index')
            ->with('success', 'Career deleted successfully');
    }
}
