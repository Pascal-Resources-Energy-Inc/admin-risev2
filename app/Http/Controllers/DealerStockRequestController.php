<?php

namespace App\Http\Controllers;

use App\DealerStockRequest;
use App\OrderDetail;
use App\Product;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DealerStockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'Dealer', 403);
        $request->validate(['product_id' => 'required|integer|exists:dms.products,id', 'quantity' => 'required|integer|min:1|max:100000']);
        $product = Product::on('dms')->findOrFail($request->product_id);

        DB::transaction(function () use ($request, $product) {
            $existingPendingRequest = DealerStockRequest::where('dealer_id', auth()->id())
                ->where('product_id', $product->id)
                ->where('status', 'Pending')
                ->lockForUpdate()
                ->first();
            abort_if($this->stockForDealer($product, auth()->id()) > 0, 422, 'You already have stock for this item. You cannot request more until all current stock is used.');
            abort_if($existingPendingRequest, 422, 'This item already has a pending stock request.');

            DealerStockRequest::create([
                'dealer_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'status' => 'Pending',
                'remarks' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'approved_order_id' => null,
            ]);
        });
        return redirect()->route('dealer.stock.inventory')->with('success', 'Stock request submitted for admin approval.');
    }

    public function adminIndex()
    {
        $this->authenticatedAdmin();

        $counts = DealerStockRequest::selectRaw(
            "COUNT(*) as total_count,
             SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
             SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved_count,
             SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected_count,
             SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_count"
        )->first();

        return view('admin_stock_requests', compact('counts'));
    }

    public function datatable(Request $request)
    {
        $this->authenticatedAdmin();

        $query = DealerStockRequest::with(['dealer.dealer', 'reviewer'])
            ->select('dealer_stock_requests.*');

        $status = trim((string) $request->input('status'));
        if (in_array($status, ['Pending', 'Approved', 'Rejected', 'Cancelled'], true)) {
            $query->where('dealer_stock_requests.status', $status);
        }

        $productNames = Product::on('dms')
            ->select('id', 'product_name')
            ->get()
            ->pluck('product_name', 'id');

        return \Yajra\DataTables\Facades\DataTables::eloquent($query)
            ->filter(function ($builder) use ($request, $productNames) {
                $search = trim((string) $request->input('search.value'));
                if ($search === '') {
                    return;
                }

                $matchingProductIds = $productNames
                    ->filter(function ($name) use ($search) {
                        return stripos((string) $name, $search) !== false;
                    })
                    ->keys()
                    ->all();

                $builder->where(function ($filtered) use ($search, $matchingProductIds) {
                    $filtered->where('dealer_stock_requests.id', 'like', '%' . $search . '%')
                        ->orWhere('dealer_stock_requests.status', 'like', '%' . $search . '%')
                        ->orWhereHas('dealer', function ($dealer) use ($search) {
                            $dealer->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('dealer.dealer', function ($profile) use ($search) {
                            $profile->where('store_name', 'like', '%' . $search . '%')
                                ->orWhere('area', 'like', '%' . $search . '%');
                        });

                    if (!empty($matchingProductIds)) {
                        $filtered->orWhereIn('dealer_stock_requests.product_id', $matchingProductIds);
                    }
                });
            })
            ->addColumn('dealer_info', function ($stockRequest) {
                $dealer = $stockRequest->dealer;
                $profile = optional($dealer)->dealer;
                $name = optional($dealer)->name ?: 'Dealer #' . $stockRequest->dealer_id;
                $store = optional($profile)->store_name ?: 'Dealer account';
                $area = trim((string) optional($profile)->area) ?: 'Area not assigned';
                $initial = mb_strtoupper(mb_substr($name, 0, 1));

                return '<div class="dealer-cell"><span class="dealer-avatar">' . e($initial) . '</span><div><strong>' . e($name) . '</strong><small><i class="bi bi-shop"></i>' . e($store) . '</small><small><i class="bi bi-geo-alt"></i>' . e($area) . '</small></div></div>';
            })
            ->addColumn('product_info', function ($stockRequest) use ($productNames) {
                $name = $productNames->get($stockRequest->product_id) ?: 'Product #' . $stockRequest->product_id;
                return '<div class="product-cell"><i class="bi bi-box-seam"></i><div><strong>' . e($name) . '</strong><small>Request #' . e($stockRequest->id) . '</small></div></div>';
            })
            ->editColumn('quantity', function ($stockRequest) {
                return '<span class="quantity-cell">' . number_format((int) $stockRequest->quantity) . '<small>units</small></span>';
            })
            ->addColumn('submitted_at', function ($stockRequest) {
                return '<time class="date-cell" datetime="' . e(optional($stockRequest->created_at)->toIso8601String()) . '"><strong>' . e(optional($stockRequest->created_at)->format('M d, Y')) . '</strong><small>' . e(optional($stockRequest->created_at)->format('g:i A')) . '</small></time>';
            })
            ->addColumn('attachments_display', function ($stockRequest) {
                if ($stockRequest->attachment_links->isEmpty()) {
                    return '<span class="empty-value"><i class="bi bi-dash"></i>None</span>';
                }

                return $stockRequest->attachment_links->map(function ($path, $index) use ($stockRequest) {
                    $url = route('admin.stock.requests.attachments.view', ['id' => $stockRequest->id, 'index' => $index]);
                    return '<a class="attachment-link" href="' . e($url) . '" target="_blank" rel="noopener"><i class="bi bi-paperclip"></i>File ' . ($index + 1) . '</a>';
                })->implode('');
            })
            ->addColumn('status_badge', function ($stockRequest) {
                $status = strtolower((string) $stockRequest->status);
                $icons = ['pending' => 'bi-clock', 'approved' => 'bi-check-lg', 'rejected' => 'bi-x-lg', 'cancelled' => 'bi-slash-circle'];
                $review = '';
                if ($stockRequest->status !== 'Pending' && $stockRequest->reviewer) {
                    $review = '<small>by ' . e($stockRequest->reviewer->name) . '</small>';
                }

                return '<div class="status-cell"><span class="status-pill ' . e($status) . '"><i class="bi ' . ($icons[$status] ?? 'bi-circle') . '"></i>' . e($stockRequest->status) . '</span>' . $review . '</div>';
            })
            ->addColumn('actions', function ($stockRequest) use ($productNames) {
                if ($stockRequest->status !== 'Pending') {
                    return '<span class="action-complete"><i class="bi bi-check2-circle"></i>Reviewed</span>';
                }

                $dealerName = optional($stockRequest->dealer)->name ?: 'Dealer #' . $stockRequest->dealer_id;
                $productName = $productNames->get($stockRequest->product_id) ?: 'Product #' . $stockRequest->product_id;
                $approveUrl = route('admin.stock.requests.approve', ['id' => $stockRequest->id]);
                $rejectUrl = route('admin.stock.requests.reject', ['id' => $stockRequest->id]);

                return '<div class="request-actions"><button class="button button-approve" type="button" data-approve-url="' . e($approveUrl) . '" data-dealer="' . e($dealerName) . '" data-product="' . e($productName) . '" data-quantity="' . e($stockRequest->quantity) . '"><i class="bi bi-check-lg"></i>Approve</button><button class="button button-reject" type="button" data-reject-url="' . e($rejectUrl) . '" data-dealer="' . e($dealerName) . '" data-product="' . e($productName) . '"><i class="bi bi-x-lg"></i>Reject</button></div>';
            })
            ->rawColumns(['dealer_info', 'product_info', 'quantity', 'submitted_at', 'attachments_display', 'status_badge', 'actions'])
            ->toJson();
    }

    public function viewAttachment($id, $index)
    {
        $this->authenticatedAdmin();

        $stockRequest = DealerStockRequest::findOrFail($id);
        $path = $stockRequest->attachment_links->get((int) $index);
        $relativePath = ltrim(str_replace('\\', '/', (string) $path), '/');

        abort_unless(
            $path && strpos($relativePath, 'uploads/stock-request-attachments/') === 0,
            404,
            'Attachment not found.'
        );

        $file = base_path('../crms-risev2/public/' . $relativePath);
        abort_unless(File::isFile($file), 404, 'This attachment file is no longer available.');

        return response()->file($file, [
            'Content-Disposition' => 'inline; filename="' . basename($file) . '"',
        ]);
    }

    public function approve(Request $request, $id)
    {
        $admin = $this->authenticatedAdmin();
        $request->validate(['quantity' => 'required|integer|min:1|max:100000']);
        $approvedQuantity = (int) $request->quantity;

        $error = DB::transaction(function () use ($id, $admin, $approvedQuantity) {
            $stockRequest = DealerStockRequest::lockForUpdate()->findOrFail($id);

            if ($stockRequest->status !== 'Pending') {
                return 'This request has already been ' . strtolower($stockRequest->status) . '.';
            }

            if ($approvedQuantity > $stockRequest->quantity) {
                return 'The approved quantity cannot be higher than the requested quantity of ' . number_format($stockRequest->quantity) . ' units.';
            }

            // Stock request product IDs belong to the DMS product database.
            $product = Product::on('dms')->findOrFail($stockRequest->product_id);

            if ($this->stockForDealer($product, $stockRequest->dealer_id) > 0) {
                return 'The dealer already has this item in stock. Approval is blocked.';
            }

            $last = OrderDetail::lockForUpdate()->latest('id')->first();
            $order = new OrderDetail;
            if (Schema::hasColumn('order_details', 'product_id')) $order->product_id = $product->id;
            $order->item = $product->product_name;
            if (Schema::hasColumn('order_details', 'sku')) $order->sku = $product->sku;
            $order->transaction_id = 'STK-' . str_pad(($last ? $last->id : 0) + 1, 6, '0', STR_PAD_LEFT);
            $order->item_description = $product->description;
            $order->qty = $approvedQuantity;
            $order->price = $product->dealer_price ?? $product->price ?? 0;
            $order->date = now()->toDateString();
            $order->dealer_id = $stockRequest->dealer_id;
            $order->created_by = $admin->id;
            $order->status = 'Completed';
            if (Schema::hasColumn('order_details', 'completed_at')) $order->completed_at = now();
            if (Schema::hasColumn('order_details', 'remarks')) $order->remarks = 'Dealer stock request approved by ' . $admin->name;
            $order->save();
            $stockRequest->update([
                'quantity' => $approvedQuantity,
                'status' => 'Approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'approved_order_id' => $order->id,
            ]);

            return null;
        });

        if ($error) {
            return back()->withErrors(['stock_request' => $error]);
        }

        return back()->with('success', 'Approved. The item has been added to the dealer inventory.');
    }

    public function reject(Request $request, $id)
    {
        $admin = $this->authenticatedAdmin();

        $request->validate(['remarks' => 'required|string|max:500']);
        $error = DB::transaction(function () use ($request, $id, $admin) {
            $stockRequest = DealerStockRequest::lockForUpdate()->findOrFail($id);

            if ($stockRequest->status !== 'Pending') {
                return 'This request has already been ' . strtolower($stockRequest->status) . '.';
            }

            $stockRequest->update([
                'status' => 'Rejected',
                'remarks' => trim($request->remarks),
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            return null;
        });

        if ($error) {
            return back()->withErrors(['stock_request' => $error]);
        }

        return back()->with('success', 'Request rejected and the dealer has been notified in their inventory.');
    }

    private function authenticatedAdmin()
    {
        $user = auth()->user();

        abort_unless(
            $user && strcasecmp(trim((string) $user->role), 'Admin') === 0,
            403,
            'Only administrators can review stock requests.'
        );

        return $user;
    }

    private function stockForDealer(Product $product, $dealerId)
    {
        $orders = OrderDetail::where('dealer_id', $dealerId)->where('status', 'Completed')->where(function ($q) use ($product) {
            Schema::hasColumn('order_details', 'product_id') ? $q->where('product_id', $product->id)->orWhere('item', $product->product_name) : $q->where('item', $product->product_name);
        })->sum('qty');
        $sales = TransactionDetail::where('dealer_id', $dealerId)->when(Schema::hasColumn('transaction_details', 'status'), function ($q) { $q->where('status', 'Completed'); })->where(function ($q) use ($product) {
            Schema::hasColumn('transaction_details', 'product_id') ? $q->where('product_id', $product->id)->orWhere('item', $product->product_name)->orWhere('item_description', $product->product_name) : $q->where('item', $product->product_name)->orWhere('item_description', $product->product_name);
        })->sum('qty');
        return max(0, (int) $orders - (int) $sales);
    }
}
