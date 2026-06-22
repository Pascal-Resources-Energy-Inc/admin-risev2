<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert;
use App\User;
use App\Dealer;
use App\Client;
use App\Stove;
use App\TransactionDetail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class EditUserController extends Controller
{
    // public function index(Request $request)
    // {
    //     $stoves = Stove::where('client_id', null)->get();
    //     $users = User::with(['dealer', 'client'])->get();
        
    //     return view('users', 
    //     array(
    //         'stoves' => $stoves,
    //         'users' => $users
    //     ));
    // }

    public function index(Request $request)
    {
        $stoves = Stove::whereNull('client_id')->get(['id', 'serial_number']);

        $users = User::with([
                'dealer:id,user_id,address,status',
                'client:id,user_id,address,status'
            ])
            ->select('id', 'name', 'email', 'role', 'address', 'can_add', 'can_edit')
            ->latest()
            ->paginate(20); // ✅ IMPORTANT

        return view('users', compact('stoves', 'users'));
    }
    
    public function show(Request $request)
    {
        return view('dashboard-dealer');
    }
    
    public function view(Request $request, $id)
    {
        $dealer = Dealer::findOrfail($id);
        $transactions = TransactionDetail::where('dealer_id', $dealer->user_id)->orderBy('id', 'desc')->get();

        return view('dealer',
            array(
                'dealer' => $dealer,
                'transactions' => $transactions,
            )
        );
    }
    
    public function filterByRole(Request $request)
    {
        try {
            $role = $request->get('role', 'all');
            
            $query = User::with(['dealer', 'client']);
            
            if (strtolower($role) !== 'all') {
                $query->where('role', ucfirst(strtolower($role)));
            }
            
            $users = $query->get();
            
            return response()->json([
                'success' => true,
                'users' => $users,
                'html' => view('partials.user-table-rows', compact('users'))->render()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error filtering users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'address' => 'nullable|string|max:500',
                'role' => 'required|in:Admin'
            ]);

            $user = new User();
            $user->name = $request->input('full_name');
            $user->email = $request->input('email');
            $user->password = bcrypt('12345678');
            $user->role = $request->input('role');
            $user->address = $request->input('address');
            $user->email_verified_at = now();
            $user->save();

            Alert::success('Success', 'Admin user created successfully with default password: 12345678');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to create admin user: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'full_name'     => 'required|string|max:255',
                'email'         => 'required|email|max:255',
                'phone'         => 'required|string|max:20',
                'address'       => 'required|string|max:500',
                'serial_number' => 'nullable|string|max:255',
            ]);

            $user = User::findOrFail($id);

            $user->name    = $request->input('full_name');
            $user->email   = $request->input('email');
            $user->save();

            if ($user->role === 'Dealer' && $user->dealer) {
                $user->dealer->number = $request->input('phone');
                $user->dealer->address = $request->input('address');
                $user->dealer->save();
            }
            elseif ($user->role === 'Client' && $user->client) {
                $newStoveId = $request->input('serial_number');
                $currentStoveId = $user->client->serial_number;
                
                $newStoveId = (is_null($newStoveId) || trim($newStoveId) === '') ? null : $newStoveId;

                $user->client->number = $request->input('phone');
                $user->client->address = $request->input('address');
                $user->client->serial_number = $newStoveId;

                if (is_null($newStoveId)) {
                    $user->client->status = 'Inactive';
                    
                    if ($currentStoveId) {
                        Stove::where('id', $currentStoveId)->update([
                            'client_id' => null,
                            'remarks'   => $user->client->id,
                        ]);
                    }
                } else {
                    $user->client->status = 'Active';

                    if ($currentStoveId && $currentStoveId != $newStoveId) {
                        Stove::where('id', $currentStoveId)->update(['client_id' => null]);
                    }

                    Stove::where('id', $newStoveId)->update(['client_id' => $user->client->id]);
                }

                $user->client->save();
            }
            elseif ($user->role === 'Admin') {
                $user->address = $request->input('address');
                $user->save();
            }

            Alert::success('Success', 'User updated successfully!');
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to update user: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updatePrivilege(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->role !== 'Admin') {
                Alert::error('Error', 'Permissions can only be assigned to Admin users.');
                return redirect()->back();
            }

            $user->can_edit = $request->has('can_edit') ? 'on' : null;
            $user->can_add = $request->has('can_add') ? 'on' : null;
            $user->can_delete = $request->has('can_delete') ? 'on' : null;
            $user->can_edit_rewards = $request->has('can_edit_rewards') ? 'on' : null;
            $user->can_add_rewards = $request->has('can_add_rewards') ? 'on' : null;
            $user->can_delete_rewards = $request->has('can_delete_rewards') ? 'on' : null;
            $user->save();

            $permissions = [];
            if ($user->can_edit) $permissions[] = 'Edit';
            if ($user->can_add) $permissions[] = 'Add';
            if ($user->can_delete) $permissions[] = 'Delete';
            if ($user->can_edit_rewards) $permissions[] = 'Edit';
            if ($user->can_add_rewards) $permissions[] = 'Add';
            if ($user->can_delete_rewards) $permissions[] = 'Delete';
            
            $permissionText = empty($permissions) ? 'No permissions' : implode(' and ', $permissions);
            
            Alert::success('Success', "Admin permissions updated to '{$permissionText}' successfully!");
            return redirect()->back();

        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to update admin permissions: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function datatable(Request $request)
    {
        $query = User::with([
            'dealer:id,user_id,address,status',
            'client:id,user_id,address,status'
        ])->select('users.*');

        if ($request->role) {
            $query->where('role', $request->role);
        }

        return DataTables::of($query)

            ->addColumn('name', fn($user) => $user->name ?? '')
            ->addColumn('email', fn($user) => $user->email ?? '')
            ->addColumn('role', fn($user) => $user->role ?? '')

            ->addColumn('address', function ($user) {
                return optional($user->dealer)->address
                    ?? optional($user->client)->address
                    ?? $user->address
                    ?? 'N/A';
            })

            ->addColumn('status', function ($user) {
                return optional($user->dealer)->status
                    ?? optional($user->client)->status
                    ?? 'N/A';
            })

            ->addColumn('actions', function ($user) {

                return '
                    <button class="btn-custom btn-edit-custom btn-edit-user"
                        data-id="'.$user->id.'">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button class="btn-custom btn-access-custom btn-access-user"
                        data-id="'.$user->id.'">
                        <i class="fas fa-key"></i>
                    </button>
                ';
            })

            ->rawColumns(['actions'])
            ->make(true);
    }
}