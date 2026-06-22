@extends('layouts.header')

@section('content')
<style>
    .adpo-page {
        max-width: 760px;
        margin: 24px auto 48px;
        background: #fff;
        border: 1px solid #dfe5ef;
        box-shadow: 0 16px 45px rgba(15, 23, 42, .08);
    }
    .adpo-top-alert {
        background: #dc2626;
        color: #fff;
        padding: 8px 18px;
        font-size: 12px;
        text-align: center;
    }
    .adpo-logo {
        max-width: 170px;
        height: auto;
    }
    .adpo-title {
        text-align: center;
        padding: 24px 18px 14px;
        border-bottom: 3px solid #dc2626;
        width: 270px;
        margin: 0 auto 24px;
    }
    .adpo-body {
        max-width: 640px;
        margin: 0 auto;
        padding: 0 18px 24px;
    }
    .adpo-panel {
        border-left: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        padding: 16px 22px;
    }
    .adpo-required {
        background: #fff1f2;
        border-top: 2px solid #ef4444;
        color: #991b1b;
        padding: 10px 12px;
        font-size: 12px;
        margin-bottom: 14px;
    }
    .adpo-label {
        font-size: 12px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 5px;
    }
    .adpo-help {
        font-size: 11px;
        color: #64748b;
    }
    .adpo-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .adpo-product {
        position: relative;
        border: 1px solid #e5e7eb;
        background: #fff;
        min-height: 250px;
        padding: 8px;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .adpo-product:hover {
        border-color: #93c5fd;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .12);
        transform: translateY(-1px);
    }
    .adpo-product.selected {
        border-color: #2563eb;
        box-shadow: inset 0 0 0 1px #2563eb;
    }
    .adpo-product.low-stock {
        border-color: #f59e0b;
        background: #fffdf7;
    }
    .adpo-product.out-stock {
        border-color: #e5e7eb;
        background: #f8fafc;
        opacity: .72;
    }
    .adpo-product.out-stock:hover {
        border-color: #e5e7eb;
        box-shadow: none;
        transform: none;
    }
    .adpo-stock-pill {
        position: absolute;
        top: 7px;
        right: 7px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        background: #ecfdf3;
        color: #027a48;
        border: 1px solid #abefc6;
    }
    .adpo-stock-pill.low {
        background: #fffaeb;
        color: #b54708;
        border-color: #fedf89;
    }
    .adpo-stock-pill.none {
        background: #fef2f2;
        color: #b42318;
        border-color: #fecaca;
    }
    .adpo-stock-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 6px;
    }
    .adpo-stock-note {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
    }
    .adpo-stock-note.none {
        color: #b42318;
    }
    .adpo-product-check {
        position: absolute;
        top: 7px;
        left: 7px;
        z-index: 1;
    }
    .adpo-product-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        background: #f8fafc;
        margin-bottom: 8px;
    }
    .adpo-product-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        min-height: 32px;
    }
    .adpo-product-desc {
        font-size: 10px;
        color: #64748b;
        min-height: 28px;
    }
    .adpo-price {
        font-size: 12px;
        font-weight: 800;
        color: #2563eb;
        margin: 5px 0;
    }
    .adpo-qty {
        width: 72px;
        height: 32px;
        font-size: 12px;
    }
    .adpo-summary {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 0 0;
        margin-top: 18px;
    }
    .grid-column-full {
        grid-column: 1 / -1;
    }
    .adpo-empty-stock {
        grid-column: 1 / -1;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 14px;
        border: 1px solid #fedf89;
        background: #fffaeb;
        color: #92400e;
        font-size: 12px;
    }
    @media (max-width: 640px) {
        .adpo-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .adpo-panel {
            padding: 14px;
        }
    }
</style>

<section class="adpo-page">
    <div class="adpo-top-alert">
        There is a minimum order amount before you can submit your purchase order.
    </div>

    <div class="adpo-title">
        <img src="{{ asset('images/logo_nya.png') }}" class="adpo-logo" alt="Gaz Lite">
        <div class="mt-2">Area Distributor Purchase Order</div>
        <strong>(ADPO)</strong>
    </div>

    <div class="adpo-body">
        <div class="adpo-panel">
            <p class="fw-semibold mb-3">We are sorry, we have limited available stock SKUs at the moment.</p>

            @if(session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST" id="adpoForm">
                @csrf
                <div class="adpo-required">
                    <strong>Business Name / Authorized Sales Territory</strong>
                    <div class="mt-1">{{ optional($ad)->business_name ?: optional($ad)->name ?: auth()->user()->name }}</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="adpo-label">Phone Number</label>
                        <input type="text" class="form-control form-control-sm" value="{{ optional($ad)->contact_number }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="adpo-label">AD's Email Address</label>
                        <input type="email" name="ad_email" class="form-control form-control-sm" value="{{ optional($ad)->email_address ?: auth()->user()->email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="adpo-label">Area Name</label>
                        <input type="text" class="form-control form-control-sm" value="{{ optional(optional($ad)->areas)->pluck('area_name')->implode(', ') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="adpo-label">Sales Link Authorized Retail Partner Uniform?</label>
                        <input type="text" name="uniform_size" class="form-control form-control-sm" placeholder="Input size">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="adpo-label">Shipping <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_type" id="shipDelivered" value="delivered" checked>
                            <label class="form-check-label small" for="shipDelivered">Delivered</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="shipping_type" id="shipPickup" value="pickup">
                            <label class="form-check-label small" for="shipPickup">Pick Up in Authorized Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="adpo-label">Would like to utilize my rebate voucher for this transaction? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payVoucher" value="voucher" checked>
                            <label class="form-check-label small" for="payVoucher">Yes</label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash">
                            <label class="form-check-label small" for="payCash">No, pay cash</label>
                        </div>
                    </div>
                    <div class="col-md-6" id="deliveryFeeBlock">
                        <label class="adpo-label">Delivery Fee</label>
                        <input type="number" name="delivery_fee" id="adpoDeliveryFee" class="form-control form-control-sm" min="0" step="0.01" value="0">
                    </div>
                </div>

                <p class="adpo-help mb-4">
                    I have read and agree to the terms and conditions. Please review all selected SKUs and quantities before submitting your purchase order.
                </p>

                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-success px-5">Submit</button>
                </div>

                <div class="d-flex justify-content-between align-items-end mb-2">
                    <div>
                        <label class="adpo-label">Select product/s for the current purchase order</label>
                        <div class="adpo-help">Tick a product and enter quantity.</div>
                    </div>
                    <div class="text-end">
                        <div class="adpo-help">Selected items</div>
                        <strong id="adpoSelectedCount">0</strong>
                    </div>
                </div>

                <div class="adpo-grid">
                    @forelse($products as $product)
                        @php
                            $price = $product->dealer_price ?? $product->price ?? 0;
                            $availableQty = auth()->user()->role === 'Admin' ? null : (float) ($stockByProduct->get($product->id, 0) ?? 0);
                            $hasStock = $availableQty === null || $availableQty > 0;
                            $isLowStock = $availableQty !== null && $availableQty > 0 && $availableQty <= 10;
                            $image = $product->product_image && file_exists(public_path('uploads/products/' . $product->product_image))
                                ? asset('uploads/products/' . $product->product_image)
                                : asset('design/assets/images/products/empty-shopping-bag.gif');
                        @endphp
                        <div class="adpo-product {{ !$hasStock ? 'out-stock' : ($isLowStock ? 'low-stock' : '') }}" data-product-card data-stock="{{ $availableQty === null ? '' : $availableQty }}">
                            <input type="checkbox" class="form-check-input adpo-product-check" name="products[{{ $product->id }}][selected]" value="1" data-product-check {{ !$hasStock ? 'disabled' : '' }}>
                            @if($availableQty !== null)
                                <span class="adpo-stock-pill {{ !$hasStock ? 'none' : ($isLowStock ? 'low' : '') }}">
                                    <i class="bi {{ !$hasStock ? 'bi-x-circle' : ($isLowStock ? 'bi-exclamation-triangle' : 'bi-check2-circle') }}"></i>
                                    {{ !$hasStock ? 'No stock' : number_format($availableQty) . ' left' }}
                                </span>
                            @endif
                            <img src="{{ $image }}" class="adpo-product-img" alt="{{ $product->product_name }}">
                            <div class="adpo-product-title">{{ $product->product_name }}</div>
                            <div class="adpo-product-desc">{{ \Illuminate\Support\Str::limit($product->description, 48) }}</div>
                            <div class="adpo-price">PHP {{ number_format($price, 2) }}</div>
                            <div class="adpo-stock-line">
                                <label class="adpo-help mb-0">Quantity</label>
                                @if($availableQty !== null)
                                    <span class="adpo-stock-note {{ !$hasStock ? 'none' : '' }}">
                                        {{ !$hasStock ? 'Unavailable' : 'Max ' . number_format($availableQty) }}
                                    </span>
                                @endif
                            </div>
                            <input type="number"
                                class="form-control form-control-sm adpo-qty"
                                name="products[{{ $product->id }}][qty]"
                                value="0"
                                min="0"
                                @if($availableQty !== null) max="{{ $availableQty }}" @endif
                                step="1"
                                data-price="{{ $price }}"
                                data-qty
                                {{ !$hasStock ? 'disabled' : '' }}>
                        </div>
                    @empty
                        <div class="alert alert-warning grid-column-full">
                            No active products available for purchase order.
                        </div>
                    @endforelse
                    @if($products->isNotEmpty() && auth()->user()->role !== 'Admin' && $stockByProduct->filter(fn($qty) => (float) $qty > 0)->isEmpty())
                        <div class="adpo-empty-stock">
                            <i class="bi bi-box-seam"></i>
                            <div>
                                <strong>No available stock for your AD account.</strong>
                                <div>Products are visible for reference, but ordering is locked until stock is added or completed ADPO stock becomes available.</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="adpo-summary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="adpo-help">Before shipping</div>
                            <strong id="adpoSubtotal">0.00PHP</strong>
                        </div>
                        <div class="text-end">
                            <div class="adpo-help">Total</div>
                            <strong id="adpoTotal">0.00PHP</strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('adpoForm');
    const subtotalText = document.getElementById('adpoSubtotal');
    const totalText = document.getElementById('adpoTotal');
    const selectedText = document.getElementById('adpoSelectedCount');
    const deliveryFee = document.getElementById('adpoDeliveryFee');
    const deliveryFeeBlock = document.getElementById('deliveryFeeBlock');
    const shippingInputs = document.querySelectorAll('input[name="shipping_type"]');
    const submitButtons = form.querySelectorAll('button[type="submit"]');

    function formatMoney(value) {
        return Number(value || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + 'PHP';
    }

    function calculate() {
        let subtotal = 0;
        let selected = 0;
        let hasOrderableStock = false;

        document.querySelectorAll('[data-product-card]').forEach(function(card) {
            const checkbox = card.querySelector('[data-product-check]');
            const qtyInput = card.querySelector('[data-qty]');
            const stock = card.dataset.stock === '' ? null : parseFloat(card.dataset.stock || 0);
            const isOutOfStock = stock !== null && stock <= 0;
            let qty = parseFloat(qtyInput.value || 0);
            const price = parseFloat(qtyInput.dataset.price || 0);

            if (isOutOfStock) {
                qty = 0;
                qtyInput.value = 0;
                checkbox.checked = false;
                qtyInput.disabled = true;
                checkbox.disabled = true;
            } else {
                hasOrderableStock = true;
                qtyInput.disabled = false;
                checkbox.disabled = false;

                if (stock !== null && qty > stock) {
                    qty = stock;
                    qtyInput.value = stock;
                    Swal.fire('Stock Limit Reached', 'Only ' + stock.toLocaleString() + ' available for this item.', 'info');
                }
            }

            const isSelected = !isOutOfStock && (checkbox.checked || qty > 0);

            checkbox.checked = isSelected;
            card.classList.toggle('selected', isSelected);

            if (isSelected && qty > 0) {
                selected += 1;
                subtotal += price * qty;
            }
        });

        const isDelivered = document.querySelector('input[name="shipping_type"]:checked')?.value === 'delivered';
        const shippingFee = isDelivered ? parseFloat(deliveryFee.value || 0) : 0;

        deliveryFeeBlock.classList.toggle('d-none', !isDelivered);
        deliveryFee.disabled = !isDelivered;

        subtotalText.textContent = formatMoney(subtotal);
        totalText.textContent = formatMoney(subtotal + shippingFee);
        selectedText.textContent = selected;
        submitButtons.forEach(function(button) {
            button.disabled = !hasOrderableStock;
        });
    }

    document.querySelectorAll('[data-product-check], [data-qty], #adpoDeliveryFee').forEach(function(input) {
        input.addEventListener('change', calculate);
        input.addEventListener('input', calculate);
    });

    shippingInputs.forEach(function(input) {
        input.addEventListener('change', calculate);
    });

    form.addEventListener('submit', function(event) {
        const hasSelected = Array.from(document.querySelectorAll('[data-qty]')).some(function(input) {
            return !input.disabled && parseFloat(input.value || 0) > 0;
        });

        if (!hasSelected) {
            event.preventDefault();
            const hasStock = Array.from(document.querySelectorAll('[data-product-card]')).some(function(card) {
                return card.dataset.stock === '' || parseFloat(card.dataset.stock || 0) > 0;
            });
            Swal.fire(
                hasStock ? 'No Product Selected' : 'No Stock Available',
                hasStock ? 'Please select at least one product and quantity.' : 'Your AD account has no available stock for ordering right now.',
                'warning'
            );
        }
    });

    calculate();
});
</script>
@endsection
