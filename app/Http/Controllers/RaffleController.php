<?php

namespace App\Http\Controllers;

use App\Raffle;
use App\RaffleEntry;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RaffleController extends Controller
{
    public function index(Request $request)
    {
        $raffles = Raffle::with(['winner', 'entries'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('prize', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $raffleIds = $raffles->pluck('id');
        $entries = RaffleEntry::with(['raffle', 'user'])
            ->whereIn('raffle_id', $raffleIds)
            ->orderBy('entered_at', 'desc')
            ->get();

        $users = User::with(['dealer', 'client'])->orderBy('name')->get();

        return view('raffles.index', compact('raffles', 'entries', 'users'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedRaffle($request);
        $data['created_by'] = auth()->id();

        Raffle::create($data);

        return redirect()->route('raffles')->with('success', 'Raffle created successfully.');
    }

    public function update(Request $request, Raffle $raffle)
    {
        if ($raffle->status === 'drawn') {
            return redirect()->route('raffles')->with('error', 'A completed raffle can no longer be edited.');
        }

        $raffle->update($this->validatedRaffle($request));

        return redirect()->route('raffles')->with('success', 'Raffle updated successfully.');
    }

    public function destroy(Raffle $raffle)
    {
        if ($raffle->status === 'drawn') {
            return redirect()->route('raffles')->with('error', 'A completed raffle cannot be deleted.');
        }

        $raffle->delete();

        return redirect()->route('raffles')->with('success', 'Raffle and its entries were deleted.');
    }

    public function storeEntry(Request $request, Raffle $raffle)
    {
        $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'participant_name' => 'required_without:user_id|nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:40',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        if (!$raffle->acceptsEntries()) {
            throw ValidationException::withMessages([
                'raffle' => 'This raffle is not currently accepting entries.',
            ]);
        }

        $participant = $this->participantData($request);

        DB::transaction(function () use ($raffle, $participant, $request) {
            $lockedRaffle = Raffle::where('id', $raffle->id)->lockForUpdate()->firstOrFail();

            if (!$lockedRaffle->acceptsEntries()) {
                throw ValidationException::withMessages([
                    'raffle' => 'This raffle is not currently accepting entries.',
                ]);
            }

            $existingCount = RaffleEntry::where('raffle_id', $lockedRaffle->id)
                ->where('participant_key', $participant['participant_key'])
                ->lockForUpdate()
                ->count();
            $quantity = (int) $request->quantity;
            $limit = (int) $lockedRaffle->max_entries_per_participant;

            if ($existingCount + $quantity > $limit) {
                throw ValidationException::withMessages([
                    'quantity' => 'This participant can only have ' . $limit . ' ticket(s). They already have ' . $existingCount . '.',
                ]);
            }

            for ($index = 0; $index < $quantity; $index++) {
                RaffleEntry::create(array_merge($participant, [
                    'raffle_id' => $lockedRaffle->id,
                    'ticket_number' => $this->generateTicketNumber($lockedRaffle->id),
                    'status' => 'eligible',
                    'entered_at' => now(),
                    'created_by' => auth()->id(),
                ]));
            }
        });

        return redirect()->route('raffles')->with('success', $request->quantity . ' raffle ticket(s) added successfully.');
    }

    public function destroyEntry(Raffle $raffle, RaffleEntry $entry)
    {
        if ((int) $entry->raffle_id !== (int) $raffle->id) {
            abort(404);
        }

        if ($raffle->status === 'drawn') {
            return redirect()->route('raffles')->with('error', 'Entries cannot be removed after a winner is drawn.');
        }

        $entry->delete();

        return redirect()->route('raffles')->with('success', 'Raffle entry removed.');
    }

    public function draw(Raffle $raffle)
    {
        DB::transaction(function () use ($raffle) {
            $lockedRaffle = Raffle::where('id', $raffle->id)->lockForUpdate()->firstOrFail();

            if ($lockedRaffle->status === 'drawn') {
                throw ValidationException::withMessages([
                    'raffle' => 'A winner has already been drawn for this raffle.',
                ]);
            }

            $eligibleEntries = RaffleEntry::where('raffle_id', $lockedRaffle->id)
                ->where('status', 'eligible')
                ->lockForUpdate()
                ->get();

            if ($eligibleEntries->isEmpty()) {
                throw ValidationException::withMessages([
                    'raffle' => 'Add at least one eligible entry before drawing a winner.',
                ]);
            }

            $winner = $eligibleEntries->get(random_int(0, $eligibleEntries->count() - 1));
            $winner->status = 'winner';
            $winner->save();

            $lockedRaffle->status = 'drawn';
            $lockedRaffle->winning_entry_id = $winner->id;
            $lockedRaffle->drawn_at = now();
            $lockedRaffle->save();
        });

        return redirect()->route('raffles')->with('success', 'Winner drawn successfully.');
    }

    private function validatedRaffle(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'prize' => 'required|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_entries_per_participant' => 'required|integer|min:1|max:1000',
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ]);

        $data['starts_at'] = $this->normalizeDateTime($data['starts_at'] ?? null);
        $data['ends_at'] = $this->normalizeDateTime($data['ends_at'] ?? null);

        return $data;
    }

    private function normalizeDateTime($value)
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    private function participantData(Request $request)
    {
        if ($request->filled('user_id')) {
            $user = User::with(['dealer', 'client'])->findOrFail($request->user_id);
            $profile = $user->dealer ?: $user->client;

            return [
                'user_id' => $user->id,
                'participant_name' => $user->name,
                'email' => $user->email,
                'phone' => $profile ? $profile->number : null,
                'participant_key' => 'user:' . $user->id,
            ];
        }

        $email = strtolower(trim((string) $request->email));
        $phone = preg_replace('/\D+/', '', (string) $request->phone);
        $name = trim((string) $request->participant_name);

        if ($email !== '') {
            $key = 'email:' . $email;
        } elseif ($phone !== '') {
            $key = 'phone:' . $phone;
        } else {
            $key = 'name:' . strtolower($name);
        }

        return [
            'user_id' => null,
            'participant_name' => $name,
            'email' => $request->email,
            'phone' => $request->phone,
            'participant_key' => $key,
        ];
    }

    private function generateTicketNumber($raffleId)
    {
        do {
            $ticket = 'RFL-' . str_pad($raffleId, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
        } while (RaffleEntry::where('ticket_number', $ticket)->exists());

        return $ticket;
    }
}
