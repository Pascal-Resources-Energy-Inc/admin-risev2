<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'user_id', 'name', 'email_address', 'number', 'address', 'status', 'serial_number', 'client_reference', 'location_region', 'location_province', 'location_city', 'location_barangay', 'postal_code', 'street_address', 'spo', 'center', 'latitude', 'longitude'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serial()
    {
        return $this->belongsTo(Stove::class, 'serial_number', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function latestTransaction()
    {
        return $this->hasOne(TransactionDetail::class)->latest('date');
    }

    public function stove()
    {
        return $this->belongsTo(Stove::class, 'serial_number', 'id');
    }

}
