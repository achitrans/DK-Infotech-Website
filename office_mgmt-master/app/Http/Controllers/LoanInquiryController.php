<?php

namespace App\Http\Controllers;

use App\Models\LoanInquiry;
use Illuminate\Http\Request;

class LoanInquiryController extends Controller
{
    public function index()
    {
        $query = LoanInquiry::with('user');

        if (request('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('phone')) {
            $query->where('phone', 'like', '%' . request('phone') . '%');
        }
        if (request('category')) {
            $query->where('category', request('category'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('source')) {
            $query->where('source', request('source'));
        }
        if (request('user')) {
            $query->where('user_id', request('user'));
        }

        $inquiries = $query->orderByDesc('created_at')->simplePaginate(20);
        $users = \App\Models\User::orderBy('name')->get();
        return view('loan_inquiries.index', compact('inquiries', 'users'));
    }

    public function create()
    {
        return view('loan_inquiries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(LoanInquiry::$categories)),
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'source' => 'required|in:' . implode(',', array_keys(LoanInquiry::$sources)),
            'status' => 'required|in:' . implode(',', array_keys(LoanInquiry::$statuses)),
            'follow_up_due' => 'nullable|date',
            'tenure' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:20',
            'dob' => 'nullable|string|max:20',
            'pan' => 'nullable|string|max:20',
            'aadhar' => 'nullable|string|max:20',
            'pin_code' => 'nullable|string|max:20',
            'statement_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        if ($request->hasFile('statement_file')) {
            $data['statement_file'] = $request->file('statement_file')->store('statements', 'public');
        }
        $data['user_id'] = \Auth::id();
        $loan = LoanInquiry::create($data);

        LoanInquiry::sendNotification($loan);

        return redirect()->route('loan-inquiries.index')->with('success', 'Loan inquiry created successfully.');
    }

    public function edit($id)
    {
        $inquiry = LoanInquiry::findOrFail($id);
        return view('loan_inquiries.edit', compact('inquiry'));
    }

    public function update(Request $request, $id)
    {
        $inquiry = LoanInquiry::findOrFail($id);
        $data = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(LoanInquiry::$categories)),
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'source' => 'required|in:' . implode(',', array_keys(LoanInquiry::$sources)),
            'status' => 'required|in:' . implode(',', array_keys(LoanInquiry::$statuses)),
            'follow_up_due' => 'nullable|date',
            'closed_at' => 'nullable|date',
            'tenure' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:20',
            'dob' => 'nullable|string|max:20',
            'pan' => 'nullable|string|max:20',
            'aadhar' => 'nullable|string|max:20',
            'pin_code' => 'nullable|string|max:20',
            'statement_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        if ($request->hasFile('statement_file')) {
            $data['statement_file'] = $request->file('statement_file')->store('statements', 'public');
        }
        $inquiry->update($data);

        LoanInquiry::sendNotification($inquiry);
        return redirect()->route('loan-inquiries.index')->with('success', 'Loan inquiry updated successfully.');
    }

    public function show($id)
    {
        $inquiry = LoanInquiry::with('user')->findOrFail($id);
        return view('loan_inquiries.show', compact('inquiry'));
    }

    public function destroy($id)
    {
        $inquiry = LoanInquiry::findOrFail($id);
        $inquiry->delete();
        return redirect()->route('loan-inquiries.index')->with('success', 'Loan inquiry deleted successfully.');
    }
}
