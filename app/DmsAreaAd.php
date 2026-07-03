<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmsAreaAd extends Model
{
    use SoftDeletes;

    protected $connection = 'dms';
    protected $table = 'ad_areas';

    public function distributor()
    {
        return $this->belongsTo(DmsAreaDistributor::class, 'ad_id', 'id');
    }
}
