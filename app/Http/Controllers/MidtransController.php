<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $order = Order::where('order_code', $request->order_id)->first();
                if ($order) {
                    $order->update(['status' => 'processing']);
                    
                    Payment::where('order_id', $order->id)->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $request->transaction_id,
                        'payment_type' => $request->payment_type,
                    ]);

                    // Send email invoice
                    try {
                        \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderInvoice($order));
                    } catch (\Exception $e) {
                        Log::error("Failed to send invoice email: " . $e->getMessage());
                    }
                }
            } else if (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
                $order = Order::with('items.variant')->where('order_code', $request->order_id)->first();
                if ($order && $order->status === 'pending') {
                    $order->update(['status' => 'cancelled']);
                    
                    Payment::where('order_id', $order->id)->update([
                        'status' => 'failed',
                        'midtrans_transaction_id' => $request->transaction_id,
                        'payment_type' => $request->payment_type,
                    ]);

                    // Auto-reverse stock
                    foreach ($order->items as $item) {
                        if ($item->variant) {
                            $item->variant->increment('stock', $item->quantity);
                        }
                    }
                }
            }
            return response()->json(['message' => 'Callback handled successfully']);
        }
        
        return response()->json(['message' => 'Invalid signature key'], 403);
    }
}
