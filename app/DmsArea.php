<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmsArea extends Model
{
    use SoftDeletes;

    protected $connection = 'dms';
    protected $table = 'areas';

    public function areaAd()
    {
        return $this->hasOne(DmsAreaAd::class, 'area_name', 'name');
    }
}
