<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $query = Expense::with('head', 'creator', 'approver')->orderBy('expense_date', 'desc');

        if ($request->filled('head_id')) {
            $query->where('expense_head_id', $request->head_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('expense_date', [$request->from, $request->to]);
        } elseif ($request->filled('from')) {
            $query->where('expense_date', '>=', $request->from);
        } elseif ($request->filled('to')) {
            $query->where('expense_date', '<=', $request->to);
        }


        if (auth()->user()->type !== 'admin') {
            $query->where('created_by', auth()->id());
        }

        $expenses = $query->simplePaginate(20)->withQueryString();

        $heads = ExpenseHead::active()->orderBy('name')->get();

        $currentBalance = auth()->user()->getWalletBalance();

        return view('expenses.index', compact('expenses', 'heads', 'currentBalance'));
    }

    public function create()
    {
        $currentBalance = auth()->user()->getWalletBalance();
        $heads = ExpenseHead::active()->orderBy('name')->get();
        return view('expenses.create', compact('heads','currentBalance'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'title' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,bank_transfer,card,cheque,upi,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';
        $data['branch_id'] = $this->branchContext->currentBranchId();

        $expense = Expense::create($data);

        // Wallet entry will be created when approved
        // WalletTransaction::createEntry(...);

        return redirect()->route('expenses.index')->with('success', 'Expense submitted.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeAction($expense);

        $heads = ExpenseHead::active()->orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'heads'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAction($expense);

        $rules = [
            'expense_head_id' => 'required|exists:expense_heads,id',
            'title' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,bank_transfer,card,cheque,upi,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        // Admin can update status
        if (auth()->user() && auth()->user()->type === 'admin') {
            $rules['status'] = 'nullable|in:pending,approved,rejected';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('receipt')) {
            // delete old file if exists
            if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        // If admin approving, set approved_by
        if (isset($data['status']) && $data['status'] === 'approved') {
            $data['approved_by'] = auth()->id();
        }

        // Handle wallet transactions for approved expenses
        if ($expense->status === 'approved' || (isset($data['status']) && $data['status'] === 'approved')) {
            $oldAmount = $expense->amount;
            $newAmount = $data['amount'] ?? $expense->amount;

            if ($oldAmount != $newAmount) {
                // Amount changed: debit old amount (reverse) and debit new amount
                WalletTransaction::createEntry(
                    $expense->created_by,
                    $oldAmount, // Credit back the old amount (positive)
                    "Expense amount adjustment - reversed: {$expense->title}",
                    "Expense ID: {$expense->id}"
                );

                WalletTransaction::createEntry(
                    $expense->created_by,
                    -$newAmount, // Debit the new amount (negative)
                    "Expense amount adjustment - new: {$expense->title}",
                    "Expense ID: {$expense->id}"
                );
            } elseif (!isset($data['status']) || $data['status'] !== 'approved') {
                // If it was approved and now something else, but amount same, maybe reverse
                // But for now, only handle amount changes
            }
        }

        // If newly approved and amount didn't change, create initial debit
        if (isset($data['status']) && $data['status'] === 'approved' && $expense->status !== 'approved') {
            // Check if already debited
            $existingDebit = WalletTransaction::where('user_id', $expense->created_by)
                ->where('reference', 'Expense ID: ' . $expense->id)
                ->where('amount', '<', 0)
                ->exists();

            if (!$existingDebit) {
                WalletTransaction::createEntry(
                    $expense->created_by,
                    -$data['amount'], // Debit the amount
                    "Expense approved: {$data['title']}",
                    "Expense ID: {$expense->id}"
                );
            }
        }

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        // only creator or admin can delete
        if (!(auth()->id() === $expense->created_by || (auth()->user() && auth()->user()->type === 'admin'))) {
            abort(403);
        }

        // delete receipt
        if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        // If expense was approved, credit back the amount
        if ($expense->status === 'approved') {
            WalletTransaction::createEntry(
                $expense->created_by,
                $expense->amount, // Credit back (positive)
                "Expense deleted: {$expense->title}",
                "Expense ID: {$expense->id}"
            );
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    protected function authorizeAction(Expense $expense)
    {
        if (auth()->user() && auth()->user()->type === 'admin') {
            return true;
        }

        if (auth()->id() !== $expense->created_by) {
            abort(403);
        }

        return true;
    }
}
