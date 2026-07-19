<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment', 'items.variant.product'])->latest();

        if ($request->has('filter') && $request->filter !== 'all') {
            $query->where('status', $request->filter);
        }

        $orders = $query->get();
        
        return view('admin.orders', compact('orders'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'resi_number' => 'nullable|string'
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $request->status,
            'resi_number' => $request->resi_number ?? $order->resi_number
        ]);

        // Send email if status changed to shipped
        if ($oldStatus !== 'shipped' && $request->status === 'shipped') {
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderShipped($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send shipped email: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }
}
