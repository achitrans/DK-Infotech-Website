<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'active');
        $users = User::where('type', 'employee')->orWhere('department', 'intern')
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select('id', 'name', 'email')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.wallet.index', compact('users', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);

        $amount = $request->type === 'debit' ? -$request->amount : $request->amount;

        WalletTransaction::createEntry(
            $request->user_id,
            $amount,
            $request->description,
            $request->reference
        );

        return redirect()->back()->with('success', 'Wallet transaction created successfully.');
    }

    public function transactions(Request $request)
    {
        $userId = $request->get('user_id');
        $transactions = WalletTransaction::with('user')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderBy('transaction_date', 'desc')
            ->simplePaginate(25);

        $users = User::where('type', 'employee')->orderBy('name', 'asc')->select('id', 'name')->get();

        return view('admin.wallet.transactions', compact('transactions', 'users', 'userId'));
    }
}
