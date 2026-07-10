<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.variant.product', 'payment'])
            ->where('user_id', auth()->id())
            ->latest();

        $filter = $request->query('filter', 'all');
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $orders = $query->get();

        return view('pages.orders', compact('orders', 'filter'));
    }

    public function show($id)
    {
        $order = Order::with(['items.variant.product.images', 'payment'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Fallback for Localhost: Check status from Midtrans if still pending
        if ($order->status === 'pending' && $order->payment && $order->payment->status === 'pending') {
            try {
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                $status = \Midtrans\Transaction::status($order->order_code);
                
                if ($status && ($status->transaction_status == 'capture' || $status->transaction_status == 'settlement')) {
                    $order->update(['status' => 'processing']);
                    $order->payment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_type' => $status->payment_type ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore if transaction not found in midtrans yet
            }
        }

        return view('pages.order-detail', compact('order'));
    }
}
