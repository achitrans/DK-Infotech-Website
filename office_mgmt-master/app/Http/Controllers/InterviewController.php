<?php

namespace App\Http\Controllers;

use App\Mail\InterviewScheduledMail;
use App\Models\Career;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
{

    protected $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function interviewForm($id)
    {
        $career = Career::findOrFail($id);
        return view('careers.interview', compact('career'));
    }

    private function interviewWhatsappMessage($career)
    {
        $interview_link = $career->others['interview_link'] ?? 'N/A';
        return "Dear {$career->name},\n\n"
            . "Greetings from ".env('COMPANY_NAME')."!\n\n"
            . "We are pleased to inform you that you have been shortlisted for an interview with our organization. "
            . "Please find the interview details below : \n\n"

            . "📅 Interview Date : " . date('d F Y', strtotime($career->interview_date)) . "\n"
            . "⏰ Interview Time : " . \Carbon\Carbon::parse($career->interview_time)->format('H:i') . "\n"
            . "🔗 Interview Link : {$interview_link}\n\n"
            . "💼 Interview Id : {$career->interview_id}\n\n"

            . "🌐 Company Website : \n"
            . env('COMPANY_WEBSITE')."\n\n"

            . "📍 Interview Address : \n"
            . "Suyash Sumitra Ashish Mansion,\n"
            . "1st Floor, 101 Road No. 2,\n"
            . "Gandhi Path, Opposite Sharda Lok Apartment,\n"
            . "Nehru Nagar, Patna, Bihar – 800013\n\n"

            . "📌 Google Map Location : \n"
            . "https://share.google/ujwqxPh1G5wl4Nruh\n\n"

            . "Please ensure that you arrive at least 10 minutes before the scheduled time "
            . "and carry a copy of your resume along with any relevant documents.\n\n"

            . "📲 Stay Connected With Us : \n"
            . "Instagram: https://www.instagram.com/dkinfotechsolutions\n"
            . "Facebook: https://www.facebook.com/dkinfotechsolution\n"
            . "LinkedIn: https://linkedin.com/company/dkinfotechsolutions\n\n"

            . "For updates and announcements, you may also join our WhatsApp channel:\n"
            . "https://whatsapp.com/channel/0029Vb6A5wSKbYMTxfLT4i2C\n\n"

            . "If you have any questions or are unable to attend, please inform us in advance.\n\n"

            . "We look forward to meeting you.\n\n"
            . "Best regards,\n"
            . "HR Team \n"
            . env('COMPANY_NAME');
    }

    public function scheduleInterview(Request $request, $id)
    {

        $request->validate([
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'interview_mode' => 'required|string',
            'interview_link' => 'nullable|url',
        ]);

        $interview = Career::findOrFail($id);

        $interview->interview_date = $request->input('interview_date');
        $interview->interview_time = $request->input('interview_time');
        $interview->interview_type = $request->input('interview_mode');
        $interview->others = array_merge($interview->others ?? [], [
            'interview_link' => $request->input('interview_link'),
        ]);
        $interview = $interview->isInterviewScheduled($interview);

        $interview->save();

        $message = $this->interviewWhatsappMessage($interview);

        $this->whatsapp->sendMessageAsync(
            $interview->mobile,
            $message
        );

        $email = trim((string) $interview->email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                \App\Jobs\SendEmailJob::dispatch($email, new InterviewScheduledMail($interview, $message));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Interview scheduled mail dispatch failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('career.index')->with('success', 'Interview scheduled & WhatsApp notification sent Successully.');
    }

    public function rescheduleInterview(Request $request, $id)
    {
        $request->validate([
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'interview_mode' => 'required|string',
        ]);

        $interview = Career::findOrFail($id);

        $interview->interview_date = $request->input('interview_date');
        $interview->interview_time = $request->interview_time;
        $interview->interview_type = $request->input('interview_mode');

        $interview->interview_id = $interview->interview_id;
        $interview->save();

        $message = $this->interviewWhatsappMessage($interview);

        $this->whatsapp->sendMessageAsync(
            $interview->mobile,
            $message
        );

        $email = trim((string) $interview->email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                \App\Jobs\SendEmailJob::dispatch($email, new InterviewScheduledMail($interview, $message));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Interview rescheduled mail dispatch failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('career.index')->with('success', 'Interview rescheduled & WhatsApp notification sent successfully.');
    }

    public function InterviewResult(Request $request, $id)
    {
        $request->validate([
            'interview_status' => 'required|string',
            'joining_on' => 'required|date|after_or_equal:today',
        ]);

        $career = Career::with('departmentSkill')->findOrFail($id);

        $career->interview_status = $request->input('interview_status');
        $career->is_joined = $request->input('is_joined');
        $career->joining_on = $request->input('joining_on');

        $career->save();

        if ($career->interview_status === 'Accept' && $career->is_joined == 1 && !$career->user_id) {

            // $existingUser = User::where('email', $career->email)
            //     ->orWhere('mobile', $career->mobile)
            //     ->first();

            // if (!$existingUser) {

            //     $user = User::create([
            //         'name'          => $career->name,
            //         'email'         => $career->email,
            //         'mobile'        => $career->mobile,
            //         'department'    => $career->departmentSkill->department,
            //         'type'          => 'employee',
            //         'work_location' => 'office',
            //         'status'        => 'active',
            //         'employee_id'   => User::generateInternId(),
            //         'password'      => bcrypt(env('DEFAULT_PASSWORD', Str::random(12))),
            //         'created_by'    => \Auth::id(),
            //         'parent_id'     => \Auth::id(),
            //     ]);

            //     $user->salary()->create([
            //         'basic'               => 0,
            //         'hra'                 => 0,
            //         'conveyance'          => 0,
            //         'special_allowance'   => 0,
            //         'medical_allowance'   => 0,
            //         'other_allowance'     => 0,
            //         'gross_salary'        => 0,
            //         'pf'                  => 0,
            //         'esi'                 => 0,
            //         'professional_tax'    => 0,
            //         'tds'                 => 0,
            //         'effective_from'      => now()->toDateString(),
            //     ]);

            //     $career->update([
            //         'user_id' => $user->id
            //     ]);
            // }

            return redirect('dashboard/users/create')
                ->withInput([
                    'name'   => $career->name,
                    'email'  => $career->email,
                    'mobile' => $career->mobile,
                ])->with('message', 'Create a new user');
        }

        return redirect()->back()->with('success', 'Interview results updated successfully.');
    }
}
