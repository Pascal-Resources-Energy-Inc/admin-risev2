<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DmsAreaDistributor extends Model
{
    use SoftDeletes;

    protected $connection = 'dms';
    protected $table = 'area_distributors';
}
