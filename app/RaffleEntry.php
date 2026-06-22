<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RaffleEntry extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'raffle_id',
        'user_id',
        'participant_name',
        'email',
        'phone',
        'participant_key',
        'ticket_number',
        'status',
        'entered_at',
        'created_by',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
