<?php

namespace App\Mail;

use App\AdPurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdPurchaseOrderWarehouseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(AdPurchaseOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('New Area Distributor Purchase Order: ' . $this->order->po_number)
            ->view('emails.adpo_warehouse_notification');
    }
}
