<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Raffle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'description',
        'prize',
        'starts_at',
        'ends_at',
        'max_entries_per_participant',
        'status',
        'winning_entry_id',
        'drawn_at',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'drawn_at' => 'datetime',
        'max_entries_per_participant' => 'integer',
    ];

    public function entries()
    {
        return $this->hasMany(RaffleEntry::class);
    }

    public function winner()
    {
        return $this->belongsTo(RaffleEntry::class, 'winning_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel()
    {
        if ($this->status === 'drawn') {
            return 'Drawn';
        }

        if ($this->status === 'closed') {
            return 'Closed';
        }

        if ($this->status === 'draft') {
            return 'Draft';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Scheduled';
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'Ended';
        }

        return 'Open';
    }

    public function acceptsEntries()
    {
        if ($this->status !== 'open') {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        return !$this->ends_at || !$this->ends_at->isPast();
    }
}
