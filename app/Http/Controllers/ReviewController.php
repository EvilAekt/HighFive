<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image',
            'video' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo',
        ]);

        // Check if order belongs to user and is paid
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Pesanan ini belum dapat diberi ulasan.');
        }

        // Check if review already exists for this order item
        $existingReview = Review::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan ini.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
        }

        Review::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imagePath,
            'video' => $videoPath,
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan! Terima kasih atas feedback Anda.');
    }
}
