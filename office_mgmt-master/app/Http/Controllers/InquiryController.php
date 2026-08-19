<?php
namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\InquiryFollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{

    public function index(Request $request)
    {
        $query = Inquiry::with('followUps.user')->latest();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('state')) {
            $query->where('state', 'like', '%' . $request->state . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('follow_up_due')) {
            $query->where('follow_up_due', $request->follow_up_due);
        }
        if ($request->filled('inquiry_date')) {
            $query->whereDate('created_at', $request->inquiry_date);
        }

        $inquiries = $query->latest()->simplePaginate();
        return view('inquiries.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = Inquiry::with('followUps')->findOrFail($id);
        return view('inquiries.show', compact('inquiry'));
    }

    public function create()
    {
        return view('inquiries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:100',
            'subject' => 'required|max:100',
            'message' => 'nullable|max:1000',
            'source' => 'required|max:100',
            'status' => 'required|max:100',
            'follow_up_due' => 'nullable|date',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
        ]);
        $data['user_id'] = Auth::id();
        $data['branch_id'] = $this->branchContext->currentBranchId();
        Inquiry::create($data);
        return redirect()->route('inquiries.index')->with('success', 'Inquiry created');
    }

        public function edit($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        return view('inquiries.edit', compact('inquiry'));
    }

    public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:100',
            'subject' => 'nullable|max:100',
            'message' => 'nullable|max:1000',
            'source' => 'required|max:100',
            'status' => 'required|max:100',
            'follow_up_due' => 'nullable|date',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
        ]);
        $inquiry->update($data);
        return redirect()->route('inquiries.index', $inquiry->id)->with('success', 'Inquiry updated');
    }

    public function addFollowUp(Request $request, $inquiryId)
    {
        $inquiry = Inquiry::findOrFail($inquiryId);
        $data = $request->validate([
            'remarks' => 'required|max:200',
            'follow_up_due' => 'nullable|date',
        ]);
        $data['user_id'] = Auth::id();
        $data['follow_up_date'] = $data['follow_up_due'];
        $data['inquiry_id'] = $inquiry->id;
        InquiryFollowUp::create($data);
        if ($data['follow_up_due']) {
            $inquiry->follow_up_due = $data['follow_up_due'];
            $inquiry->save();
        }
        return back()->with('success', 'Follow up added');
    }
}
