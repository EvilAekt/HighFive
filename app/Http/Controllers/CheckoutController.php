<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Coupon;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['variant.product.images'])
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong');
        }

        $subtotal = $carts->sum(function ($cart) {
            return $cart->quantity * $cart->variant->product->price;
        });

        // Hardcode shipping cost for now, as in original React app
        $shippingCost = 25000;
        
        if ($subtotal > 500000) {
            $shippingCost = 0; // Free shipping for > 500k
        }
        
        // Calculate Discount from Session
        $discountAmount = 0;
        $appliedCoupon = session('applied_coupon');
        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon)->first();
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_purchase) {
                if ($coupon->type === 'fixed') {
                    $discountAmount = min($coupon->value, $subtotal);
                } else {
                    $discountAmount = ($subtotal * $coupon->value) / 100;
                    if ($coupon->max_discount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount);
                    }
                }
            } else {
                session()->forget('applied_coupon'); // Invalidated
            }
        }

        $total = $subtotal - $discountAmount + $shippingCost;

        return view('pages.checkout', compact('carts', 'subtotal', 'shippingCost', 'discountAmount', 'appliedCoupon', 'total'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);
        $code = strtoupper($request->coupon_code);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Kode kupon tidak ditemukan.');
        }

        if (!$coupon->isValid()) {
            return back()->with('error', 'Kupon sudah tidak aktif atau kuota habis.');
        }

        // Get subtotal
        $subtotal = Cart::where('user_id', auth()->id())->get()->sum(function ($cart) {
            return $cart->quantity * $cart->variant->product->price;
        });

        if ($subtotal < $coupon->min_purchase) {
            return back()->with('error', 'Minimal belanja untuk kupon ini adalah Rp ' . number_format($coupon->min_purchase, 0, ',', '.'));
        }

        session(['applied_coupon' => $code]);
        return back()->with('success', 'Kupon berhasil digunakan!');
    }

    public function store(Request $request, MidtransService $midtransService)
    {
        $request->validate([
            'address' => 'required|string|min:10',
            'phone' => 'required|string|min:10',
        ]);

        $user = auth()->user();
        
        // Update user profile if needed
        if (!$user->phone || !$user->address) {
            $user->update([
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
        }

        $carts = Cart::with(['variant.product'])->where('user_id', $user->id)->get();
        
        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong');
        }

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $itemsForMidtrans = [];

            foreach ($carts as $cart) {
                $price = $cart->variant->product->price;
                $subtotal += $price * $cart->quantity;
                
                $itemsForMidtrans[] = [
                    'id' => $cart->product_variant_id,
                    'price' => $price,
                    'quantity' => $cart->quantity,
                    'name' => $cart->variant->product->name . ' (' . $cart->variant->color . ' - ' . $cart->variant->size . ')',
                ];
            }

            $shippingCost = $subtotal > 500000 ? 0 : 25000;

            // Apply coupon logic
            $discountAmount = 0;
            $appliedCouponCode = session('applied_coupon');
            if ($appliedCouponCode) {
                $coupon = Coupon::where('code', $appliedCouponCode)->first();
                if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_purchase) {
                    if ($coupon->type === 'fixed') {
                        $discountAmount = min($coupon->value, $subtotal);
                    } else {
                        $discountAmount = ($subtotal * $coupon->value) / 100;
                        if ($coupon->max_discount) {
                            $discountAmount = min($discountAmount, $coupon->max_discount);
                        }
                    }
                    $coupon->increment('current_uses');
                }
            }

            $totalPrice = $subtotal - $discountAmount + $shippingCost;
            $orderCode = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'shipping_address' => $request->address,
                'shipping_cost' => $shippingCost,
                'coupon_code' => $appliedCouponCode,
                'discount_amount' => $discountAmount,
                'status' => 'pending'
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cart->product_variant_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->variant->product->price,
                ]);

                // Reduce stock
                $cart->variant->decrement('stock', $cart->quantity);
            }

            // Get snap token
            $snapToken = $midtransService->createSnapToken($order, $user, $itemsForMidtrans);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => 'pending',
                'snap_token' => $snapToken
            ]);

            // Clear cart & session
            Cart::where('user_id', $user->id)->delete();
            session()->forget('applied_coupon');

            DB::commit();

            return redirect()->route('orders.show', ['order' => $order->id, 'payment' => 'true']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }
    }
}
