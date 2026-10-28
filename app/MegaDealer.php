<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class MegaDealer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'mysql';

    protected $fillable = [
        'user_id', 'partner_code', 'name', 'first_name', 'middle_name', 'last_name',
        'mothers_name', 'birthdate', 'age', 'email_address', 'number', 'facebook',
        'store_name', 'store_type', 'mfi', 'center', 'spo', 'area', 'address',
        'street_address', 'location_region', 'location_province', 'location_city',
        'location_barangay', 'postal_code', 'latitude', 'longitude', 'status',
        'avatar', 'contract', 'valid_file', 'valid_id', 'valid_id_number', 'signature',
    ];

    protected $dates = ['birthdate', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(OrderDetail::class, 'dealer_id', 'user_id');
    }

    public function sales()
    {
        return $this->hasMany(TransactionDetail::class, 'dealer_id', 'user_id');
    }
}
