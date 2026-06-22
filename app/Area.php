<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Area extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'areas';

    public function areaAd()
    {
        return $this->hasOne(AreaAd::class, 'area_name', 'name');
    }
}
