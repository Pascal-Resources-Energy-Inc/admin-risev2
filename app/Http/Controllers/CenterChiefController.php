<?php

namespace App\Http\Controllers;

use App\Center;
use App\CenterChief;
use App\DmsArea;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CenterChiefController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->admin();

        $chiefs = CenterChief::with(['centers', 'user'])->orderBy('name')->get();
        $centerAssignments = [];
        foreach ($chiefs as $chief) {
            foreach ($chief->centers as $center) {
                $centerAssignments[$center->id] = $chief->name;
            }
        }

        return view('center_chiefs.index', [
            'chiefs' => $chiefs,
            'centers' => Center::orderBy('name')->get(),
            'centerAssignments' => $centerAssignments,
            'mfis' => $this->mfis(),
            'areas' => DmsArea::whereNotNull('name')->orderBy('name')->get(),
            'activeCount' => $chiefs->where('status', 'Active')->count(),
            'inactiveCount' => $chiefs->where('status', 'Inactive')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $this->admin();
        DB::transaction(function () use ($request) {
            $data = $this->validatedData($request);
            $centerIds = $data['center_ids'];
            $password = $data['password'] ?? '12345678';
            unset($data['center_ids'], $data['password'], $data['password_confirmation']);
            $user = User::create([
                'name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($password), 'role' => 'Center Chief',
            ]);
            $chief = CenterChief::create(array_merge($data, ['user_id' => $user->id]));
            $chief->centers()->sync($centerIds);
        });

        return back()->with('success', 'Center Chief added successfully.');
    }

    public function update(Request $request, CenterChief $centerChief)
    {
        $this->admin();
        DB::transaction(function () use ($request, $centerChief) {
            $data = $this->validatedData($request, $centerChief);
            $centerIds = $data['center_ids'];
            $password = $data['password'] ?? null;
            unset($data['center_ids'], $data['password'], $data['password_confirmation']);
            $user = $centerChief->user;
            if (! $user) {
                $user = User::create([
                    'name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($password ?: '12345678'), 'role' => 'Center Chief',
                ]);
                $data['user_id'] = $user->id;
            } else {
                $userData = ['name' => $data['name'], 'email' => $data['email'], 'role' => 'Center Chief'];
                if ($password) { $userData['password'] = Hash::make($password); }
                $user->update($userData);
            }
            $centerChief->update($data);
            $centerChief->centers()->sync($centerIds);
        });

        return back()->with('success', 'Center Chief updated successfully.');
    }

    public function destroy(CenterChief $centerChief)
    {
        $this->admin();
        $centerChief->delete();

        return back()->with('success', 'Center Chief removed successfully.');
    }

    private function validatedData(Request $request, CenterChief $centerChief = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(optional(optional($centerChief)->user)->id)],
            'number' => 'nullable|string|max:50',
            'street_address' => 'required|string|max:255',
            'location_barangay' => 'required|string|max:255',
            'location_city' => 'required|string|max:255',
            'location_province' => 'required|string|max:255',
            'location_region' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'mfi' => 'required|string|max:100|exists:dms.mfis,name',
            'center_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'center_ids.*' => 'integer|distinct|exists:centers,id',
            'status' => 'required|in:Active,Inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $centers = Center::whereIn('id', $data['center_ids'])->get();
        if ($centers->count() !== count($data['center_ids']) || $centers->contains(function ($center) use ($data) {
            return trim((string) $center->mfi) !== '' && strcasecmp(trim((string) $center->mfi), trim($data['mfi'])) !== 0;
        })) {
            throw ValidationException::withMessages([
                'center_ids' => 'Every selected center must be assigned to the selected MFI.',
            ]);
        }

        $assignedElsewhere = DB::table('center_chief_center')
            ->whereIn('center_id', $data['center_ids'])
            ->when($centerChief, function ($query) use ($centerChief) {
                return $query->where('center_chief_id', '!=', $centerChief->id);
            })->exists();
        if ($assignedElsewhere) {
            throw ValidationException::withMessages([
                'center_ids' => 'One or more selected centers are already assigned to another Center Chief.',
            ]);
        }

        return $data;
    }

    private function mfis()
    {
        return DB::connection('dms')->table('mfis')->orderBy('name')->pluck('name');
    }

    private function admin()
    {
        abort_unless(auth()->user() && auth()->user()->role === 'Admin', 403);
    }
}
