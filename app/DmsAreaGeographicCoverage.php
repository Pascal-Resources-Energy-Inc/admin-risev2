<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DmsAreaGeographicCoverage extends Model
{
    protected $connection = 'dms';
    protected $table = 'area_geographic_coverages';

    public function area()
    {
        return $this->belongsTo(DmsArea::class, 'area_id');
    }
}
