<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .invoice-title {
            font-size: 20px;
            color: #555;
            margin-top: 10px;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section td {
            vertical-align: top;
        }
        .info-title {
            font-weight: bold;
            color: #777;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 5px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            text-align: left;
            padding: 12px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        table.items td {
            border-bottom: 1px solid #dee2e6;
            padding: 12px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals {
            width: 50%;
            float: right;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
        }
        .total-row {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #000;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">HIGHFIVE</div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <table class="info-section">
        <tr>
            <td style="width: 50%;">
                <div class="info-title">Ditagihkan Kepada:</div>
                <strong>{{ $order->user->name }}</strong><br>
                {{ $order->user->phone }}<br>
                {{ $order->user->email }}<br>
                <br>
                <div class="info-title">Alamat Pengiriman:</div>
                {!! nl2br(e($order->shipping_address)) !!}
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="info-title">Detail Pesanan:</div>
                <strong>Nomor Order:</strong> {{ $order->order_code }}<br>
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}<br>
                <strong>Ekspedisi:</strong> {{ $order->shipping_courier }} - {{ $order->shipping_service }}<br>
                <br>
                <div class="info-title">Status Pembayaran:</div>
                <strong style="color: green;">LUNAS</strong> ({{ \Carbon\Carbon::parse($order->payment->paid_at)->format('d F Y H:i') }})
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->variant->product->name }}</strong><br>
                    <span style="font-size: 12px; color: #777;">Varian: {{ $item->variant->color }} - {{ $item->variant->size }}</span>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($order->total_price - $order->shipping_cost + $order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td>Diskon ({{ $order->coupon_code }})</td>
                <td class="text-right" style="color: red;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td>Ongkos Kirim</td>
                <td class="text-right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di HIGHFIVE.<br>
        Invoice ini sah sebagai bukti pembayaran yang valid.
    </div>

</body>
</html>
