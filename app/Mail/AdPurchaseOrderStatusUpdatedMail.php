<?php

namespace App\Mail;

use App\AdPurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdPurchaseOrderStatusUpdatedMail extends Mailable
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
        return $this->subject('ADPO Status Updated: ' . $this->order->po_number)
            ->view('emails.adpo_status_updated');
    }
}
