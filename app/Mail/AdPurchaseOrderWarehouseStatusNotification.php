<?php

namespace App\Mail;

use App\AdPurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdPurchaseOrderWarehouseStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;

    public function __construct(AdPurchaseOrder $order, $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    public function build()
    {
        return $this->subject('Warehouse Action Needed: ' . $this->order->po_number . ' - ' . $this->order->status)
            ->view('emails.adpo_warehouse_status_notification');
    }
}
