<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Center extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'mfi', 'created_by'];

    public function centerChiefs()
    {
        return $this->belongsToMany(CenterChief::class, 'center_chief_center')->withTimestamps();
    }
}
