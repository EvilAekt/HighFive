<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Dikirim - HIGHFIVE</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;">
        <h2 style="text-align: center; text-transform: uppercase; letter-spacing: 2px;">HIGHFIVE</h2>
        
        <p>Halo, {{ $order->user->name }}!</p>
        <p>Hore! Pesanan Anda <strong>{{ $order->order_code }}</strong> telah kami serahkan ke pihak ekspedisi dan saat ini sedang dalam perjalanan menuju ke alamat Anda.</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #000;">
            <p style="margin-top: 0; margin-bottom: 5px;"><strong>Ekspedisi:</strong> {{ $order->shipping_courier }} - {{ $order->shipping_service }}</p>
            <p style="margin: 0; font-size: 18px;"><strong>Nomor Resi: <span style="font-family: monospace;">{{ $order->resi_number }}</span></strong></p>
        </div>
        
        <p>Anda dapat melacak posisi paket Anda menggunakan nomor resi di atas melalui website ekspedisi, atau melihat detail pesanan Anda melalui tautan berikut:</p>
        <p>
            <a href="{{ route('orders.show', $order->id) }}" style="display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; font-weight: bold;">
                Lacak Pesanan Saya
            </a>
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777; text-align: center;">
            Terima kasih telah berbelanja di HIGHFIVE!<br>
            Email ini dibuat otomatis, mohon tidak membalas email ini.<br>
            HIGHFIVE Apparel &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
