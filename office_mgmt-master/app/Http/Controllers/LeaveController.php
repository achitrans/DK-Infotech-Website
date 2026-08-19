<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class LeaveController extends Controller
{

    // Employee: view own leaves
    public function index()
    {
        $leaves = Leave::where('user_id', Auth::id())->orderByDesc('from_date')->simplePaginate(20);
        return view('leaves.index', compact('leaves'));
    }
    // Employee: apply for leave
    public function create()
    {
        return view('leaves.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:tomorrow',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|in:paid,unpaid',
            'reason' => 'required|string|max:255',
        ]);

        // Calculate total leave days
        $fromDate = \Carbon\Carbon::parse($request->from_date);
        $toDate = \Carbon\Carbon::parse($request->to_date);
        $totalDays = $fromDate->diffInDays($toDate) + 1;

        // Only one paid leave per month
        if ($request->leave_type === 'paid') {
            $paidCount = Leave::where('user_id', Auth::id())
                ->where(function($query) use ($request) {
                    $query->whereBetween('from_date', [$request->from_date, $request->to_date])
                          ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
                          ->orWhere(function($subQuery) use ($request) {
                              $subQuery->where('from_date', '<=', $request->from_date)
                                       ->where('to_date', '>=', $request->to_date);
                          });
                })
                ->where('leave_type', 'paid')
                ->count();
            if ($paidCount > 0) {
                return back()->withErrors(['leave_type' => 'Paid leave dates overlap with existing paid leave.'])->withInput();
            }
        }

        $leave = Leave::create([
            'user_id' => Auth::id(),
            'branch_id' => $this->branchContext->currentBranchId(),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'status' => 'pending',
            'applied_by' => Auth::id(),
        ]);

        $wa = new WhatsappService();
        $message = Auth::user()->name ." has applied for leave.\n";
        $message .= 'Reason: '.$request->reason."\n";
        if ($request->from_date != $request->to_date){
            $message .= 'From : '.$fromDate . ' to '.$request->to_date."\n";
        }else{
            $message .= 'Date: '.$request->from_date."\n";
        }

        $message .= 'Update leave status: '. route('leave.updateStatusPublicUrl', Crypt::encrypt($leave->id)) . "\n";


        $wa->sendMessage(env('COMPANY_WHATSAPP_NOTIFICATION_NUMBER'),$message);
        return redirect()->route('leaves.index')->with('success', 'Leave applied successfully.');
    }
    // Admin: view all leaves
    public function adminIndex()
    {
        $leaves = Leave::with('user')->orderByDesc('from_date')->simplePaginate(20);
        return view('leaves.admin_index', compact('leaves'));
    }
    // Admin: approve leave
    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'approved';
        $leave->approved_by = Auth::id();
        $leave->remarks = $request->remarks;
        $leave->save();

        Leave::sendUpdateNotification($leave);

        return back()->with('success', 'Leave approved.');
    }
    // Admin: reject leave
    public function reject(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'rejected';
        $leave->rejected_by = Auth::id();
        $leave->remarks = $request->remarks;
        $leave->save();
        Leave::sendUpdateNotification($leave);
        return back()->with('success', 'Leave rejected.');
    }
    // Admin: change leave type
    public function changeType(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $leave->leave_type = $request->leave_type;
        $leave->save();
        return back()->with('success', 'Leave type updated.');
    }

    // Admin: edit leave
    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        return view('leaves.edit', compact('leave'));
    }

    // Admin: update leave
    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|in:paid,unpaid',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ]);
        $leave->from_date = $request->from_date;
        $leave->to_date = $request->to_date;
        $leave->leave_type = $request->leave_type;
        $leave->status = $request->status;
        $leave->reason = $request->reason;
        $leave->remarks = $request->remarks;
        if ($request->status == 'approved') {
            $leave->approved_by = Auth::id();
        } elseif ($request->status == 'rejected') {
            $leave->rejected_by = Auth::id();
        }
        $leave->save();
        Leave::sendUpdateNotification($leave);
        return redirect()->route('leaves.admin.index')->with('success', 'Leave updated successfully.');
    }

    public function updateStatusPublicUrl($string)
    {
        try {
            $leaveId = $this->decodePublicStatusToken($string);
            $leave = Leave::with('user')->findOrFail($leaveId);
            return view('leaves.public_status_update', [
                'leave' => $leave,
                'string' => $string,
                'statusOptions' => ['approved', 'rejected'],
            ]);
        } catch (\Exception $exception) {
            abort(404, 'Invalid leave update link.');
        }
    }

    public function processStatusUpdatePublicUrl(Request $request, $string)
    {
        try {
            $leaveId = $this->decodePublicStatusToken($string);
        } catch (\Exception $exception) {
            abort(404, 'Invalid leave update link.');
        }
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $leave = Leave::findOrFail($leaveId);
        $statusUpdated = false;
        $statusMessage = 'Leave already '.ucfirst($request->status).'.';

        if ($leave->status !== $request->status) {
            $leave->status = $request->status;
            $leave->save();
            Leave::sendUpdateNotification($leave);
            $statusUpdated = true;
            $statusMessage = 'Leave '.ucfirst($request->status).' successfully.';
        }

        return view('leaves.public_status_update', [
            'leave' => $leave,
            'string' => $string,
            'statusOptions' => ['approved', 'rejected'],
            'statusMessage' => $statusMessage,
            'statusUpdated' => $statusUpdated,
            'selectedStatus' => $request->status,
        ]);
    }

    protected function decodePublicStatusToken($string)
    {
        $data = Crypt::decrypt($string);
        if (!ctype_digit((string) $data)) {
            throw new \RuntimeException('Invalid leave status token.');
        }
        return (int) $data;
    }
}
