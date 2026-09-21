<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stove extends Model
{
    protected $fillable = ['serial_number', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
