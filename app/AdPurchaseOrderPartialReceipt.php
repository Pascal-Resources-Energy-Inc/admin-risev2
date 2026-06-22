<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdPurchaseOrderPartialReceipt extends Model
{
    protected $fillable = [
        'ad_purchase_order_id',
        'ad_purchase_order_item_id',
        'delivery_date',
        'dr_number',
        'received_qty',
        'confirmed_qty',
        'status',
        'created_by',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $dates = [
        'delivery_date',
        'confirmed_at',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(AdPurchaseOrder::class, 'ad_purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(AdPurchaseOrderItem::class, 'ad_purchase_order_item_id');
    }
}
