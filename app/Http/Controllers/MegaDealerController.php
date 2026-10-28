<?php

namespace App\Http\Controllers;

use App\Center;
use App\DmsArea;
use App\MegaDealer;
use App\TransactionDetail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class MegaDealerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->role === 'Admin', 403);
            return $next($request);
        });
    }

    public function index()
    {
        return view('mds.index', [
            'totalAds' => MegaDealer::count(),
            'activeAds' => MegaDealer::where('status', 'Active')->count(),
            'inactiveAds' => MegaDealer::where('status', 'Inactive')->count(),
            'centers' => Center::orderBy('name')->get(),
            'areas' => DmsArea::with('areaAd.distributor')->whereNotNull('name')->orderBy('name')->get(),
            'mfis' => Schema::connection('dms')->hasTable('mfis')
                ? DB::connection('dms')->table('mfis')->orderBy('name')->pluck('name')
                : collect(),
            'isMegaDealer' => true,
        ]);
    }

    public function datatable(Request $request)
    {
        $query = MegaDealer::query()
            ->leftJoin('users as mega_dealer_users', 'mega_dealer_users.id', '=', 'mega_dealers.user_id')
            ->leftJoinSub(DB::table('order_details')->select('dealer_id', DB::raw('SUM(qty) as stock_qty'))->groupBy('dealer_id'), 'mega_stock', function ($join) {
                $join->on('mega_stock.dealer_id', '=', 'mega_dealers.user_id');
            })
            ->leftJoinSub(DB::table('transaction_details')->select('dealer_id', DB::raw('SUM(qty) as sold_qty'))->groupBy('dealer_id'), 'mega_sales', function ($join) {
                $join->on('mega_sales.dealer_id', '=', 'mega_dealers.user_id');
            })
            ->select('mega_dealers.*', 'mega_stock.stock_qty', 'mega_sales.sold_qty');

        return DataTables::eloquent($query)
            ->addColumn('partner_code_display', function ($row) {
                return '<span class="dealer-ref">' . e($row->partner_code) . '</span>';
            })
            ->addColumn('name_display', function ($row) {
                return '<a class="dealer-link" href="' . route('mega-dealers.show', $row->id) . '">' . e(strtoupper($row->name)) . '</a>';
            })
            ->editColumn('store_name', function ($row) { return e(strtoupper($row->store_name ?: '-')); })
            ->addColumn('contact', function ($row) { return e($row->number ?: '-'); })
            ->addColumn('stock_display', function ($row) { return number_format($row->stock_qty ?: 0); })
            ->addColumn('sold_display', function ($row) { return number_format($row->sold_qty ?: 0); })
            ->addColumn('territory', function ($row) { return e(strtoupper($row->area ?: '-')); })
            ->addColumn('status_display', function ($row) {
                $active = $row->status === 'Active';
                return '<span class="dealer-status ' . ($active ? 'active' : 'inactive') . '">' . e($row->status ?: 'Inactive') . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex gap-1">'
                    . '<a class="btn btn-sm btn-outline-info" href="' . route('mega-dealers.show', $row->id) . '" title="View"><i class="ti ti-eye"></i></a>'
                    . '<form method="POST" action="' . route('mega-dealers.destroy', $row->id) . '" onsubmit="return confirm(\'Delete this Mega Dealer?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="ti ti-trash"></i></button></form></div>';
            })
            ->rawColumns(['partner_code_display', 'name_display', 'status_display', 'actions'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate($this->rules($request));
        $name = $this->fullName($request);
        if (MegaDealer::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->exists()) {
            return back()->withErrors(['dealer_duplicate' => 'A Mega Dealer with this name already exists.'])->withInput();
        }

        $megaDealer = DB::transaction(function () use ($request, $name) {
            $user = User::create([
                'name' => $name,
                'email' => $request->email_address,
                'role' => 'Mega Dealer',
                'password' => bcrypt('12345678'),
            ]);

            return MegaDealer::create($this->payload($request, $name) + [
                'user_id' => $user->id,
                'partner_code' => $this->nextPartnerCode(),
                'status' => 'Active',
            ]);
        });

        Alert::success('Success', 'Mega Dealer created successfully.')->persistent('Dismiss');
        return redirect()->route('mega-dealers.show', $megaDealer->id);
    }

    public function checkDuplicate(Request $request)
    {
        $name = $this->fullName($request);
        return response()->json(['exists' => $name !== '' && MegaDealer::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->exists()]);
    }

    public function show($id)
    {
        $megaDealer = MegaDealer::with('user')->findOrFail($id);
        return view('dealer', [
            'dealer' => $megaDealer,
            'transactions' => TransactionDetail::where('dealer_id', $megaDealer->user_id)->orderByDesc('id')->get(),
            'centers' => Center::orderBy('name')->get(),
            'areas' => DmsArea::with('areaAd.distributor')->whereNotNull('name')->orderBy('name')->get(),
            'mfis' => Schema::connection('dms')->hasTable('mfis') ? DB::connection('dms')->table('mfis')->orderBy('name')->pluck('name') : collect(),
            'isMegaDealer' => true,
        ]);
    }

    public function changeAvatar(Request $request, $id)
    {
        $request->validate(['image_data' => 'required|string']);
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,/i', $request->image_data, $matches)) {
            return back()->withErrors(['image_data' => 'Please select a valid image.']);
        }
        $bytes = base64_decode(substr($request->image_data, strpos($request->image_data, ',') + 1), true);
        if ($bytes === false) {
            return back()->withErrors(['image_data' => 'The selected image could not be processed.']);
        }

        $megaDealer = MegaDealer::findOrFail($id);
        $directory = public_path('avatar-dealer');
        if (! is_dir($directory)) mkdir($directory, 0755, true);
        $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $fileName = 'avatar_mega_dealer_' . $megaDealer->id . '_' . time() . '.' . $extension;
        file_put_contents($directory . DIRECTORY_SEPARATOR . $fileName, $bytes);
        $megaDealer->avatar = 'avatar-dealer/' . $fileName;
        $megaDealer->save();

        Alert::success('Success', 'Mega Dealer avatar updated.')->persistent('Dismiss');
        return back();
    }

    public function uploadValidId(Request $request, $id)
    {
        $request->validate([
            'valid_id_type' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:100',
            'id_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $megaDealer = MegaDealer::findOrFail($id);
        $directory = public_path('valid_ids');
        if (! is_dir($directory)) mkdir($directory, 0755, true);
        $attachment = $request->file('id_file');
        $name = time() . '_' . uniqid() . '.' . $attachment->getClientOriginalExtension();
        $attachment->move($directory, $name);
        $megaDealer->valid_id = $request->valid_id_type;
        $megaDealer->valid_id_number = $request->id_number;
        $megaDealer->valid_file = '/valid_ids/' . $name;
        $megaDealer->save();

        Alert::success('Success', 'Mega Dealer ID uploaded.')->persistent('Dismiss');
        return back();
    }

    public function contractSign(Request $request, $id)
    {
        $request->validate(['contract_signature' => 'required|file|mimes:png,jpg,jpeg|max:5120']);
        $megaDealer = MegaDealer::findOrFail($id);
        $directory = public_path('signatures');
        if (! is_dir($directory)) mkdir($directory, 0755, true);
        $attachment = $request->file('contract_signature');
        $name = time() . '_mega_dealer_' . $megaDealer->id . '.' . $attachment->getClientOriginalExtension();
        $attachment->move($directory, $name);
        $megaDealer->signature = '/signatures/' . $name;
        $megaDealer->contract = now();
        $megaDealer->save();

        Alert::success('Success', 'Mega Dealer contract signed.')->persistent('Dismiss');
        return redirect()->route('mega-dealers.show', $megaDealer->id);
    }

    public function sign($id)
    {
        return view('signature_dealer', ['dealer' => MegaDealer::findOrFail($id), 'isMegaDealer' => true]);
    }

    public function update(Request $request, $id)
    {
        $megaDealer = MegaDealer::findOrFail($id);
        $request->validate($this->rules($request, $megaDealer));
        $name = $this->fullName($request);
        if (MegaDealer::where('id', '!=', $id)->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])->exists()) {
            return back()->withErrors(['name' => 'A Mega Dealer with this name already exists.'])->withInput();
        }

        DB::transaction(function () use ($request, $megaDealer, $name) {
            if ($megaDealer->user_id) {
                User::whereKey($megaDealer->user_id)->update(['name' => $name, 'email' => $request->email_address]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $request->email_address,
                    'role' => 'Mega Dealer',
                    'password' => bcrypt('12345678'),
                ]);
                $megaDealer->user_id = $user->id;
            }
            $megaDealer->update($this->payload($request, $name) + ['status' => $request->status]);
        });

        Alert::success('Success', 'Mega Dealer updated successfully.')->persistent('Dismiss');
        return redirect()->route('mega-dealers.show', $megaDealer->id);
    }

    public function destroy($id)
    {
        $megaDealer = MegaDealer::findOrFail($id);
        DB::transaction(function () use ($megaDealer) {
            $megaDealer->delete();
            if ($megaDealer->user_id) {
                User::whereKey($megaDealer->user_id)->update(['role' => 'Inactive Mega Dealer']);
            }
        });
        Alert::success('Deleted', 'Mega Dealer removed successfully.')->persistent('Dismiss');
        return redirect()->route('mds');
    }

    private function rules(Request $request, MegaDealer $megaDealer = null)
    {
        $email = Rule::unique('users', 'email');
        if ($megaDealer && $megaDealer->user_id) {
            $email->ignore($megaDealer->user_id);
        }

        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'mothers_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before_or_equal:today',
            'email_address' => ['required', 'email', $email],
            'number' => 'nullable|string|max:50',
            'facebook' => 'nullable|string|max:255',
            'store_name' => 'required|string|max:255',
            'store_type' => 'required|string|max:255',
            'mfi' => 'required|string|max:100|exists:dms.mfis,name',
            'center' => [
                'required',
                'string',
                'max:255',
                Rule::exists('centers', 'name')->where(function ($query) use ($request) {
                    $query->where('mfi', $request->mfi);
                }),
            ],
            'spo' => 'required|string|max:255',
            'area' => 'required|string|max:255|exists:dms.areas,name',
            'status' => $megaDealer ? 'required|in:Active,Inactive' : 'nullable',
            'street_address' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }

    private function fullName(Request $request)
    {
        return trim(collect([$request->first_name, $request->middle_name, $request->last_name])->filter()->implode(' '));
    }

    private function payload(Request $request, $name)
    {
        return [
            'name' => $name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'mothers_name' => $request->mothers_name,
            'birthdate' => $request->birthdate,
            'age' => $request->birthdate ? Carbon::parse($request->birthdate)->age : null,
            'email_address' => $request->email_address,
            'number' => $request->number,
            'facebook' => $request->facebook,
            'store_name' => $request->store_name,
            'store_type' => $request->store_type,
            'mfi' => $request->mfi,
            'center' => $request->center,
            'spo' => $request->spo,
            'area' => $request->area,
            'address' => $request->address,
            'street_address' => $request->street_address,
            'location_region' => $request->location_region,
            'location_province' => $request->location_province,
            'location_city' => $request->location_city,
            'location_barangay' => $request->location_barangay,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
    }

    private function nextPartnerCode()
    {
        $lastCode = MegaDealer::withTrashed()->where('partner_code', 'like', 'PRMD%')->orderByRaw('CAST(SUBSTRING(partner_code, 5) AS UNSIGNED) DESC')->lockForUpdate()->value('partner_code');
        $sequence = $lastCode && preg_match('/^PRMD(\d+)$/', $lastCode, $matches) ? (int) $matches[1] : 0;
        return 'PRMD' . str_pad($sequence + 1, 5, '0', STR_PAD_LEFT);
    }
}
