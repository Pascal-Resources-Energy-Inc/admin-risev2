<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CenterChief extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'number', 'mfi', 'status',
          'street_address', 'location_barangay', 'location_city', 'location_province', 'location_region', 'area',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class, 'center_chief_center')->withTimestamps();
    }
}
