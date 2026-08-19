<?php

namespace App\Http\Controllers;

use App\Models\AdvanceSalary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AdvanceSalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = AdvanceSalary::with(['user', 'approver'])
            ->where('branch_id', $this->branchContext->currentBranchId())
            ->orderByDesc('created_at');

        if (!$this->canManageAdvanceRequests()) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $advances = $query->paginate(15);
        return view('advance_salaries.index', compact('advances'));
    }

    public function create()
    {
        $employees = $this->canManageAdvanceRequests()
            ? User::where('type', 'employee')
                ->where('branch_id', $this->branchContext->currentBranchId())
                ->get()
            : collect([Auth::user()]);

        return view('advance_salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'term_type' => ['required', Rule::in([AdvanceSalary::TERM_FULL, AdvanceSalary::TERM_FIXED])],
            'deduction_value' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'reference' => ['nullable', 'string'],
        ]);

        if ($validated['term_type'] === AdvanceSalary::TERM_FIXED && empty($validated['deduction_value'])) {
            return Redirect::back()->withInput()->withErrors(['deduction_value' => 'Deduction value is required when term is fixed amount.']);
        }

        $userId = $validated['user_id'] ?? Auth::id();
        if (!$this->canManageAdvanceRequests()) {
            $userId = Auth::id();
        }

        AdvanceSalary::create([
            'user_id' => $userId,
            'branch_id' => $this->branchContext->currentBranchId(),
            'requested_by' => Auth::id(),
            'amount' => $validated['amount'],
            'outstanding_amount' => 0,
            'term_type' => $validated['term_type'],
            'deduction_value' => $validated['deduction_value'] ?? 0,
            'status' => AdvanceSalary::STATUS_PENDING,
            'remarks' => $validated['remarks'] ?? null,
            'reference' => $validated['reference'] ?? null,
        ]);

        Session::flash('success', 'Advance salary request created and pending approval.');
        return Redirect::route('advance-salaries.index');
    }

    public function approve(AdvanceSalary $advance)
    {
        if ($advance->status !== AdvanceSalary::STATUS_PENDING) {
            return Redirect::back()->withErrors(['status' => 'Only pending advances can be approved.']);
        }

        $advance->update([
            'status' => AdvanceSalary::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'outstanding_amount' => $advance->amount,
        ]);

        Session::flash('success', 'Advance salary approved.');
        return Redirect::route('advance-salaries.index');
    }

    public function reject(AdvanceSalary $advance)
    {
        if ($advance->status !== AdvanceSalary::STATUS_PENDING) {
            return Redirect::back()->withErrors(['status' => 'Only pending advances can be rejected.']);
        }

        $advance->update([
            'status' => AdvanceSalary::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        Session::flash('success', 'Advance salary request rejected.');
        return Redirect::route('advance-salaries.index');
    }

    protected function canManageAdvanceRequests(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'accounts', 'branch manager']);
    }
}
