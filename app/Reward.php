<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Reward extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'rewards';

    public $timestamps = true;

    protected $fillable = [
        'price_reward',
        'description',
        'points_required',
        'stock',
        'expiry_date',
        'image',
        'is_active'
    ];
    
    protected $casts = [
        'points_required' => 'integer',
        'stock' => 'integer',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function redeemedHistories()
    {
        return $this->hasMany(RedeemedHistory::class, 'reward_name', 'description');
    }

    public function isExpired()
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    public function isAvailable()
    {
        return $this->is_active && 
               $this->stock > 0 && 
               !$this->isExpired() &&
               is_null($this->deleted_at);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)
                    ->whereNull('deleted_at');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', 1)
                    ->where('stock', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now());
                    })
                    ->whereNull('deleted_at');
    }
}