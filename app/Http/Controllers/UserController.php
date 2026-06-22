<?php

namespace App\Http\Controllers;
use App\User;
use App\Dealer;
use App\Client;
use App\Stove;
use App\Area;
use App\TransactionDetail;
use App\AreaDistributor;
use App\AreaAd;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Facades\Auditor;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $stoves = Stove::whereNull('client_id')->get(['id', 'serial_number']);
        $areas = Area::get();
        $users = User::with([
                'dealer:id,user_id,address,status',
                'client:id,user_id,address,status',
                'ad'
            ])
            ->select('id', 'name', 'email', 'role', 'address', 'can_add', 'can_edit')
            ->latest()
            ->paginate(20); // ✅ IMPORTANT

        return view('users.index', compact('stoves', 'users', 'areas'));
    }

    public function store(Request $request)
    {
       $isAdmin = $request->role === 'Admin';
       $needsDeliveryAddress = in_array($request->role, ['Area Distributor', 'Provincial Distributor'], true);

       if ($isAdmin && $request->has('same_as_address')) {
            $request->merge([
                'delivery_address' => $request->address,
            ]);
       }

       if ($needsDeliveryAddress && $request->has('same_as_delivery_address')) {
            $request->merge([
                'delivery_address' => $request->address,
            ]);
       }

       if ($needsDeliveryAddress && trim((string) $request->delivery_address) === '') {
            return back()
                ->withErrors(['delivery_address' => 'Delivery address is required.'])
                ->withInput();
       }

       $request->validate([
            'warehouse' => 'nullable|in:lubao,guinobatan',
            'delivery_address' => 'nullable|string|max:1000',
            'designation' => 'required_if:role,Admin|nullable|string|max:255',
            'employee_number' => 'required_if:role,Admin|nullable|string|max:255',
            'department' => 'required_if:role,Admin|nullable|string|max:255',
        ]);

       $duplicate = false;

       if (!$isAdmin) {
            $duplicate = User::where('first_name', $request->first_name)
                ->where('last_name', $request->last_name)
                ->where('mothers_name', $request->mothers_name)
                ->exists();
       }

        if ($duplicate) {

            return back()->withErrors([
                'duplicate' => 'User already exists.'
            ]);

        }

        $types = $request->input('type', []);
        $types = array_values(array_filter($types, fn($t) => $t !== 'Regular'));


        $imagePath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/area_distributor'), $filename);
            $imagePath = 'uploads/area_distributor/' . $filename;
        }

        $fullName = trim(collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
        ])->filter()->implode(' '));
        
        $user = new User();
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->name = $fullName;
        $user->email = $request->email_address;
        $user->address = $isAdmin ? null : $request->address;
        $user->warehouse = $isAdmin ? $request->warehouse : null;
        $user->delivery_address = $isAdmin
            ? ($request->has('same_as_address') ? $request->address : $request->delivery_address)
            : null;
        $user->role = $request->role;
        $user->type = json_encode($types);
        $user->birthdate = $isAdmin ? null : $request->birthdate;
        $user->age = $isAdmin ? null : $request->age;
        $user->mothers_name = $isAdmin ? null : $request->mothers_name;
        $user->designation = $isAdmin ? $request->designation : null;
        $user->employee_number = $isAdmin ? $request->employee_number : null;
        $user->department = $isAdmin ? $request->department : null;
        $user->password = bcrypt('12345678');

        if ($imagePath) {
            $user->avatar = $imagePath;
        }

        $user->save();

        if ($request->role === 'Admin') {
            return redirect()
                ->route('users')
                ->with('success', 'Admin successfully created');
        }

        $latestAd = AreaDistributor::orderBy('id', 'desc')->first();

        $number = ($latestAd && $latestAd->ad_reference)
            ? intval(substr($latestAd->ad_reference, 4)) + 1
            : 1;

        $ad_reference = 'PRP' . str_pad($number, 5, '0', STR_PAD_LEFT);


        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/attachments'), $filename);
            $attachmentPath = 'uploads/attachments/' . $filename;
        }

        $areaDistributor = new AreaDistributor();
        $areaDistributor->user_id = $user->id;
        $areaDistributor->ad_reference = $ad_reference;
        $areaDistributor->name = $fullName;
        $areaDistributor->store_code = $request->store_code;
        $areaDistributor->email_address = $request->email_address;
        $areaDistributor->contact_number = $request->contact_number;
        $areaDistributor->facebook = $request->facebook;
        $areaDistributor->address = $request->address;
        $areaDistributor->delivery_address = $needsDeliveryAddress
            ? $request->delivery_address
            : null;

        $areaDistributor->street_address = $request->street_address;
        $areaDistributor->location_region = $request->location_region;
        $areaDistributor->location_province = $request->location_province;
        $areaDistributor->location_city = $request->location_city;
        $areaDistributor->location_barangay = $request->location_barangay;
        $areaDistributor->zipcode = $request->zipcode;

        $areaDistributor->business_name = $request->business_name;
        $areaDistributor->business_type = $request->business_type;
        $areaDistributor->withholding_tax = $request->has('withholding_tax') ? 1 : 0;

        $areaDistributor->latitude = $request->latitude;
        $areaDistributor->longitude = $request->longitude;

        if ($attachmentPath) {
            $areaDistributor->attachment = $attachmentPath;
        }

        if ($imagePath) {
            $areaDistributor->avatar = $imagePath;
        }

        $areaDistributor->status = "Active";
        $areaDistributor->save();


        $joiningDates = $request->input('joining_date', []);
        $riseAreas = $request->input('area_name_rise', []);
        $genesisAreas = $request->input('area_name_genesis', []);

        $maxRows = max(
            count($joiningDates),
            count($riseAreas),
            count($genesisAreas)
        );

        for ($i = 0; $i < $maxRows; $i++) {

            $rise = $riseAreas[$i] ?? null;
            $genesis = $genesisAreas[$i] ?? null;
            $date = $joiningDates[$i] ?? null;

            // Save Rise
            if ($rise) {
                AreaAd::create([
                    'ad_id' => $areaDistributor->id,
                    'ad_user_id' => $user->id,
                    'area_name' => $rise,
                    'project_type' => 'Project Rise',
                    'joining_date' => $date,
                    'user_role' => $request->role,
                ]);
            }

            // Save Genesis
            if ($genesis) {
                AreaAd::create([
                    'ad_id' => $areaDistributor->id,
                    'ad_user_id' => $user->id,
                    'area_name' => $genesis,
                    'project_type' => 'Project Genesis',
                    'joining_date' => $date,
                    'user_role' => $request->role,
                ]);
            }
        }

        return redirect()
            ->route('users')
            ->with('success', 'Successfully encoded');
    }

    // public function store(Request $request)
    // {
    //     $latestAd = AreaDistributor::orderBy('id', 'desc')->first();

    //     $number = ($latestAd && $latestAd->ad_reference)
    //         ? intval(substr($latestAd->ad_reference, 4)) + 1
    //         : 1;

    //     $ad_reference = 'PRAD' . str_pad($number, 5, '0', STR_PAD_LEFT);

    //     $imagePath = null;

    //     if ($request->hasFile('avatar')) {
    //         $file = $request->file('avatar');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path('uploads/area_distributor'), $filename);
    //         $imagePath = 'uploads/area_distributor/' . $filename;
    //     }

    //     // ✅ Attachment fix
    //     $attachmentPath = null;
    //     if ($request->hasFile('attachment')) {
    //         $file = $request->file('attachment');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->move(public_path('uploads/attachments'), $filename);
    //         $attachmentPath = 'uploads/attachments/' . $filename;
    //     }

    //     $fullAddress = $request->address;

    //     $user = new User;
    //     $user->name = $request->name;
    //     $user->email = $request->email_address;
    //     $user->address = $fullAddress;
    //     $user->role = $request->role;
    //     $user->type = $request->type;
    //     $user->birthdate = $request->birthdate;
    //     $user->password = bcrypt('12345678');

    //     if ($imagePath) {
    //         $user->avatar = $imagePath;
    //     }

    //     $user->save();

    //     $areaDistributor = new AreaDistributor;
    //     $areaDistributor->user_id = $user->id;
    //     $areaDistributor->ad_reference = $ad_reference;
    //     $areaDistributor->name = $request->name;
    //     $areaDistributor->store_code = $request->store_code;
    //     $areaDistributor->email_address = $request->email_address;
    //     $areaDistributor->contact_number = $request->contact_number;
    //     $areaDistributor->facebook = $request->facebook;
    //     $areaDistributor->address = $fullAddress;

    //     // ✅ ZIPCODE SAVED PROPERLY
    //     $areaDistributor->location_region = $request->location_region;

    //     $areaDistributor->business_name = $request->business_name;
    //     $areaDistributor->business_type = $request->business_type;
    //     $areaDistributor->joining_date = $request->joining_date;
    //     $areaDistributor->latitude = $request->latitude;
    //     $areaDistributor->longitude = $request->longitude;

    //     if ($attachmentPath) {
    //         $areaDistributor->attachment = $attachmentPath;
    //     }

    //     if ($imagePath) {
    //         $areaDistributor->avatar = $imagePath;
    //     }

    //     $areaDistributor->status = "Active";
    //     $areaDistributor->save();

    //     $areas = $request->input('area_name', []);

    //     foreach ($areas as $area) {
    //         AreaAd::create([
    //             'ad_id' => $areaDistributor->id,
    //             'ad_user_id' => $user->id,
    //             'area_name' => $area,
    //             'user_role' => $request->role,
    //             'joining_date' => $request->joining_date
    //         ]);
    //     }

    //     return redirect()->route('users')->with('success', 'Successfully encoded');
    // }

    public function view(){

        if(auth()->user()->role == "Dealer")
        {
            $profile = Dealer::where('user_id',auth()->user()->id)->first();
             $transactions = TransactionDetail::where('dealer_id',$profile->user_id)->get();
        }
        else
        {
            $profile = Client::where('user_id',auth()->user()->id)->first();
            $transactions = TransactionDetail::where('client_id',$profile->id)->get();
        }
       
       return view('view_profile',
            array(
                'profile' => $profile,  
                'transactions' => $transactions,
            )
        );
    }

    public function generatePartnerCode(Request $request)
    {
        $role = $request->role;
        $year = date('Y');

        // Role Prefix Mapping
        $prefixMap = [
            'Provincial Distributor' => 'PD',
            'Area Distributor' => 'AD',
            'Mega Dealer' => 'MD',
        ];

        if (!isset($prefixMap[$role])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role selected.'
            ]);
        }

        $prefix = $prefixMap[$role];

        // Get latest code for this role
        $latestUser = AreaDistributor::where('store_code', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($latestUser) {
            $lastCode = $latestUser->store_code;

            // Example: PD-2026-0007
            $explode = explode('-', $lastCode);

            if (isset($explode[2])) {
                $nextNumber = intval($explode[2]) + 1;
            }
        }

        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $generatedCode = $prefix . '-' . $year . '-' . $formattedNumber;

        return response()->json([
            'success' => true,
            'code' => $generatedCode
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $exists = User::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('mothers_name', $request->mothers_name)
            ->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function checkMothersName(Request $request)
    {
        $mothersName = trim($request->input('mothers_name', ''));
        $firstName = trim($request->input('first_name', ''));
        $middleName = trim($request->input('middle_name', ''));
        $lastName = trim($request->input('last_name', ''));

        if ($firstName !== '' && $lastName !== '') {
            $exists = User::whereRaw('LOWER(TRIM(first_name)) = ?', [mb_strtolower($firstName)])
                ->whereRaw("LOWER(TRIM(COALESCE(middle_name, ''))) = ?", [mb_strtolower($middleName)])
                ->whereRaw('LOWER(TRIM(last_name)) = ?', [mb_strtolower($lastName)])
                ->exists();

            if ($exists) {
                return response()->json([
                    'exists' => true
                ]);
            }
        }

        if ($mothersName === '') {
            return response()->json([
                'exists' => false
            ]);
        }

        $exists = User::whereRaw(
            'LOWER(TRIM(mothers_name)) = ?',
            [mb_strtolower($mothersName)]
        )->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function getZipCode(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'zipcode' => null,
                'message' => 'Missing coordinates'
            ]);
        }

        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10
            ]);

            $response = $client->get('https://nominatim.openstreetmap.org/reverse', [
                'headers' => [
                    'User-Agent' => 'LaravelApp/1.0 (zipcode lookup)'
                ],
                'query' => [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'addressdetails' => 1
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            $zipcode = null;

            if (!empty($data['address'])) {
                $address = $data['address'];

                $zipcode =
                    $address['postcode']
                    ?? $address['postal_code']
                    ?? $address['zip']
                    ?? null;
            }

            return response()->json([
                'success' => true,
                'zipcode' => $zipcode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'zipcode' => null,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with([
                'dealer:id,user_id,address,status',
                'client:id,user_id,address,status'
            ])->findOrFail($id);

            return response()->json([
                'id' => $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'can_edit' => $user->can_edit,
                'can_add' => $user->can_add,
                'can_delete' => $user->can_delete,

                'can_edit_rewards' => $user->can_edit_rewards,
                'can_add_rewards' => $user->can_add_rewards,
                'can_delete_rewards' => $user->can_delete_rewards,
                'address' => optional($user->dealer)->address
                    ?? optional($user->client)->address
                    ?? $user->address
                    ?? '',
                'status' => optional($user->dealer)->status
                    ?? optional($user->client)->status
                    ?? ''
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id,
            ]);

            $user = User::findOrFail($request->id);

            DB::beginTransaction();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            // OPTIONAL: update related tables
            if ($user->role === 'Dealer' && $user->dealer) {
                $user->dealer->address = $request->address ?? $user->dealer->address;
                $user->dealer->save();
            }

            if ($user->role === 'Client' && $user->client) {
                $user->client->address = $request->address ?? $user->client->address;
                $user->client->save();
            }

            if ($user->role === 'Admin') {
                $user->address = $request->address ?? $user->address;
                $user->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Update failed'
            ], 500);
        }
    }

    public function updateAccess(Request $request)
    {
        $user = User::findOrFail($request->id);

        $user->can_edit = $request->can_edit;
        $user->can_add = $request->can_add;
        $user->can_delete = $request->can_delete;

        $user->can_edit_rewards = $request->can_edit_rewards;
        $user->can_add_rewards = $request->can_add_rewards;
        $user->can_delete_rewards = $request->can_delete_rewards;

        $user->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function datatable(Request $request)
    {
        $query = User::with([
            'dealer:id,user_id,address,status',
            'client:id,user_id,address,status',
            'ad'
        ])->select('users.*');

        if ($request->role) {
            $query->where('role', $request->role);
        }

        return \Yajra\DataTables\Facades\DataTables::of($query)

            ->addColumn('name', function ($user) {
                return trim(strtoupper(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')))
                    ?: strtoupper(($user->name ?? ''));
            })

            ->addColumn('email', function ($user) {
                return strtoupper($user->email ?? '');
            })

            ->addColumn('role', function ($user) {
                return strtoupper($user->role ?? '');
            })

            // ->addColumn('address', function ($user) {
            //     return optional($user->dealer)->address
            //         ?? optional($user->client)->address
            //         ?? $user->address
            //         ?? 'N/A';
            //     return strtoupper($address);
            // })
            ->addColumn('address', function ($user) {

                $address = optional($user->dealer)->address
                    ?? optional($user->client)->address
                    ?? $user->address
                    ?? 'N/A';

                return strtoupper($address);
            })

            ->addColumn('status', function ($user) {
                $status = optional($user->dealer)->status
                    ?? optional($user->client)->status
                    ?? optional($user->ad)->status
                    ?? 'N/A';

                return strtoupper($status);
            })

            ->addColumn('actions', function ($user) {
                return '
                    <button class="btn btn-sm btn-primary btn-edit-user" data-id="'.$user->id.'">Edit</button>
                    <button class="btn btn-sm btn-warning btn-access-user" data-id="'.$user->id.'">Access</button>
                ';
            })

            // 🔥 IMPORTANT: ENABLE SEARCH HERE
            ->filter(function ($query) use ($request) {

                if ($request->has('search') && $request->search['value']) {

                    $search = $request->search['value'];

                    $query->where(function ($q) use ($search) {

                        $q->whereRaw(
                            "CONCAT(users.first_name, ' ', users.last_name) LIKE ?",
                            ["%{$search}%"]
                        )
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.role', 'LIKE', "%{$search}%")
                        ->orWhere('users.address', 'LIKE', "%{$search}%");

                        // 🔥 include relations
                        $q->orWhereHas('dealer', function ($q2) use ($search) {
                            $q2->where('address', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%");
                        });

                        $q->orWhereHas('client', function ($q2) use ($search) {
                            $q2->where('address', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%");
                        });

                    });
                }
            })

            ->rawColumns(['actions'])
            ->make(true);
    }
}
