<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EstimateController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'approved' => 'Approved',
        'expired' => 'Expired',
    ];

    public function index(Request $request)
    {
        $query = Estimate::with(['client', 'creator', 'convertedInvoice'])
            ->orderByDesc('estimate_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('estimate_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('estimate_date', '<=', $request->date_to);
        }

        if (Auth::user()->isClient()) {
            $query->where('client_id', Auth::id());
        }

        $estimates = $query->simplePaginate(15)->withQueryString();

        $filters = $request->only(['status', 'date_from', 'date_to']);

        return view('estimates.index', compact('estimates', 'filters'));
    }

    public function create()
    {
        $clients = User::where('type', 'client')
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'hsn_code', 'sales_price', 'gst_rate']);
        $productMeta = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'hsn_code' => $product->hsn_code,
                'sales_price' => (float) $product->sales_price,
                'gst_rate' => (float) $product->gst_rate,
            ];
        })->toArray();

        return view('estimates.create', [
            'clients' => $clients,
            'statuses' => self::STATUS_OPTIONS,
            'nextNumber' => Estimate::nextEstimateNumber(),
            'defaultExpiry' => now()->addDays(30)->toDateString(),
            'products' => $products,
            'productMeta' => $productMeta,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_type' => ['required', Rule::in(['customer', 'client'])],
            'client_id' => [
                'exclude_unless:customer_type,client',
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('type', 'client')),
            ],
            'estimate_number' => ['required', 'string', 'max:64', Rule::unique('estimates', 'estimate_number')],
            'estimate_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:estimate_date',
            'buyer_name' => 'required|string|max:255',
            'buyer_mobile' => 'nullable|string|max:32',
            'buyer_gstin' => 'nullable|string|max:32',
            'status' => ['required', Rule::in(array_keys(self::STATUS_OPTIONS))],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.hsn_code' => 'nullable|string|max:64',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0|max:100',
        ];

        $validated = $request->validate($rules);
        $itemsPayload = $validated['items'] ?? [];
        $subTotal = 0;
        $totalTax = 0;
        $grandTotal = 0;
        $itemsData = [];
        $buyerDetails = $this->resolveBuyerDetails($validated);
        $branchId = $this->branchContext->currentBranchId();

        foreach ($itemsPayload as $payload) {
            $quantity = (float) $payload['quantity'];
            $rate = (float) $payload['rate'];
            $discount = (float) ($payload['discount'] ?? 0);
            $gstRate = (float) $payload['gst_rate'];

            $taxableValue = max(0, ($quantity * $rate) - $discount);
            $taxAmount = $taxableValue * ($gstRate / 100);
            $totalAmount = $taxableValue + $taxAmount;

            $subTotal += $taxableValue;
            $totalTax += $taxAmount;
            $grandTotal += $totalAmount;

            $itemsData[] = [
                'product_id' => $payload['product_id'] ?? null,
                'item_name' => $payload['item_name'],
                'hsn_code' => $payload['hsn_code'] ?? '',
                'quantity' => round($quantity, 2),
                'rate' => round($rate, 2),
                'discount' => round($discount, 2),
                'taxable_value' => round($taxableValue, 2),
                'gst_rate' => round($gstRate, 2),
                'total_amount' => round($totalAmount, 2),
            ];
        }

        DB::transaction(function () use ($validated, $itemsData, $subTotal, $totalTax, $grandTotal, $buyerDetails, $branchId) {
            $estimate = Estimate::create([
                'client_id' => $validated['customer_type'] === 'client' ? $validated['client_id'] : null,
                'buyer_name' => $buyerDetails['buyer_name'],
                'buyer_mobile' => $buyerDetails['buyer_mobile'],
                'buyer_gstin' => $buyerDetails['buyer_gstin'],
                'estimate_number' => $validated['estimate_number'],
                'estimate_date' => $validated['estimate_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'sub_total' => round($subTotal, 2),
                'total_tax' => round($totalTax, 2),
                'grand_total' => round($grandTotal, 2),
                'status' => $validated['status'],
                'branch_id' => $branchId,
                'created_by' => Auth::id(),
            ]);

            $estimate->items()->createMany($itemsData);
        });

        return redirect()->route('estimates.index')->with('success', 'Estimate created successfully.');
    }

    public function show(Estimate $estimate)
    {
        $estimate->loadMissing(['client', 'items.product', 'convertedInvoice']);
        if (Auth::user()->isClient() && $estimate->client_id != Auth::id()) {
            return back()->with('error', 'Un-authorized request.');
        }

        return view('estimates.show', compact('estimate'));
    }

    public function publicView(string $token)
    {
        try {
            $estimateId = Crypt::decryptString($token);
        } catch (DecryptException) {
            abort(404);
        }

        $estimate = Estimate::with(['client', 'items.product', 'creator'])->findOrFail($estimateId);

        return view('estimates.public', compact('estimate'));
        // $pdf = Pdf::loadView('estimates.public', compact('estimate'))->setPaper('a4');
        // return $pdf->stream(sprintf('Estimate-%s.pdf', $estimate->estimate_number));
    }

    public function edit(Estimate $estimate)
    {
        $clients = User::where('type', 'client')
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'hsn_code', 'sales_price', 'gst_rate']);
        $productMeta = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'hsn_code' => $product->hsn_code,
                'sales_price' => (float) $product->sales_price,
                'gst_rate' => (float) $product->gst_rate,
            ];
        })->toArray();

        $items = $estimate->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'item_name' => $item->item_name,
                'hsn_code' => $item->hsn_code,
                'quantity' => $item->quantity,
                'rate' => $item->rate,
                'discount' => $item->discount,
                'gst_rate' => $item->gst_rate,
                'total_amount' => $item->total_amount,
            ];
        })->toArray();

        return view('estimates.edit', [
            'estimate' => $estimate,
            'clients' => $clients,
            'statuses' => self::STATUS_OPTIONS,
            'products' => $products,
            'productMeta' => $productMeta,
            'items' => $items,
        ]);
    }

    public function update(Request $request, Estimate $estimate)
    {
        $rules = [
            'customer_type' => ['required', Rule::in(['customer', 'client'])],
            'client_id' => [
                'exclude_unless:customer_type,client',
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('type', 'client')),
            ],
            'estimate_number' => ['required', 'string', 'max:64', Rule::unique('estimates', 'estimate_number')->ignore($estimate->id)],
            'estimate_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:estimate_date',
            'buyer_name' => 'required|string|max:255',
            'buyer_mobile' => 'nullable|string|max:32',
            'buyer_gstin' => 'nullable|string|max:32',
            'status' => ['required', Rule::in(array_keys(self::STATUS_OPTIONS))],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.hsn_code' => 'nullable|string|max:64',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0|max:100',
        ];

        $validated = $request->validate($rules);
        $itemsPayload = $validated['items'] ?? [];
        $subTotal = 0;
        $totalTax = 0;
        $grandTotal = 0;
        $itemsData = [];
        $buyerDetails = $this->resolveBuyerDetails($validated);

        foreach ($itemsPayload as $payload) {
            $quantity = (float) $payload['quantity'];
            $rate = (float) $payload['rate'];
            $discount = (float) ($payload['discount'] ?? 0);
            $gstRate = (float) $payload['gst_rate'];

            $taxableValue = max(0, ($quantity * $rate) - $discount);
            $taxAmount = $taxableValue * ($gstRate / 100);
            $totalAmount = $taxableValue + $taxAmount;

            $subTotal += $taxableValue;
            $totalTax += $taxAmount;
            $grandTotal += $totalAmount;

            $itemsData[] = [
                'product_id' => $payload['product_id'] ?? null,
                'item_name' => $payload['item_name'],
                'hsn_code' => $payload['hsn_code'] ?? '',
                'quantity' => round($quantity, 2),
                'rate' => round($rate, 2),
                'discount' => round($discount, 2),
                'taxable_value' => round($taxableValue, 2),
                'gst_rate' => round($gstRate, 2),
                'total_amount' => round($totalAmount, 2),
            ];
        }

        DB::transaction(function () use ($estimate, $validated, $itemsData, $subTotal, $totalTax, $grandTotal, $buyerDetails) {
            $estimate->update([
                'client_id' => $validated['customer_type'] === 'client' ? $validated['client_id'] : null,
                'buyer_name' => $buyerDetails['buyer_name'],
                'buyer_mobile' => $buyerDetails['buyer_mobile'],
                'buyer_gstin' => $buyerDetails['buyer_gstin'],
                'estimate_number' => $validated['estimate_number'],
                'estimate_date' => $validated['estimate_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'sub_total' => round($subTotal, 2),
                'total_tax' => round($totalTax, 2),
                'grand_total' => round($grandTotal, 2),
                'status' => $validated['status'],
            ]);

            $estimate->items()->delete();
            $estimate->items()->createMany($itemsData);
        });

        return redirect()->route('estimates.index')->with('success', 'Estimate updated successfully.');
    }

    public function destroy(Estimate $estimate)
    {
        $estimate->delete();

        return redirect()->route('estimates.index')->with('success', 'Estimate deleted successfully.');
    }

    private function resolveBuyerDetails(array $validated): array
    {
        $buyerName = $validated['buyer_name'];
        $buyerMobile = $validated['buyer_mobile'] ?? null;
        $buyerGstin = $validated['buyer_gstin'] ?? null;

        if ($validated['customer_type'] === 'client' && ! empty($validated['client_id'])) {
            $client = User::with('kycClient')->find($validated['client_id']);
            if ($client) {
                $buyerName = $client->name;
                $buyerMobile = $client->mobile ?? $buyerMobile;
                $buyerGstin = $client->kycClient?->business_gstin ?? $buyerGstin;
            }
        }

        return [
            'buyer_name' => $buyerName,
            'buyer_mobile' => $buyerMobile,
            'buyer_gstin' => $buyerGstin,
        ];
    }
}
