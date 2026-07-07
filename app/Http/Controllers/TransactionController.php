<?php

namespace App\Http\Controllers;
use App\TransactionDetail;
use App\OrderDetail;
use App\Item;
use App\Client;
use App\Dealer;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;
class TransactionController extends Controller
{
    //

    public function index(Request $request)
    {
        $customers = Client::where('status', 'Active')->whereHas('serial')->get();
        $items = Item::get();
        $dealers = Dealer::get();
        $transactionQuery = TransactionDetail::with(['dealer', 'customer'])
            ->orderBy('id', 'desc');

        if (auth()->user()->role == "Dealer") {
            $transactionQuery->where('dealer_id', auth()->user()->id);
        }

        $summaryQuery = clone $transactionQuery;
        $summaryQuery->getQuery()->orders = null;

        $transactionSummary = $summaryQuery
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(price * qty), 0) as total_sales, COALESCE(SUM(qty), 0) as total_qty, COALESCE(SUM(points_dealer), 0) + COALESCE(SUM(points_client), 0) as total_points')
            ->first();

        $transactions = $transactionQuery
            ->select('id', 'date', 'qty', 'price', 'dealer_id', 'client_id', 'points_dealer', 'points_client', 'item', 'payment_method')
            ->paginate(50)
            ->appends($request->query());

        return view('transactions',
            array(
                'transactions' => $transactions,
                'transactionSummary' => $transactionSummary,
                'items' => $items,
                'customers' => $customers,
                'dealers' => $dealers,
            )
        );
    }

    public function adTransactions(Request $request)
    {
        $user = auth()->user();
        $centers = optional($user->ad)
            ->areas
            ? $user->ad->areas->pluck('area_name')->toArray()
            : [];

        $dealers = Dealer::whereIn('area', $centers)->get();
        
        $dealerCenters = $dealers->pluck('center')->filter()->unique()->values()->toArray();
        $customers = Client::where('status', 'Active')
            ->whereHas('serial')
            ->when(!empty($dealerCenters), function ($q) use ($dealerCenters) {
                $q->whereIn('center', $dealerCenters);
            })
            ->get();
        $items = Item::get();

        $adUser = optional(auth()->user()->ad)->id;
        $pendingOrdersCount = OrderDetail::where('ad_id', $adUser)
            ->where('status', 'Pending')
            ->count();
        
        $transactionQuery = TransactionDetail::with(['dealer', 'customer'])
            ->whereHas('adDealer', function($q) use ($centers) {
                $q->whereIn('area', $centers);
            })
            ->orderBy('id', 'desc');

        $summaryQuery = clone $transactionQuery;
        $summaryQuery->getQuery()->orders = null;

        $transactionSummary = $summaryQuery
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(price * qty), 0) as total_sales, COALESCE(SUM(qty), 0) as total_qty, COALESCE(SUM(points_dealer), 0) + COALESCE(SUM(points_client), 0) as total_points')
            ->first();

        $transactions = $transactionQuery
            ->select('id', 'date', 'qty', 'price', 'dealer_id', 'client_id', 'points_dealer', 'points_client', 'item', 'payment_method')
            ->paginate(50)
            ->appends($request->query());

        return view('area_distributor.transactions',
            array(
                'transactions' => $transactions,
                'transactionSummary' => $transactionSummary,
                'items' => $items,
                'customers' => $customers,
                'dealers' => $dealers,
                'pendingOrdersCount' => $pendingOrdersCount
            )
        );
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $item = Item::findOrfail($request->item_id);
        
        $transaction = new TransactionDetail;
        $transaction->item = $item->item;
        $transaction->points_dealer = $item->dealer_points * $request->qty;
        $transaction->points_client = $item->customer_points * $request->qty;
        $transaction->item_description = $item->item_description;
        $transaction->qty = $request->qty;
        $transaction->price = $item->price;
        $transaction->client_id = $request->customer_id;
        $transaction->date = date('Y-m-d');
        $transaction->dealer_id = auth()->user()->id;
        $transaction->created_by = auth()->user()->id;
        $transaction->save();


        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }

    public function storeAd(Request $request)
    {
        $request->validate([
            'dealer' => 'required|integer',
            'customer_id' => 'required|integer',
            'item_id' => 'required|integer',
            'qty' => 'required|numeric|min:1',
            'date' => 'nullable|date',
        ]);

        $user = auth()->user();
        $areas = optional($user->ad)
            ->areas
            ? $user->ad->areas->pluck('area_name')->toArray()
            : [];

        $dealer = Dealer::where('user_id', $request->dealer)
            ->whereIn('area', $areas)
            ->firstOrFail();

        $customer = Client::where('status', 'Active')
            ->whereHas('serial')
            ->where('id', $request->customer_id)
            ->where('center', $dealer->center)
            ->firstOrFail();

        $item = Item::findOrFail($request->item_id);

        $transaction = new TransactionDetail;
        $transaction->item = $item->item;
        $transaction->points_dealer = $item->dealer_points * $request->qty;
        $transaction->points_client = $item->customer_points * $request->qty;
        $transaction->item_description = $item->item_description;
        $transaction->qty = $request->qty;
        $transaction->price = $item->price;
        $transaction->client_id = $customer->id;
        $transaction->dealer_id = $dealer->user_id;
        $transaction->date = $request->date ?: date('Y-m-d');
        $transaction->created_by = $user->id;
        $transaction->save();

        Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }
    
    public function storeAdmin(Request $request)
    {
        // dd($request->all());
        $item = Item::findOrfail($request->item_id);


        $transaction = new TransactionDetail;
        $transaction->item = $item->item;
        $transaction->points_dealer = $item->dealer_points * $request->qty;
        $transaction->points_client = $item->customer_points * $request->qty;
        $transaction->item_description = $item->item_description;
        $transaction->qty = $request->qty;
        $transaction->price = $item->price;
        $transaction->client_id = $request->customer_id;
        $transaction->dealer_id = $request->dealer;
        $transaction->date = $request->date;
        $transaction->created_by = auth()->user()->id;
        $transaction->save();


         Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }

  public function destroy($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['error' => 'Invalid transaction ID'], 400);
            }

            $transaction = TransactionDetail::findOrFail($id);

            if (auth()->user()->role === "Dealer" && $transaction->dealer_id != auth()->user()->id) {
                return response()->json(['error' => 'Unauthorized to delete this transaction'], 403);
            }

            $transaction->delete();

            return response()->json([
                'success' => 'Transaction deleted successfully',
                'transaction_id' => $id
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete transaction'], 500);
        }
    }


   public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (!$ids || !is_array($ids) || empty($ids)) {
                return response()->json(['error' => 'No transactions selected'], 400);
            }

            $validIds = array_filter($ids, function ($id) {
                return is_numeric($id) && intval($id) > 0;
            });

            if (empty($validIds)) {
                return response()->json(['error' => 'Invalid transaction IDs provided'], 400);
            }

            $validIds = array_map('intval', $validIds);

            $query = TransactionDetail::whereIn('id', $validIds);

            if (auth()->user()->role === "Dealer") {
                $query->where('dealer_id', auth()->user()->id);
            }

            $transactions = $query->get();

            if ($transactions->isEmpty()) {
                return response()->json(['error' => 'No valid transactions found or unauthorized'], 403);
            }

            $deletedIds = $transactions->pluck('id')->toArray();
            $deletedCount = TransactionDetail::whereIn('id', $deletedIds)->delete();

            return response()->json([
                'success' => "Successfully deleted {$deletedCount} transaction(s)",
                'deleted_count' => $deletedCount,
                'deleted_ids' => $deletedIds
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete transactions'], 500);
        }
    }


       
}
