<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function create()
    {
        $products = Product::select('id', 'name', 'html_description')->orderBy('name')->get();

        return view('quotations.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'intro' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'exp_date' => ['nullable', 'date'],
        ]);

        $data['branch_id'] = $this->branchContext->currentBranchId();
        Quotation::create($data);

        return redirect()->route('quotations.create')->with('success', 'Quotation created successfully.');
    }

    public function index()
    {
        $quotations = Quotation::with('product')->latest()->simplePaginate(12);

        return view('quotations.index', compact('quotations'));
    }

    public function edit(Quotation $quotation)
    {
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('quotations.edit', compact('quotation', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'intro' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'exp_date' => ['nullable', 'date'],
        ]);

        $quotation->update($data);

        return redirect()->route('quotations.edit', $quotation)->with('success', 'Quotation updated.');
    }

    public function show(Quotation $quotation)
    {
        return view('quotations.show', compact('quotation'));
    }

    public function print(Quotation $quotation)
    {
        $quotation->loadMissing(['product', 'branch']);

        return view('quotations.print', compact('quotation'));
    }
}
