<?php

namespace App\Http\Controllers;

use App\Client;
use App\Stove;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SerialNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->admin();

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;
        $status = $request->input('status', 'all');
        $search = trim((string) $request->input('search'));

        $query = Stove::with('client');
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('serial_number', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('client_reference', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($status === 'available') {
            $query->whereNull('client_id');
        } elseif ($status === 'assigned') {
            $query->whereNotNull('client_id');
        } elseif ($status === 'signed') {
            $query->whereHas('client', function ($clientQuery) {
                $clientQuery->whereNotNull('signature')->where('signature', '!=', '');
            });
        }

        $serials = $query->orderBy('serial_number')->paginate($perPage)->appends($request->except('page'));
        $clients = Client::orderBy('name')->get(['id', 'name', 'client_reference', 'signature', 'serial_number']);
        $serialStats = [
            'total' => Stove::count(),
            'assigned' => Stove::whereNotNull('client_id')->count(),
            'available' => Stove::whereNull('client_id')->count(),
            'signed' => Stove::whereHas('client', function ($clientQuery) {
                $clientQuery->whereNotNull('signature')->where('signature', '!=', '');
            })->count(),
        ];

        return view('serial_numbers.index', compact('serials', 'clients', 'serialStats', 'search', 'status', 'perPage'));
    }

    public function store(Request $request)
    {
        $this->admin();
        $data = $this->validateSerial($request);

        DB::transaction(function () use ($data) {
            $serial = Stove::create(['serial_number' => $data['serial_number']]);
            $this->assignClientWithinTransaction($serial, $data['client_id'] ?? null);
        });

        return redirect()->route('serial-numbers.index')->with('success', 'Serial number added successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->admin();
        $serial = Stove::findOrFail($id);
        $data = $this->validateSerial($request, $serial->id);

        DB::transaction(function () use ($serial, $data) {
            $lockedSerial = Stove::whereKey($serial->id)->lockForUpdate()->firstOrFail();
            $lockedSerial->serial_number = $data['serial_number'];
            $lockedSerial->save();
            $this->assignClientWithinTransaction($lockedSerial, $data['client_id'] ?? null);
        });

        return redirect()->route('serial-numbers.index')->with('success', 'Serial number updated successfully.');
    }

    private function validateSerial(Request $request, $ignoreId = null)
    {
        $unique = 'unique:stoves,serial_number';
        if ($ignoreId) {
            $unique .= ',' . $ignoreId;
        }

        return $request->validate([
            'serial_number' => ['required', 'string', 'max:255', $unique],
            'client_id' => 'nullable|integer|exists:clients,id',
        ]);
    }

    private function assignClientWithinTransaction(Stove $serial, $clientId)
    {
        $previousClientId = $serial->client_id;
        if ($previousClientId && (int) $previousClientId !== (int) $clientId) {
            $previousClient = Client::whereKey($previousClientId)->lockForUpdate()->first();
            if ($previousClient && (int) $previousClient->serial_number === (int) $serial->id) {
                $previousClient->serial_number = null;
                $previousClient->save();
            }
        }

        if (!$clientId) {
            $serial->client_id = null;
            $serial->save();
            return;
        }

        $client = Client::whereKey($clientId)->lockForUpdate()->firstOrFail();
        if ($client->serial_number && (int) $client->serial_number !== (int) $serial->id) {
            Stove::whereKey($client->serial_number)
                ->where('client_id', $client->id)
                ->update(['client_id' => null]);
        }

        $serial->client_id = $client->id;
        $serial->save();
        $client->serial_number = $serial->id;
        $client->save();
    }

    private function admin()
    {
        abort_unless(auth()->check() && strcasecmp(trim((string) auth()->user()->role), 'Admin') === 0, 403);
    }
}
