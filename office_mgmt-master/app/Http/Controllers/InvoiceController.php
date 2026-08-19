<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft' => 'Draft',
        'created' => 'Created',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'creator'])->orderByDesc('invoice_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if (Auth::user()->isClient()) {
            $query->where('client_id', Auth::id());
        }

        $invoices = $query->simplePaginate(15)->withQueryString();

        $filters = $request->only(['status', 'date_from', 'date_to']);

        return view('invoices.index', compact('invoices', 'filters'));
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

        return view('invoices.create', [
            'clients' => $clients,
            'statuses' => self::STATUS_OPTIONS,
            'nextNumber' => Invoice::nextInvoiceNumber(),
            'products' => $products,
            'productMeta' => $productMeta,
        ]);
    }

    public function createFromEstimate(Estimate $estimate)
    {
        $estimate->loadMissing(['client', 'items']);

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

        $invoice = new Invoice;
        $invoice->client_id = $estimate->client_id;
        $invoice->buyer_name = $estimate->buyer_name;
        $invoice->buyer_mobile = $estimate->buyer_mobile;
        $invoice->buyer_gstin = $estimate->buyer_gstin;
        $invoice->invoice_date = $estimate->estimate_date ?? now();
        if ($estimate->client) {
            $invoice->setRelation('client', $estimate->client);
        }

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

        return view('invoices.create', [
            'clients' => $clients,
            'statuses' => self::STATUS_OPTIONS,
            'nextNumber' => Invoice::nextInvoiceNumber(),
            'products' => $products,
            'productMeta' => $productMeta,
            'estimate' => $estimate,
            'invoice' => $invoice,
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateInvoice($request);
        $itemsPayload = $validated['items'] ?? [];
        $buyerDetails = $this->resolveBuyerDetails($validated);
        $companyGstin = strtoupper(config('app.gstin', '33AAAAA0000A1Z5'));
        $companyState = substr($companyGstin, 0, 2);
        $isIntraState = $this->isIntraState($buyerDetails['buyer_gstin'] ?? null, $companyState);

        $totals = $this->calculateTotals($itemsPayload, $isIntraState);
        $branchId = $this->branchContext->currentBranchId();

        DB::transaction(function () use ($validated, $buyerDetails, $totals, $branchId) {
            $invoice = Invoice::create([
                'client_id' => $validated['customer_type'] === 'client' ? $validated['client_id'] : null,
                'buyer_name' => $buyerDetails['buyer_name'],
                'buyer_mobile' => $buyerDetails['buyer_mobile'],
                'buyer_gstin' => $buyerDetails['buyer_gstin'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'sub_total' => round($totals['sub_total'], 2),
                'total_cgst' => round($totals['total_cgst'], 2),
                'total_sgst' => round($totals['total_sgst'], 2),
                'total_igst' => round($totals['total_igst'], 2),
                'grand_total' => round($totals['grand_total'], 2),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'branch_id' => $branchId,
                'created_by' => Auth::id(),
            ]);

            $invoice->items()->createMany($totals['items']);

            if (! empty($validated['estimate_id'])) {
                Estimate::where('id', $validated['estimate_id'])
                    ->update(['converted_invoice_id' => $invoice->id]);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->loadMissing(['client', 'items.product', 'creator', 'invoicePayments.creator']);
        if (Auth::user()->isClient() && $invoice->client_id != Auth::id()) {
            return back()->with('error', 'Un-authorized request.');
        }

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
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

        $items = $invoice->items->map(function ($item) {
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

        return view('invoices.edit', [
            'invoice' => $invoice,
            'clients' => $clients,
            'statuses' => self::STATUS_OPTIONS,
            'products' => $products,
            'productMeta' => $productMeta,
            'items' => $items,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $this->validateInvoice($request, $invoice);
        $itemsPayload = $validated['items'] ?? [];
        $buyerDetails = $this->resolveBuyerDetails($validated);
        $companyGstin = strtoupper(config('app.gstin', '33AAAAA0000A1Z5'));
        $companyState = substr($companyGstin, 0, 2);
        $isIntraState = $this->isIntraState($buyerDetails['buyer_gstin'] ?? null, $companyState);

        $totals = $this->calculateTotals($itemsPayload, $isIntraState);

        DB::transaction(function () use ($invoice, $validated, $buyerDetails, $totals) {
            $invoice->update([
                'client_id' => $validated['customer_type'] === 'client' ? $validated['client_id'] : null,
                'buyer_name' => $buyerDetails['buyer_name'],
                'buyer_mobile' => $buyerDetails['buyer_mobile'],
                'buyer_gstin' => $buyerDetails['buyer_gstin'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'sub_total' => round($totals['sub_total'], 2),
                'total_cgst' => round($totals['total_cgst'], 2),
                'total_sgst' => round($totals['total_sgst'], 2),
                'total_igst' => round($totals['total_igst'], 2),
                'grand_total' => round($totals['grand_total'], 2),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($totals['items']);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function publicView(string $token)
    {
        try {
            $invoiceId = Crypt::decryptString($token);
        } catch (DecryptException) {
            abort(404);
        }

        $invoice = Invoice::with(['client', 'items.product', 'creator'])->findOrFail($invoiceId);

        return view('invoices.public', compact('invoice'));
        // $pdf = Pdf::loadView('invoices.public', compact('invoice'))->setPaper('a4');
        // return $pdf->stream(sprintf('Invoice-%s.pdf', $invoice->invoice_number));
    }

    private function validateInvoice(Request $request, ?Invoice $invoice = null): array
    {
        $rules = [
            'customer_type' => ['required', Rule::in(['customer', 'client'])],
            'client_id' => [
                'exclude_unless:customer_type,client',
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('type', 'client')),
            ],
            'invoice_number' => ['required', 'string', 'max:64', Rule::unique('invoices', 'invoice_number')->ignore($invoice?->id)],
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'billing_address' => 'nullable|string|max:512',
            'shipping_address' => 'nullable|string|max:512',
            'buyer_name' => 'required|string|max:255',
            'buyer_mobile' => 'nullable|string|max:32',
            'buyer_gstin' => 'nullable|string|max:32',
            'status' => ['required', Rule::in(array_keys(self::STATUS_OPTIONS))],
            'notes' => 'nullable|string|max:1000',
            'estimate_id' => 'nullable|exists:estimates,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.hsn_code' => 'nullable|string|max:64',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0|max:100',
        ];

        return $request->validate($rules);
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

    private function isIntraState(?string $buyerGstin, string $companyState): bool
    {
        if (empty($buyerGstin)) {
            return true;
        }

        $buyerState = strtoupper(substr($buyerGstin, 0, 2));

        return $buyerState === $companyState;
    }

    private function calculateTotals(array $itemsPayload, bool $isIntraState): array
    {
        $subTotal = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $grandTotal = 0;
        $itemsData = [];

        foreach ($itemsPayload as $payload) {
            $quantity = (float) $payload['quantity'];
            $rate = (float) $payload['rate'];
            $discount = (float) ($payload['discount'] ?? 0);
            $gstRate = (float) $payload['gst_rate'];

            $taxableValue = max(0, ($quantity * $rate) - $discount);
            $gstAmount = $taxableValue * ($gstRate / 100);
            $cgstAmount = 0;
            $sgstAmount = 0;
            $igstAmount = 0;

            if ($gstAmount > 0 && $isIntraState) {
                $cgstAmount = $gstAmount / 2;
                $sgstAmount = $gstAmount / 2;
            } else {
                $igstAmount = $gstAmount;
            }

            $totalAmount = $taxableValue + $cgstAmount + $sgstAmount + $igstAmount;

            $subTotal += $taxableValue;
            $totalCgst += $cgstAmount;
            $totalSgst += $sgstAmount;
            $totalIgst += $igstAmount;
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
                'cgst_amount' => round($cgstAmount, 2),
                'sgst_amount' => round($sgstAmount, 2),
                'igst_amount' => round($igstAmount, 2),
                'total_amount' => round($totalAmount, 2),
            ];
        }

        return [
            'sub_total' => $subTotal,
            'total_cgst' => $totalCgst,
            'total_sgst' => $totalSgst,
            'total_igst' => $totalIgst,
            'grand_total' => $grandTotal,
            'items' => $itemsData,
        ];
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:50',
            'reference_no' => 'nullable|string|max:100',
            'comment' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->invoicePayments()->create([
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_mode' => $validated['payment_mode'],
                'reference_no' => $validated['reference_no'],
                'comment' => $validated['comment'],
                'created_by' => Auth::id(),
            ]);

            // Auto-update status to paid if grand_total is reached
            $invoice->refresh(); // refresh to get new total_paid
            if ($invoice->total_paid >= $invoice->grand_total) {
                $invoice->update(['status' => 'paid']);
            }
        });

        return redirect()->back()->with('success', 'Payment added successfully.');
    }
}
