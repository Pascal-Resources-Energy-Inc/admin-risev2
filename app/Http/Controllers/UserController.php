<?php

namespace App\Http\Controllers;
use App\User;
use App\Dealer;
use App\Client;
use App\Stove;
use App\Area;
use App\TransactionDetail;
use App\AreaDistributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'Admin')->count(),
            'dealers' => User::whereIn('role', ['Dealer', 'Mega Dealer'])->count(),
            'customers' => User::where('role', 'Client')->count(),
        ];

        return view('users.index', compact('stoves', 'users', 'areas', 'userStats'));
    }

    public function store(Request $request)
    {
        $actor = auth()->user();
        abort_unless($actor && $actor->role === 'Admin' && $actor->can_add === 'on', 403);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255|unique:users,email',
            'designation' => 'required|string|max:255',
            'employee_number' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'warehouse' => 'nullable|in:lubao,guinobatan',
        ]);

        $fullName = trim(collect([
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
        ])->filter()->implode(' '));

        $user = new User();
        $user->name = $fullName;
        $user->email = $data['email_address'];
        $user->role = 'Admin';
        $user->password = bcrypt('12345678');
        $user->designation = $data['designation'];
        $user->employee_number = $data['employee_number'];
        $user->department = $data['department'];
        $user->status = $data['status'];

        $optionalValues = [
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'warehouse' => $data['warehouse'] ?? null,
        ];
        foreach ($optionalValues as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $value;
            }
        }

        if (Schema::hasColumn('users', 'type')) {
            $user->type = json_encode([]);
        }

        $user->save();

        return redirect()
            ->route('users')
            ->with('success', 'Admin successfully created. Initial password: 12345678.');
    }
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

        if ($role === 'Mega Dealer') {
            $lastCode = AreaDistributor::where('store_code', 'like', 'PRMD%')
                ->orderByRaw('CAST(SUBSTRING(store_code, 5) AS UNSIGNED) DESC')
                ->value('store_code');
            $lastNumber = 0;

            if ($lastCode && preg_match('/^PRMD(\d{5,})$/', $lastCode, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            return response()->json([
                'success' => true,
                'code' => 'PRMD' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT),
            ]);
        }

        $year = date('Y');

        // Role Prefix Mapping
        $prefixMap = [
            'Provincial Distributor' => 'PD',
            'Area Distributor' => 'AD',
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
            $nameParts = preg_split('/\s+/', trim((string) $user->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $fallbackFirstName = count($nameParts) ? array_shift($nameParts) : '';
            $fallbackLastName = count($nameParts) ? array_pop($nameParts) : '';

            return response()->json([
                'id' => $user->id,
                'name' => $user->name ?? '',
                'role' => $user->role ?? '',
                'first_name' => $user->getAttribute('first_name') ?: $fallbackFirstName,
                'middle_name' => $user->getAttribute('middle_name') ?: implode(' ', $nameParts),
                'last_name' => $user->getAttribute('last_name') ?: $fallbackLastName,
                'email' => $user->email ?? '',
                'designation' => $user->getAttribute('designation') ?? '',
                'employee_number' => $user->getAttribute('employee_number') ?? '',
                'department' => $user->getAttribute('department') ?? '',
                'can_edit' => $user->can_edit,
                'can_add' => $user->can_add,
                'can_delete' => $user->can_delete,

                'can_edit_rewards' => $user->can_edit_rewards,
                'can_add_rewards' => $user->can_add_rewards,
                'can_delete_rewards' => $user->can_delete_rewards,
                'module_access' => $user->module_access,
                'address' => optional($user->dealer)->address
                    ?? optional($user->client)->address
                    ?? $user->address
                    ?? '',
                'status' => $user->role === 'Admin'
                    ? ($user->getAttribute('status') ?: 'Active')
                    : (optional($user->dealer)->status
                        ?? optional($user->client)->status
                        ?? '')
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
            $request->validate(['id' => 'required|exists:users,id']);
            $user = User::findOrFail($request->id);
            $rules = [
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ];

            if ($user->role === 'Admin') {
                $rules += [
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'designation' => 'required|string|max:255',
                    'employee_number' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'status' => 'required|in:Active,Inactive',
                ];
            } else {
                $rules['name'] = 'required|string|max:255';
            }
            $data = $request->validate($rules);

            DB::beginTransaction();

            if ($user->role === 'Admin') {
                $user->name = trim(collect([
                    $data['first_name'],
                    $data['middle_name'] ?? null,
                    $data['last_name'],
                ])->filter()->implode(' '));
                $user->designation = $data['designation'];
                $user->employee_number = $data['employee_number'];
                $user->department = $data['department'];
                $user->status = $data['status'];

                foreach (['first_name', 'middle_name', 'last_name'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $user->{$column} = $data[$column] ?? null;
                    }
                }
            } else {
                $user->name = $data['name'];
            }
            $user->email = $data['email'];
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            \Log::error('Update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Update failed'
            ], 500);
        }
    }

    public function updateAccess(Request $request)
    {
        $actor = auth()->user();
        abort_unless($actor && $actor->role === 'Admin' && ($actor->can_edit === 'on' || $actor->can_add === 'on'), 403);
        $request->validate([
            'id' => 'required|exists:users,id',
            'module_access' => 'nullable|array',
        ]);
        $user = User::findOrFail($request->id);
        abort_unless($user->role === 'Admin', 403, 'Module access can only be assigned to admin users.');

        $user->can_edit = $request->can_edit;
        $user->can_add = $request->can_add;
        $user->can_delete = $request->can_delete;

        $user->can_edit_rewards = $request->can_edit_rewards;
        $user->can_add_rewards = $request->can_add_rewards;
        $user->can_delete_rewards = $request->can_delete_rewards;

        $selectedModules = ['dashboard' => true];
        foreach (array_keys(config('admin_modules', [])) as $module) {
            if ($module !== 'dashboard' && $request->input('module_access.' . $module)) {
                $selectedModules[$module] = true;
            }
        }
        $user->module_access = $selectedModules;

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
                $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? '');
                $initial = mb_strtoupper(mb_substr($name, 0, 1));
                return '<div class="user-name-wrap"><span class="user-avatar-initial">'.e($initial).'</span><strong class="user-name-cell">'.e($name).'</strong></div>';
            })

            ->addColumn('email', function ($user) {
                return '<span class="user-email-cell">'.e($user->email ?? '').'</span>';
            })

            ->addColumn('role', function ($user) {
                $role = $user->role ?? 'Unknown';
                $normalizedRole = strtolower($role);
                $roleClass = $normalizedRole === 'admin' ? 'admin' : (in_array($normalizedRole, ['dealer', 'mega dealer'], true) ? 'dealer' : ($normalizedRole === 'client' ? 'client' : 'other'));
                return '<span class="user-role-pill '.$roleClass.'">'.e($role).'</span>';
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

                return '<span class="user-address-cell" title="'.e($address).'">'.e($address).'</span>';
            })

            ->addColumn('status', function ($user) {
                $status = $user->role === 'Admin' ? $user->status : (optional($user->dealer)->status
                    ?? optional($user->client)->status
                    ?? optional($user->ad)->status
                    ?? 'N/A');

                $normalizedStatus = strtolower(trim((string) $status));
                $statusClass = $normalizedStatus === 'active' ? 'active' : ($normalizedStatus === 'inactive' ? 'inactive' : 'other');
                return '<span class="user-status-pill '.$statusClass.'">'.e($status ?: 'N/A').'</span>';
            })

            ->addColumn('actions', function ($user) {
                $actions = '<div class="users-actions"><button type="button" class="btn btn-edit-user" data-id="'.(int) $user->id.'"><i class="bi bi-pencil-square"></i>Edit</button>';

                $actor = auth()->user();
                if ($user->role === 'Admin' && $actor && $actor->role === 'Admin'
                    && ($actor->can_edit === 'on' || $actor->can_add === 'on')) {
                    $actions .= '<button type="button" class="btn btn-access-user" data-id="'.(int) $user->id.'"><i class="bi bi-shield-lock"></i>Access</button>';
                }

                return $actions.'</div>';
            })

            // 🔥 IMPORTANT: ENABLE SEARCH HERE
            ->filter(function ($query) use ($request) {

                if ($request->has('search') && $request->search['value']) {

                    $search = $request->search['value'];

                    $query->where(function ($q) use ($search) {

                        // This installation stores the display name in
                        // users.name; first_name and last_name are not
                        // available in every database schema.
                        $q->where('users.name', 'LIKE', "%{$search}%")
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

            ->rawColumns(['name', 'email', 'address', 'status', 'role', 'actions'])
            ->make(true);
    }
}
