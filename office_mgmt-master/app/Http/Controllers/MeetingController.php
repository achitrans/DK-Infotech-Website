<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Models\Inquiry;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use App\Services\GoogleCalendarService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    protected GoogleCalendarService $calendarService;
    protected WhatsappService $whatsappService;

    public function __construct(GoogleCalendarService $calendarService, WhatsappService $whatsappService)
    {
        parent::__construct(app(\App\Services\BranchContext::class));
        $this->calendarService = $calendarService;
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $query = Meeting::with(['creator', 'client', 'project', 'inquiry', 'interview']);

        if (request('title')) {
            $query->where('title', 'like', '%' . request('title') . '%');
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('date')) {
            $query->whereDate('start_time', request('date'));
        }

        if (!Auth::user()->isAdmin() && !Auth::user()->isBranchManager()) {
            $query->where(function ($q) {
                $q->where('created_by', Auth::id())
                  ->orWhere('client_id', Auth::id());
            });
        }

        if (Auth::user()->isBranchManager()) {
            $query->where('branch_id', Auth::user()->branch_id);
        }

        $meetings = $query->orderBy('start_time', 'desc')->get();
        $googleTokenRecord = \App\Models\GoogleToken::first();
        $googleConnected = (bool) $googleTokenRecord;
        $googleEmail = $googleTokenRecord ? $googleTokenRecord->google_email : null;

        return view('meetings.index', compact('meetings', 'googleConnected', 'googleEmail'));
    }

    public function create(Request $request)
    {
        $clients = User::clients()->get();
        $employees = User::employees()->get();
        $projects = Project::all();
        $inquiries = Inquiry::all();
        $interviews = Career::whereNotNull('interview_date')->get();
        $googleConnected = \App\Models\GoogleToken::exists();

        $selectedProjectId = $request->query('project_id');
        $selectedInquiryId = $request->query('inquiry_id');
        $selectedInterviewId = $request->query('interview_id');
        $selectedClientId = $request->query('client_id');

        $meetingFor = 'client';

        if ($selectedProjectId) {
            $proj = Project::find($selectedProjectId);
            if ($proj && $proj->client_id) {
                $selectedClientId = $proj->client_id;
            }
            $meetingFor = 'client';
        } elseif ($selectedInquiryId || $selectedInterviewId) {
            $meetingFor = 'other';
        } elseif ($selectedClientId) {
            $meetingFor = 'client';
        }

        return view('meetings.create', compact(
            'clients', 'employees', 'projects', 'inquiries', 'interviews', 'googleConnected',
            'selectedClientId', 'selectedProjectId', 'selectedInquiryId', 'selectedInterviewId', 'meetingFor'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'date'                 => 'required|date|after_or_equal:today',
            'time'                 => 'required|string',
            'duration'             => 'required|integer|min:5|max:480',
            'client_id'            => 'nullable|exists:users,id',
            'project_id'           => 'nullable|exists:projects,id',
            'inquiry_id'           => 'nullable|exists:inquiries,id|prohibits:interview_id',
            'interview_id'         => 'nullable|exists:careers,id|prohibits:inquiry_id',
            'additional_attendees' => 'nullable|string',
            'send_notifications'   => 'nullable|boolean',
        ]);

        $tz = config('app.timezone', 'Asia/Kolkata');
        $startTime = Carbon::parse($data['date'] . ' ' . $data['time'], $tz);

        if ($startTime->isPast()) {
            return back()->withInput()->withErrors(['date' => 'Meeting start date and time cannot be in the past.']);
        }
        $endTime = (clone $startTime)->addMinutes((int) $data['duration']);

        $attendeesList = [];
        if (!Auth::user()->isClient() && !empty($data['additional_attendees'])) {
            $emails = explode(',', $data['additional_attendees']);
            foreach ($emails as $e) {
                $e = trim($e);
                if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $attendeesList[] = $e;
                }
            }
        }

        $meeting = Meeting::create([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'created_by'   => Auth::id(),
            'client_id'    => $data['client_id'] ?? null,
            'project_id'   => $data['project_id'] ?? null,
            'inquiry_id'   => $data['inquiry_id'] ?? null,
            'interview_id' => $data['interview_id'] ?? null,
            'attendees'    => $attendeesList,
            'status'       => 'scheduled',
            'branch_id'    => $this->branchContext->currentBranchId(),
        ]);

        $sendNotifications = $request->boolean('send_notifications', true);

        try {
            // Generate Google Meet Link via Google Calendar API & notify if requested
            $this->calendarService->createMeetEvent($meeting, $sendNotifications);
        } catch (\Exception $e) {
            $meeting->delete();
            return redirect()->route('meetings.index')
                ->with('error', $e->getMessage());
        }

        // Auto-send WhatsApp message with Google Meet link to client if notifications enabled
        if ($sendNotifications && $meeting->client && !empty($meeting->client->mobile)) {
            $this->sendMeetingWhatsappNotification($meeting, $meeting->client->mobile);
        }

        return redirect()->route('meetings.show', $meeting->id)
            ->with('success', 'Meeting scheduled successfully and Google Meet link generated!');
    }

    public function show($id)
    {
        $meeting = Meeting::with(['creator', 'client', 'project', 'inquiry', 'interview'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->isBranchManager()) {
            if ($meeting->created_by != Auth::id() && $meeting->client_id != Auth::id()) {
                return abort(404);
            }
        }

        $googleConnected = \App\Models\GoogleToken::exists();
        return view('meetings.show', compact('meeting', 'googleConnected'));
    }

    public function edit($id)
    {
        $meeting = Meeting::findOrFail($id);

        if (!Auth::user()->isAdmin() && $meeting->created_by != Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        $clients = User::clients()->get();
        $projects = Project::all();
        $inquiries = Inquiry::all();
        $interviews = Career::whereNotNull('interview_date')->get();

        return view('meetings.edit', compact('meeting', 'clients', 'projects', 'inquiries', 'interviews'));
    }

    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        if (!Auth::user()->isAdmin() && $meeting->created_by != Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'date'                 => 'required|date|after_or_equal:today',
            'time'                 => 'required|string',
            'duration'             => 'required|integer|min:5|max:480',
            'client_id'            => 'nullable|exists:users,id',
            'project_id'           => 'nullable|exists:projects,id',
            'inquiry_id'           => 'nullable|exists:inquiries,id|prohibits:interview_id',
            'interview_id'         => 'nullable|exists:careers,id|prohibits:inquiry_id',
            'status'               => 'required|in:scheduled,completed,cancelled',
            'additional_attendees' => 'nullable|string',
            'send_notifications'   => 'nullable|boolean',
        ]);

        $tz = config('app.timezone', 'Asia/Kolkata');
        $startTime = Carbon::parse($data['date'] . ' ' . $data['time'], $tz);

        if ($startTime->isPast()) {
            return back()->withInput()->withErrors(['date' => 'Meeting start date and time cannot be in the past.']);
        }
        $endTime = (clone $startTime)->addMinutes((int) $data['duration']);

        $attendeesList = [];
        if (!Auth::user()->isClient() && !empty($data['additional_attendees'])) {
            $emails = explode(',', $data['additional_attendees']);
            foreach ($emails as $e) {
                $e = trim($e);
                if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $attendeesList[] = $e;
                }
            }
        }

        $meeting->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'client_id'    => $data['client_id'] ?? null,
            'project_id'   => $data['project_id'] ?? null,
            'inquiry_id'   => $data['inquiry_id'] ?? null,
            'interview_id' => $data['interview_id'] ?? null,
            'attendees'    => $attendeesList,
            'status'       => $data['status'],
        ]);

        $sendNotifications = $request->boolean('send_notifications', false);

        // Sync changes with Google Calendar & notify if requested
        $this->calendarService->updateMeetEvent($meeting, $sendNotifications);

        if ($sendNotifications && $meeting->client && !empty($meeting->client->mobile)) {
            $this->sendMeetingWhatsappNotification($meeting, $meeting->client->mobile);
        }

        return redirect()->route('meetings.show', $meeting->id)->with('success', 'Meeting updated successfully!');
    }

    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);

        if (!Auth::user()->isAdmin() && $meeting->created_by != Auth::id()) {
            return abort(403, 'Unauthorized action.');
        }

        $this->calendarService->deleteMeetEvent($meeting, true);
        $meeting->delete();

        return redirect()->route('meetings.index')->with('success', 'Meeting cancelled and deleted successfully.');
    }

    public function sendWhatsapp($id)
    {
        $meeting = Meeting::with('client')->findOrFail($id);

        if (!$meeting->client || empty($meeting->client->mobile)) {
            return back()->with('error', 'Client mobile number not available.');
        }

        $sent = $this->sendMeetingWhatsappNotification($meeting, $meeting->client->mobile);

        if ($sent) {
            return back()->with('success', 'Meeting Google Meet invitation sent via WhatsApp successfully!');
        }

        return back()->with('error', 'Failed to send WhatsApp message.');
    }

    public function googleConnect()
    {
        $authUrl = $this->calendarService->getAuthUrl();
        Log::info("Redirecting user #" . Auth::id() . " to Google OAuth URL: " . $authUrl);
        return redirect()->away($authUrl);
    }

    public function googleCallback(Request $request)
    {
        Log::info("Google Callback Request Query Parameters", $request->all());

        if (!$request->has('code')) {
            Log::error("Google Callback Error: Code missing in request", $request->all());
            return redirect()->route('meetings.index')->with('error', 'Google authorization code missing or access denied.');
        }

        try {
            $this->calendarService->handleCallback($request->get('code'), Auth::id());
            return redirect()->route('meetings.index')->with('success', 'Google Calendar connected successfully!');
        } catch (\Exception $e) {
            Log::error("Google Auth Exception: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('meetings.index')->with('error', 'Google Auth Failed: ' . $e->getMessage());
        }
    }

    public function googleDisconnect()
    {
        if (!Auth::user()->isAdmin()) {
            return abort(403, 'Unauthorized action.');
        }

        \App\Models\GoogleToken::truncate();
        Log::info("User #" . Auth::id() . " disconnected Google Calendar account.");

        return redirect()->back()->with('success', 'Google Calendar account disconnected successfully!');
    }

    protected function sendMeetingWhatsappNotification(Meeting $meeting, string $phone): bool
    {
        $formattedDate = $meeting->start_time->format('d M Y');
        $formattedTime = $meeting->start_time->format('h:i A');
        $meetLink = $meeting->meet_link ?? 'Will be provided shortly';

        $message = "📅 *Meeting Invitation*\n\n"
                 . "Dear {$meeting->client->name},\n\n"
                 . "You have been invited to a meeting with *" . config('app.name', 'DK Info Tech') . "*.\n\n"
                 . "📌 *Subject*: {$meeting->title}\n"
                 . "🗓 *Date*: {$formattedDate}\n"
                 . "⏰ *Time*: {$formattedTime}\n"
                 . "🔗 *Google Meet Link*: {$meetLink}\n\n"
                 . "Please join the Google Meet link at the scheduled time.\n\n"
                 . "Thank you!";

        try {
            $res = $this->whatsappService->sendMessage($phone, $message);
            return !empty($res);
        } catch (\Exception $e) {
            Log::error("Failed to send meeting WhatsApp notification: " . $e->getMessage());
            return false;
        }
    }
}
