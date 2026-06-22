<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ADPO Status Updated</title>
</head>
<body style="margin:0; padding:24px; background:#f8fafc; font-family:Arial, sans-serif; color:#111827;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
        <div style="padding:22px 24px; border-bottom:1px solid #edf0f5; background:#fcfcfd;">
            <div style="font-size:12px; font-weight:bold; color:#dc2626; text-transform:uppercase;">Purchase Order Update</div>
            <h2 style="margin:8px 0 0; font-size:22px; color:#111827;">Your ADPO Status Has Changed</h2>
        </div>

        <div style="padding:24px;">
            <p style="margin-top:0;">Hello {{ $order->business_name ?: optional($order->ad)->name ?: 'Area Distributor' }},</p>
            <p>Your purchase order status has been updated.</p>

            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <tr>
                    <td style="padding:8px 0; color:#667085; width:170px;">PO Number</td>
                    <td style="padding:8px 0; font-weight:bold;">{{ $order->po_number }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#667085;">Previous Status</td>
                    <td style="padding:8px 0;">{{ $oldStatus ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#667085;">New Status</td>
                    <td style="padding:8px 0; font-weight:bold; color:#dc2626;">{{ $order->status }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#667085;">Shipping</td>
                    <td style="padding:8px 0;">{{ strtoupper(str_replace('_', ' ', $order->shipping_type ?: 'N/A')) }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#667085;">Delivery Address</td>
                    <td style="padding:8px 0;">{{ $order->delivery_address ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#667085;">Total</td>
                    <td style="padding:8px 0; font-weight:bold;">PHP {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>

            <p style="margin:22px 0 0; color:#667085; font-size:13px;">Thank you.</p>
        </div>
    </div>
</body>
</html>
