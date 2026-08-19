@php
    $invoice = $invoice ?? null;
    $customerTypeValue = old('customer_type') ?? ($invoice?->client ? 'client' : 'customer');
    $clientIdValue = old('client_id', $invoice?->client_id ?? '');
    $invoiceNumberValue = old('invoice_number', $invoice?->invoice_number ?? ($nextNumber ?? ''));
    $invoiceDateValue = old('invoice_date', $invoice?->invoice_date?->toDateString() ?? now()->toDateString());
    $dueDateValue = old('due_date', $invoice?->due_date?->toDateString() ?? now()->addWeeks(1)->toDateString());
    $billingAddressValue = old('billing_address', $invoice?->billing_address ?? '');
    $shippingAddressValue = old('shipping_address', $invoice?->shipping_address ?? '');
    $statusValue = old('status', $invoice?->status ?? array_key_first($statuses));
    $buyerNameValue = old('buyer_name', $invoice?->buyer_name ?? '');
    $buyerMobileValue = old('buyer_mobile', $invoice?->buyer_mobile ?? '');
    $buyerGstinValue = old('buyer_gstin', $invoice?->buyer_gstin ?? '');
    $notesValue = old('notes', $invoice?->notes ?? '');
    $storedItems = old('items', $items ?? []);
    if (count($storedItems) === 0) {
        $storedItems = [
            [
                'item_name' => '',
                'hsn_code' => '',
                'quantity' => 1,
                'rate' => 0,
                'discount' => 0,
                'gst_rate' => 0,
                'total_amount' => '',
                'product_id' => '',
            ],
        ];
    }
    $formMethodNormalized = strtoupper($formMethod ?? 'POST');
@endphp
<form action="{{ $formAction }}" method="POST" id="invoice_form">
    @csrf
    @if (!empty($estimate))
        <input type="hidden" name="estimate_id" value="{{ $estimate->id }}">
    @endif
    @if ($formMethodNormalized !== 'POST')
        @method($formMethodNormalized)
    @endif


    <span class="text-warning fs-4">Invoice Info</span>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label class="form-label fw-bold">Create for</label>
            <div class="d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="customer_type" id="customer_type_customer"
                        value="customer" {{ $customerTypeValue === 'customer' ? 'checked' : '' }}>
                    <label class="form-check-label" for="customer_type_customer">Customer (manual entry)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="customer_type" id="customer_type_client"
                        value="client" {{ $customerTypeValue === 'client' ? 'checked' : '' }}>
                    <label class="form-check-label" for="customer_type_client">Existing client</label>
                </div>
            </div>
        </div>
        <div class="col-md-6 my-2 client-select-wrapper {{ $customerTypeValue !== 'client' ? 'd-none' : '' }}">
            <label class="form-label">Select client</label>
            <select class="form-select" name="client_id" id="client_selector">
                <option value="">Choose client...</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}"
                        {{ (string) $clientIdValue === (string) $client->id ? 'selected' : '' }}>
                        {{ $client->name }} ({{ $client->email }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="row date-fields-row">
            <div class="col-md-6 my-2">
                <label class="form-label">Invoice number</label>
                <input type="text" class="form-control " name="invoice_number"
                    value="{{ $invoiceNumberValue }}" required>
            </div>
            <div class="col-md-6 my-2  date-field">
                <label class="form-label">Invoice date</label>
                <input type="date" class="form-control " name="invoice_date"
                    value="{{ $invoiceDateValue }}" required>
            </div>
            <div class="col-md-6 my-2  date-field">
                <label class="form-label">Due date</label>
                <input type="date" class="form-control " name="due_date" value="{{ $dueDateValue }}">
            </div>
            <div class="col-md-6 my-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" {{ $statusValue === $value ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <hr>

    <span class="text-warning fs-4">Address Info</span>

    <div class="row g-3">
        <div class="col-md-6 my-2">
            <label class="form-label">Billing address</label>
            <textarea class="form-control " name="billing_address" rows="2">{{ $billingAddressValue }}</textarea>
        </div>
        <div class="col-md-6 my-2">
            <label class="form-label">Shipping address</label>
            <textarea class="form-control " name="shipping_address" rows="2">{{ $shippingAddressValue }}</textarea>
        </div>
    </div>

    <hr>

    <span class="text-warning fs-4">Buyer Info</span>

    <div class="row g-3 mt-3">
        <div class="col-md-4">
            <label class="form-label">Buyer name</label>
            <input type="text" class="form-control " name="buyer_name" id="buyer_name"
                value="{{ $buyerNameValue }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Buyer mobile</label>
            <input type="text" class="form-control " name="buyer_mobile" id="buyer_mobile"
                value="{{ $buyerMobileValue }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Buyer GSTIN</label>
            <input type="text" class="form-control " name="buyer_gstin" id="buyer_gstin"
                value="{{ $buyerGstinValue }}">
        </div>
    </div>

    <hr>

    <span class="text-warning fs-4">Invoice Items</span>

    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Items</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add_item_row">Add item</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-borderless align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Item</th>
                        <th>HSN</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Discount</th>
                        <th>GST %</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="items_table_body">
                    @foreach ($storedItems as $index => $item)
                        <tr data-row-index="{{ $index }}">
                            <td>
                                <select name="items[{{ $index }}][product_id]"
                                    class="form-select form-select-sm product-selector">
                                    <option value="">Manual entry</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ (array_key_exists('product_id', $item) ? (string) $item['product_id'] : '') === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}{{ $product->sku ? ' (' . $product->sku . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][item_name]"
                                    class="form-control " value="{{ $item['item_name'] ?? '' }}"
                                    required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][hsn_code]"
                                    class="form-control " value="{{ $item['hsn_code'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" min="0" name="items[{{ $index }}][quantity]"
                                    class="form-control  input-number"
                                    value="{{ $item['quantity'] ?? 0 }}" required>
                            </td>
                            <td>
                                <input type="number" min="0" name="items[{{ $index }}][rate]"
                                    class="form-control  input-number"
                                    value="{{ $item['rate'] ?? 0 }}" required>
                            </td>
                            <td>
                                <input type="number" min="0" name="items[{{ $index }}][discount]"
                                    class="form-control  input-number"
                                    value="{{ $item['discount'] ?? 0 }}">
                            </td>
                            <td>
                                <select name="items[{{ $index }}][gst_rate]"
                                    class="form-select form-select-sm input-number" required>
                                    @foreach (App\Models\Product::gstRateOptions() as $option)
                                        <option value="{{ $option }}"
                                            {{ ((float) ($item['gst_rate'] ?? 0)) === (float) $option ? 'selected' : '' }}>
                                            {{ number_format($option, fmod($option, 1) ? 2 : 0) }}%</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][total_amount]"
                                    class="form-control  total-amount" readonly
                                    value="{{ isset($item['total_amount']) ? number_format((float) $item['total_amount'], 2, '.', '') : '' }}">
                            </td>
                            <td>
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <template id="item_row_template">
                <tr data-row-index="__INDEX__">
                    <td>
                        <select name="items[__INDEX__][product_id]"
                            class="form-select form-select-sm product-selector">
                            <option value="">Manual entry</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}{{ $product->sku ? ' (' . $product->sku . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="items[__INDEX__][item_name]" class="form-control "
                            required>
                    </td>
                    <td>
                        <input type="text" name="items[__INDEX__][hsn_code]" class="form-control ">
                    </td>
                    <td>
                        <input type="number" min="0" name="items[__INDEX__][quantity]"
                            class="form-control  input-number" value="1" required>
                    </td>
                    <td>
                        <input type="number" min="0" name="items[__INDEX__][rate]"
                            class="form-control  input-number" value="0" required>
                    </td>
                    <td>
                        <input type="number" min="0" name="items[__INDEX__][discount]"
                            class="form-control  input-number" value="0">
                    </td>
                    <td>
                        <select name="items[__INDEX__][gst_rate]" class="form-select form-select-sm input-number"
                            required>
                            @foreach (App\Models\Product::gstRateOptions() as $option)
                                <option value="{{ $option }}">
                                    {{ number_format($option, fmod($option, 1) ? 2 : 0) }}%</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[__INDEX__][total_amount]"
                            class="form-control  total-amount" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button>
                    </td>
                </tr>
            </template>
        </div>
    </div>

    <hr>

    <span class="text-warning fs-4">Invoice Summary</span>

    <div class="mt-4 row gx-3">
        <div class="col-md-3">
            <label class="form-label mb-0">Sub total</label>
            <div class="fs-5 fw-bold" id="sub_total_display">0.00</div>
            <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', 0) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-0">CGST total</label>
            <div class="fs-5 fw-bold" id="cgst_total_display">0.00</div>
            <input type="hidden" name="total_cgst" id="total_cgst" value="{{ old('total_cgst', 0) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-0">SGST total</label>
            <div class="fs-5 fw-bold" id="sgst_total_display">0.00</div>
            <input type="hidden" name="total_sgst" id="total_sgst" value="{{ old('total_sgst', 0) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-0">IGST total</label>
            <div class="fs-5 fw-bold" id="igst_total_display">0.00</div>
            <input type="hidden" name="total_igst" id="total_igst" value="{{ old('total_igst', 0) }}">
        </div>
    </div>

    <div class="mt-3 row gx-3">
        <div class="col-md-4">
            <label class="form-label mb-0">Grand total</label>
            <div class="fs-4 fw-bold" id="grand_total_display">0.00</div>
            <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', 0) }}">
        </div>
    </div>

    <hr>

    <span class="text-warning fs-4">Additional Info</span>

    <div class="mt-4">
        <label class="form-label">Notes</label>
        <textarea class="form-control " name="notes" rows="2">{{ $notesValue }}</textarea>
    </div>

    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save invoice' }}</button>
    </div>
</form>

@section('scripts')
    <script>
        (function($) {
            const customerRadios = $('input[name="customer_type"]');
            const clientWrapper = $('.client-select-wrapper');
            const clientSelector = $('#client_selector');
            const itemsBody = $('#items_table_body');
            const productMeta = @json($productMeta);
            const productMap = productMeta.reduce((acc, product) => {
                acc[product.id] = product;
                return acc;
            }, {});
            const itemRowTemplate = $('#item_row_template').html();
            const subTotalInput = $('#sub_total');
            const cgstInput = $('#total_cgst');
            const sgstInput = $('#total_sgst');
            const igstInput = $('#total_igst');
            const grandTotalInput = $('#grand_total');
            const subTotalDisplay = $('#sub_total_display');
            const cgstDisplay = $('#cgst_total_display');
            const sgstDisplay = $('#sgst_total_display');
            const igstDisplay = $('#igst_total_display');
            const grandTotalDisplay = $('#grand_total_display');
            const companyGstin = "{{ strtoupper(config('office.gstin', '33AAAAA0000A1Z5')) }}";
            const companyState = companyGstin.substring(0, 2);
            let rowIndex = itemsBody.find('tr').length;

            function toggleClientSection() {
                const selected = customerRadios.filter(':checked').val();
                clientWrapper.toggleClass('d-none', selected !== 'client');
                if (selected === 'client') {
                    fetchClientDetails(clientSelector.val());
                }
            }

            function fetchClientDetails(clientId) {
                if (!clientId) {
                    return;
                }
                const url = "{{ url('dashboard/clients') }}" + '/' + clientId + '/details';
                $.get(url, function(response) {
                    const kyc = response.kyc || {};
                    $('#buyer_name').val(kyc.business_name || response.name || '');
                    $('#buyer_mobile').val(response.mobile || kyc.business_phone || '');
                    $('#buyer_gstin').val(kyc.business_gstin || '');
                });
            }

            function getBuyerGstin() {
                return $('#buyer_gstin').val().trim().toUpperCase();
            }

            function isIntraState() {
                const buyerGstin = getBuyerGstin();
                if (!buyerGstin) {
                    return true;
                }
                return buyerGstin.substring(0, 2) === companyState;
            }

            function parseNumber(value) {
                return parseFloat(value) || 0;
            }

            function recalcRow($row) {
                const quantity = parseNumber($row.find('[name*="[quantity]"]').val());
                const rate = parseNumber($row.find('[name*="[rate]"]').val());
                const discount = parseNumber($row.find('[name*="[discount]"]').val());
                const gstRate = parseNumber($row.find('[name*="[gst_rate]"]').val());
                const taxable = Math.max(0, (quantity * rate) - discount);
                const gst = taxable * gstRate / 100;
                const intra = isIntraState();
                let cgst = 0;
                let sgst = 0;
                let igst = 0;

                if (gst > 0 && intra) {
                    cgst = gst / 2;
                    sgst = gst / 2;
                } else {
                    igst = gst;
                }

                const total = taxable + cgst + sgst + igst;

                $row.data('taxable', taxable);
                $row.data('cgst', cgst);
                $row.data('sgst', sgst);
                $row.data('igst', igst);
                $row.find('[name*="[total_amount]"]').val(total ? total.toFixed(2) : '');
            }

            function refreshTotals() {
                let subTotal = 0;
                let totalCgst = 0;
                let totalSgst = 0;
                let totalIgst = 0;
                let grandTotal = 0;

                itemsBody.find('tr').each(function() {
                    const $row = $(this);
                    const taxable = $row.data('taxable') || 0;
                    const cgst = $row.data('cgst') || 0;
                    const sgst = $row.data('sgst') || 0;
                    const igst = $row.data('igst') || 0;
                    const total = taxable + cgst + sgst + igst;

                    subTotal += taxable;
                    totalCgst += cgst;
                    totalSgst += sgst;
                    totalIgst += igst;
                    grandTotal += total;
                });

                subTotalInput.val(subTotal.toFixed(2));
                cgstInput.val(totalCgst.toFixed(2));
                sgstInput.val(totalSgst.toFixed(2));
                igstInput.val(totalIgst.toFixed(2));
                grandTotalInput.val(grandTotal.toFixed(2));

                subTotalDisplay.text(subTotal.toFixed(2));
                cgstDisplay.text(totalCgst.toFixed(2));
                sgstDisplay.text(totalSgst.toFixed(2));
                igstDisplay.text(totalIgst.toFixed(2));
                grandTotalDisplay.text(grandTotal.toFixed(2));
            }

            function applyProduct($row, productId) {
                if (!productId) {
                    return;
                }
                const product = productMap[productId];
                if (!product) {
                    return;
                }

                $row.find('[name*="[item_name]"]').val(product.name);
                $row.find('[name*="[hsn_code]"]').val(product.hsn_code);
                $row.find('[name*="[rate]"]').val(product.sales_price.toFixed(2));
                $row.find('[name*="[gst_rate]"]').val(product.gst_rate.toString());
                recalcRow($row);
                refreshTotals();
            }

            function bindRow($row) {
                $row.on('input change', '.input-number', function() {
                    recalcRow($row);
                    refreshTotals();
                });
                $row.find('.product-selector').on('change', function() {
                    applyProduct($row, $(this).val());
                });
                $row.find('.remove-row').on('click', function() {
                    if (itemsBody.find('tr').length > 1) {
                        $row.remove();
                        refreshTotals();
                    }
                });
            }

            customerRadios.on('change', toggleClientSection);
            toggleClientSection();

            clientSelector.on('change', function() {
                fetchClientDetails($(this).val());
            });

            $('#buyer_gstin').on('input', function() {
                itemsBody.find('tr').each(function() {
                    recalcRow($(this));
                });
                refreshTotals();
            });

            itemsBody.find('tr').each(function() {
                const $row = $(this);
                bindRow($row);
                recalcRow($row);
            });
            refreshTotals();

            $('#add_item_row').on('click', function() {
                const index = rowIndex++;
                const rowHtml = itemRowTemplate.replace(/__INDEX__/g, index);
                const $row = $(rowHtml);
                itemsBody.append($row);
                bindRow($row);
                recalcRow($row);
                refreshTotals();
            });
        })(jQuery);
    </script>
@endsection
