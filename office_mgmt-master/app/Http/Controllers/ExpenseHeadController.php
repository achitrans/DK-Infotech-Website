<?php

namespace App\Http\Controllers;

use App\Models\ExpenseHead;
use Illuminate\Http\Request;

class ExpenseHeadController extends Controller
{

    public function index()
    {
        $heads = ExpenseHead::orderBy('name')->simplePaginate(20);
        return view('expense_heads.index', compact('heads'));
    }

    public function create()
    {
        return view('expense_heads.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:expense_heads,name',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        ExpenseHead::create($data);

        return redirect()->route('expense-heads.index')->with('success', 'Expense head created.');
    }

    public function edit(ExpenseHead $expense_head)
    {
        return view('expense_heads.edit', ['head' => $expense_head]);
    }

    public function update(Request $request, ExpenseHead $expense_head)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:expense_heads,name,' . $expense_head->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $expense_head->update($data);

        return redirect()->route('expense-heads.index')->with('success', 'Expense head updated.');
    }

    public function destroy(ExpenseHead $expense_head)
    {
        $expense_head->delete();
        return redirect()->route('expense-heads.index')->with('success', 'Expense head deleted.');
    }
}
