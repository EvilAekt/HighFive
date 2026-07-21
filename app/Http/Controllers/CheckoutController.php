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
            return $cart->quantity * $cart->variant->product->current_price;
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
            return $cart->quantity * $cart->variant->product->current_price;
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'address' => 'required|string|min:10',
            'phone' => 'required|string|min:10',
            'shipping_province' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_city_id' => 'required|string',
            'shipping_courier' => 'required|string',
            'shipping_service' => 'required|string',
        ]);

        $fullAddress = $request->address . "\n" . $request->shipping_city . ", " . $request->shipping_province;

        $user = auth()->user();
        
        // Always update the profile to ensure latest details are used
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $carts = Cart::with(['variant.product'])->where('user_id', $user->id)->get();
        
        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong');
        }

        try {
            DB::beginTransaction();

            // Lock variants to prevent race conditions (negative stock)
            $variantIds = $carts->pluck('product_variant_id')->toArray();
            $lockedVariants = \App\Models\ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get();

            $subtotal = 0;
            $totalWeight = 0;
            $itemsForMidtrans = [];

            foreach ($carts as $cart) {
                // Verify stock availability
                $lockedVariant = $lockedVariants->firstWhere('id', $cart->product_variant_id);
                if (!$lockedVariant || $lockedVariant->stock < $cart->quantity) {
                    DB::rollBack();
                    return back()->with('error', 'Maaf, stok untuk produk ' . $cart->variant->product->name . ' (' . $cart->variant->size . ') tidak mencukupi atau sudah habis.');
                }

                $price = $cart->variant->product->current_price;
                $subtotal += $price * $cart->quantity;
                $totalWeight += ($cart->variant->product->weight * $cart->quantity);
                
                $itemsForMidtrans[] = [
                    'id' => $cart->product_variant_id,
                    'price' => $price,
                    'quantity' => $cart->quantity,
                    'name' => $cart->variant->product->name . ' (' . $cart->variant->color . ' - ' . $cart->variant->size . ')',
                ];
            }

            // Calculate shipping from RajaOngkir
            $shippingCost = 0;
            if ($subtotal <= 500000) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                        'key' => env('RAJAONGKIR_API_KEY')
                    ])->post(env('RAJAONGKIR_BASE_URL', 'https://api.rajaongkir.com/starter') . "/cost", [
                        'origin' => env('RAJAONGKIR_ORIGIN_CITY', 501),
                        'destination' => $request->shipping_city_id,
                        'weight' => $totalWeight > 0 ? $totalWeight : 1000,
                        'courier' => strtolower($request->shipping_courier)
                    ]);
                    
                    if (!$response->successful()) throw new \Exception('API Error');

                    $costs = $response->json()['rajaongkir']['results'][0]['costs'] ?? [];
                    $selectedCostObj = collect($costs)->firstWhere('service', $request->shipping_service);
                    
                    if ($selectedCostObj) {
                        $shippingCost = $selectedCostObj['cost'][0]['value'];
                    } else {
                        throw new \Exception('Invalid shipping service selected');
                    }
                } catch (\Exception $e) {
                    // Fallback to mock cost for local dev if RajaOngkir fails
                    $shippingCost = $request->shipping_service === 'REG' ? 25000 : 45000;
                }
            }

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
                'shipping_address' => $fullAddress,
                'shipping_cost' => $shippingCost,
                'shipping_courier' => strtoupper($request->shipping_courier),
                'shipping_service' => $request->shipping_service,
                'coupon_code' => $appliedCouponCode,
                'discount_amount' => $discountAmount,
                'status' => 'pending'
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cart->product_variant_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->variant->product->current_price,
                ]);

                // Reduce stock using the locked variant
                $lockedVariant = $lockedVariants->firstWhere('id', $cart->product_variant_id);
                $lockedVariant->decrement('stock', $cart->quantity);
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
