<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Updated</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:20px;">

    <div style="max-width:600px; margin:auto; background:#ffffff; padding:30px; border-radius:10px;">

        <h2 style="color:#2c3e50;">
            Order Status Updated
        </h2>

        <p>Hello {{ optional($order->dealer)->name ?? 'Dealer' }},</p>

        <p>
            Your order has been updated successfully.
        </p>

        <table style="width:100%; border-collapse: collapse;">
            <tr>
                <td><strong>Order ID:</strong></td>
                <td>#{{ $order->id }}</td>
            </tr>

            <tr>
                <td><strong>Quantity:</strong></td>
                <td>{{ $order->qty }}</td>
            </tr>

            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ ucfirst($order->payment_method) }}</td>
            </tr>

            <tr>
                <td><strong>Delivery Type:</strong></td>
                <td>{{ ucfirst($order->delivery_type) }}</td>
            </tr>

            @if($order->delivery_type === 'delivery')
            <tr>
                <td><strong>Delivery Fee:</strong></td>
                <td>{{ number_format($order->delivery_fee ?? 0, 2) }}</td>
            </tr>
            @endif

            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <strong style="color:green;">
                        {{ ucfirst($order->status) }}
                    </strong>
                </td>
            </tr>
        </table>

        <br>

        <p>
            Thank you for using our system.
        </p>

        <p>
            Regards,<br>
            CRM System
        </p>

    </div>

</body>
</html>
