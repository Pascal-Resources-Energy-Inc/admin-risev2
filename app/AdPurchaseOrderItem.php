<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AdPurchaseOrderItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'ad_purchase_order_id',
        'product_id',
        'sku',
        'product_name',
        'description',
        'product_image',
        'color_breakdown',
        'size_breakdown',
        'qty',
        'partial_received_qty',
        'partial_delivery_date',
        'partial_dr_number',
        'unit_price',
        'line_total',
        'dealer_points',
    ];

    protected $dates = [
        'partial_delivery_date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(AdPurchaseOrder::class, 'ad_purchase_order_id');
    }

    public function partialReceipts()
    {
        return $this->hasMany(AdPurchaseOrderPartialReceipt::class, 'ad_purchase_order_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
