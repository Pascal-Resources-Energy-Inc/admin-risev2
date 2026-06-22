<div class="modal fade" id="purchaseOrderModal" tabindex="-1" aria-labelledby="purchaseOrderModalLabel" aria-hidden="true">
    <style>
        .po-stock-panel {
            border: 1px solid #d8e2ef;
            background: #f8fafc;
        }

        .po-stock-panel.is-ok {
            border-color: #9bd4b4;
            background: #f1fbf5;
        }

        .po-stock-panel.is-low {
            border-color: #f4c36d;
            background: #fff8e8;
        }

        .po-stock-panel.is-out {
            border-color: #f0a5a5;
            background: #fff2f2;
        }

        .po-stock-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
        }

        .po-stock-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .po-stock-metric {
            border: 1px solid #e3e8ef;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            min-width: 0;
        }

        .po-stock-metric span {
            display: block;
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .po-stock-metric strong {
            display: block;
            font-size: 15px;
            line-height: 1.2;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .po-stock-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            @php
                $tracksStock = auth()->user()->role !== 'Admin';
                $stockByAreaProduct = $stockByAreaProduct ?? collect();
                $inventoryStatsByAreaProduct = $inventoryStatsByAreaProduct ?? collect();
                $stockByAreaProductPayload = $stockByAreaProduct->map(function($areaStock) {
                    return collect($areaStock)->map(function($qty) {
                        return (float) $qty;
                    })->all();
                })->all();
                $inventoryStatsPayload = $inventoryStatsByAreaProduct->map(function($areaStats) {
                    return collect($areaStats)->map(function($stats) {
                        return [
                            'stock_after_movement' => (float) ($stats['stock_after_movement'] ?? 0),
                            'sales_orders' => (float) ($stats['sales_orders'] ?? 0),
                            'available' => (float) ($stats['available'] ?? 0),
                            'status' => $stats['status'] ?? 'No stock',
                        ];
                    })->all();
                })->all();
                $inStockProducts = $tracksStock
                    ? collect($products)->filter(function($product) use ($stockByAreaProduct) {
                        return $stockByAreaProduct->contains(function($areaStock) use ($product) {
                            return (float) (collect($areaStock)->get($product->id, 0) ?? 0) > 0;
                        });
                    })->count()
                    : $products->count();
                $purchaseDisabled = $dealers->count() === 0 || $products->count() === 0 || ($tracksStock && $inStockProducts === 0);
            @endphp
            <script>
                window.poAreaStock = @json($stockByAreaProductPayload);
                window.poInventoryStats = @json($inventoryStatsPayload);
            </script>
            <form action="{{ route('orders.store') }}" method="POST" id="purchaseOrderForm">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="purchaseOrderModalLabel">New Purchase Order</h5>
                        <small class="text-muted">Create a pending dealer order under the selected Area Distributor product.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if($dealers->count() === 0 || $products->count() === 0)
                        <div class="alert alert-warning mb-0">
                            You need at least one active dealer and one active product before creating a purchase order.
                        </div>
                    @elseif($tracksStock && $inStockProducts === 0)
                        <div class="alert alert-warning mb-0">
                            No products have available area stock for your dealer areas right now. Please contact an admin to restock before creating a purchase order.
                        </div>
                    @else
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="poDealer" class="form-label">Dealer <span class="text-danger">*</span></label>
                                <select name="dealer_id" id="poDealer" class="form-select select2" data-placeholder="Select dealer" required>
                                    <option value="">Select dealer</option>
                                    @foreach($dealers as $dealer)
                                        <option value="{{ $dealer->user_id }}"
                                            data-area="{{ $dealer->area }}"
                                            data-center="{{ $dealer->center }}"
                                            data-address="{{ $dealer->address ?: trim(($dealer->street_address ?? '') . ' ' . ($dealer->location_barangay ?? '') . ' ' . ($dealer->location_city ?? '')) }}">
                                            {{ $dealer->name }} @if($dealer->area) - {{ $dealer->area }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="poProduct" class="form-label">Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="poProduct" class="form-select select2" data-placeholder="Select product" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        @php
                                            $poPrice = $product->dealer_price ?? $product->price;
                                            $tracksStock = auth()->user()->role !== 'Admin';
                                            $availableStock = (float) ($stockByProduct->get($product->id, 0) ?? 0);
                                        @endphp
                                        <option value="{{ $product->id }}"
                                            data-price="{{ $poPrice }}"
                                            data-points="{{ $product->dealer_points }}"
                                            data-sku="{{ $product->sku }}"
                                            data-description="{{ $product->description }}"
                                            data-stock="{{ $tracksStock ? $availableStock : '' }}"
                                            data-track-stock="{{ $tracksStock ? '1' : '0' }}">
                                            {{ $product->product_name }} - PHP {{ number_format($poPrice, 2) }}
                                            @if($tracksStock)
                                                - check dealer area stock
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="poQty" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="poQty" class="form-control" min="1" step="1" value="1" required>
                            </div>

                            <div class="col-md-3">
                                <label for="poDate" class="form-label">Order Date</label>
                                <input type="date" name="date" id="poDate" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="poPayment" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="poPayment" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="poDeliveryType" class="form-label">Delivery Type <span class="text-danger">*</span></label>
                                <select name="delivery_type" id="poDeliveryType" class="form-select" required>
                                    <option value="pickup">Pickup</option>
                                    <option value="delivery">Delivery</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-none" id="poDeliveryFeeWrap">
                                <label for="poDeliveryFee" class="form-label">Delivery Fee</label>
                                <input type="number" name="delivery_fee" id="poDeliveryFee" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>

                            <div class="col-md-8">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <div class="row g-2 small">
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Dealer Area</span>
                                            <strong id="poDealerArea">-</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">SKU</span>
                                            <strong id="poSku">-</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Unit Price</span>
                                            <strong id="poUnitPrice">PHP 0.00</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block">Dealer Points</span>
                                            <strong id="poPoints">0</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" id="poStockAlertWrap">
                                <div id="poStockAlert" class="alert d-none mb-3" role="alert"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="po-stock-panel rounded p-3 h-100 d-flex align-items-center gap-3" id="poStockPanel">
                                    <div class="po-stock-icon rounded-circle bg-white border d-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-seam fs-4 text-secondary" id="poStockIcon"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Dealer Area Stock</div>
                                        <strong class="d-block" id="poStockStatus">Select a product</strong>
                                        <small class="text-muted" id="poStockHelp">Choose a dealer and product to check area stock.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="po-stock-metrics" id="poStockMetrics">
                                    <div class="po-stock-metric">
                                        <span>Stock After Movement</span>
                                        <strong id="poStockAfterMovement">-</strong>
                                    </div>
                                    <div class="po-stock-metric">
                                        <span>Sales Orders</span>
                                        <strong id="poSalesOrders">-</strong>
                                    </div>
                                    <div class="po-stock-metric">
                                        <span>Available</span>
                                        <strong id="poAvailableQty">-</strong>
                                    </div>
                                    <div class="po-stock-metric">
                                        <span>Status</span>
                                        <strong id="poInventoryStatus">-</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-between align-items-center border rounded p-3">
                                    <div>
                                        <div class="text-muted small">Estimated Total</div>
                                        <div class="h4 mb-0" id="poTotal">PHP 0.00</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Status</div>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="poSubmitBtn" {{ $purchaseDisabled ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-1"></i> Create Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
