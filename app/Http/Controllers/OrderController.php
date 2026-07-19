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

                    // Send email invoice
                    try {
                        \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderInvoice($order));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send invoice email: " . $e->getMessage());
                    }
                } else if ($status && in_array($status->transaction_status, ['expire', 'cancel', 'deny'])) {
                    $order->update(['status' => 'cancelled']);
                    $order->payment->update([
                        'status' => 'failed',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_type' => $status->payment_type ?? null,
                    ]);

                    // Auto-reverse stock
                    foreach ($order->items as $item) {
                        if ($item->variant) {
                            $item->variant->increment('stock', $item->quantity);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore if transaction not found in midtrans yet
            }
        }

        return view('pages.order-detail', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::with(['items.variant', 'payment'])
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        try {
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            
            // Check status first to prevent cancelling a just-paid order
            try {
                $status = \Midtrans\Transaction::status($order->order_code);
                if ($status && in_array($status->transaction_status, ['capture', 'settlement'])) {
                    return back()->with('error', 'Pesanan sudah dibayar dan tidak dapat dibatalkan.');
                }
                
                // If it exists and is not paid, cancel it in Midtrans
                \Midtrans\Transaction::cancel($order->order_code);
            } catch (\Exception $e) {
                // If 404 (Transaction doesn't exist because user hasn't selected payment method)
                // or 412 (Cannot be cancelled), we can safely ignore and proceed to cancel locally.
                $errorMsg = $e->getMessage();
                if (!str_contains($errorMsg, '404') && !str_contains($errorMsg, '412')) {
                    throw $e; // Rethrow if it's a different error
                }
            }
            
            $order->update(['status' => 'cancelled']);
            if ($order->payment) {
                $order->payment->update(['status' => 'failed']);
            }

            // Auto-reverse stock
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to cancel order via Midtrans: " . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan pesanan. Silakan coba lagi atau hubungi admin.');
        }
    }
}
