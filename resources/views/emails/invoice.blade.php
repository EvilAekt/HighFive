<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pesanan HIGHFIVE</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;">
        <h2 style="text-align: center; text-transform: uppercase; letter-spacing: 2px;">HIGHFIVE</h2>
        
        <p>Halo, {{ $order->user->name }}!</p>
        <p>Terima kasih telah berbelanja di HIGHFIVE. Pembayaran Anda untuk pesanan <strong>{{ $order->order_code }}</strong> telah kami terima dan pesanan Anda sedang kami proses.</p>
        
        <p>Sebagai referensi, kami telah melampirkan invoice (nota pembelian) berformat PDF pada email ini.</p>
        
        <p>Anda dapat melihat detail pesanan kapan saja melalui tautan berikut:</p>
        <p>
            <a href="{{ route('orders.show', $order->id) }}" style="display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; font-weight: bold;">
                Lihat Detail Pesanan
            </a>
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777; text-align: center;">
            Email ini dibuat otomatis, mohon tidak membalas email ini.<br>
            HIGHFIVE Apparel &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
