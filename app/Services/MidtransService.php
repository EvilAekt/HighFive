<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken($order, $user, $items)
    {
        $itemDetails = [];
        
        foreach ($items as $item) {
            $itemDetails[] = [
                'id' => $item['id'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'name' => substr($item['name'], 0, 50),
            ];
        }

        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => route('orders.show', $order->id),
                'error' => route('orders.show', $order->id),
                'unfinish' => route('orders.show', $order->id)
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return null;
        }
    }
}
