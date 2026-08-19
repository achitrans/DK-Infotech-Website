<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'sku', 'hsn_code', 'gst_rate']);

        $productsQuery = Product::orderBy('name');

        if (!empty($filters['name'])) {
            $productsQuery->where('name', 'like', '%'.$filters['name'].'%');
        }
        if (!empty($filters['sku'])) {
            $productsQuery->where('sku', 'like', '%'.$filters['sku'].'%');
        }
        if (!empty($filters['hsn_code'])) {
            $productsQuery->where('hsn_code', 'like', '%'.$filters['hsn_code'].'%');
        }
        if (!empty($filters['gst_rate'])) {
            $productsQuery->where('gst_rate', $filters['gst_rate']);
        }

        $products = $productsQuery->simplePaginate(15)->appends(array_filter($filters));

        return view('products.index', compact('products', 'filters'));
    }

    public function create()
    {
        return view('products.create', [
            'uomOptions' => Product::uomOptions(),
            'gstOptions' => Product::gstRateOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:64', 'unique:products,sku'],
            'hsn_code' => ['required', 'string', 'max:8'],
            'uom' => ['required', Rule::in(Product::uomOptions())],
            'sales_price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', Rule::in(Product::gstRateOptions())],
            'is_service' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'html_description' => ['nullable', 'string'],
        ]);

        Product::create(array_merge($data, ['is_service' => $request->boolean('is_service')]));

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'uomOptions' => Product::uomOptions(),
            'gstOptions' => Product::gstRateOptions(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:64', 'unique:products,sku,'.$product->id],
            'hsn_code' => ['required', 'string', 'max:8'],
            'uom' => ['required', Rule::in(Product::uomOptions())],
            'sales_price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', Rule::in(Product::gstRateOptions())],
            'is_service' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'html_description' => ['nullable', 'string'],
        ]);

        $product->update(array_merge($data, ['is_service' => $request->boolean('is_service')]));

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product removed.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function intro(Product $product)
    {
        return response()->json(['html_description' => $product->html_description]);
    }
}