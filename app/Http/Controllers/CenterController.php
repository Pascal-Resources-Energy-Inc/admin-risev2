<?php

namespace App\Http\Controllers;

use App\Center;
use App\CenterChief;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->admin();

        return view('centers.index', [
            'mfis' => DB::connection('dms')->table('mfis')->orderBy('name')->pluck('name'),
            'centerCount' => Center::count(),
            'mfiCount' => Center::whereNotNull('mfi')->distinct()->count('mfi'),
            'assignedChiefCount' => Center::has('centerChiefs')->count(),
        ]);
    }

    /**
     * Return a paginated center directory for DataTables without loading the
     * entire directory or its edit forms into the initial page response.
     */
    public function datatable(Request $request)
    {
        $this->admin();

        $chiefAssignments = DB::table('center_chief_center as assignment')
            ->join('center_chiefs as chief', 'chief.id', '=', 'assignment.center_chief_id')
            ->select(
                'assignment.center_id',
                DB::raw("GROUP_CONCAT(chief.name ORDER BY chief.name SEPARATOR ', ') as center_chief_names")
            )
            ->groupBy('assignment.center_id');

        $query = Center::query()
            ->leftJoinSub($chiefAssignments, 'chief_assignments', function ($join) {
                $join->on('chief_assignments.center_id', '=', 'centers.id');
            })
            ->select('centers.*', 'chief_assignments.center_chief_names')
            ->withCount('centerChiefs');

        return \Yajra\DataTables\Facades\DataTables::eloquent($query)
            ->addColumn('center', function (Center $center) {
                return '<div class="center-name"><span><i class="bi bi-geo-alt"></i></span><strong>'
                    . e($center->name) . '</strong></div>';
            })
            ->addColumn('mfi_badge', function (Center $center) {
                return '<span class="center-mfi"><i class="bi bi-bank"></i>' . e($center->mfi) . '</span>';
            })
            ->addColumn('center_chief', function (Center $center) {
                if (! $center->center_chiefs_count) {
                    return '<span class="center-chief unassigned"><i class="bi bi-person-dash"></i>Not assigned</span>';
                }

                $names = $center->center_chief_names;

                return '<span class="center-chief assigned"><i class="bi bi-person-check"></i>'
                    . e($names ?: 'Assigned') . '</span>';
            })
            ->addColumn('added', function (Center $center) {
                return '<span class="center-date">' . ($center->created_at ? e($center->created_at->format('M d, Y')) : '&mdash;') . '</span>';
            })
            ->addColumn('actions', function (Center $center) {
                $deleteUrl = route('centers.destroy', $center);
                $disabled = $center->center_chiefs_count ? ' disabled' : '';

                return '<div class="center-actions">'
                    . '<button type="button" class="btn btn-sm btn-outline-primary center-edit" title="Edit ' . e($center->name) . '" data-center-id="' . $center->id . '"><i class="bi bi-pencil"></i></button>'
                    . '<form method="POST" action="' . e($deleteUrl) . '" onsubmit="return confirm(\'Remove this center? This cannot be undone.\');">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Remove ' . e($center->name) . '"' . $disabled . '><i class="bi bi-trash"></i></button>'
                    . '</form></div>';
            })
            ->rawColumns(['center', 'mfi_badge', 'center_chief', 'added', 'actions'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $this->admin();
        $data = $this->validatedData($request);
        $data['created_by'] = auth()->id();
        Center::create($data);

        return back()->with('success', 'Center added successfully.');
    }

    public function update(Request $request, Center $center)
    {
        $this->admin();
        $oldName = $center->name;
        $data = $this->validatedData($request, $center);
        $center->update($data);

        if ($oldName !== $center->name) {
            DB::table('dealers')->where('center', $oldName)->update(['center' => $center->name]);
            DB::table('clients')->where('center', $oldName)->update(['center' => $center->name]);
        }

        $center->centerChiefs()->update(['mfi' => $center->mfi]);

        return back()->with('success', 'Center updated successfully.');
    }

    public function destroy(Center $center)
    {
        $this->admin();
        if ($center->centerChiefs()->exists()) {
            return back()->withErrors(['center' => 'Remove or reassign the Center Chief before removing this center.']);
        }

        $center->delete();

        return back()->with('success', 'Center removed successfully.');
    }

    private function validatedData(Request $request, Center $center = null)
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('centers', 'name')->ignore(optional($center)->id),
            ],
            'mfi' => 'required|string|max:100|exists:dms.mfis,name',
        ]);
    }

    private function admin()
    {
        abort_unless(auth()->user() && auth()->user()->role === 'Admin', 403);
    }
}
