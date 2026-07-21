<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['variant.product.images'])
            ->where('user_id', auth()->id())
            ->get();

        return view('pages.cart', compact('carts'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = \App\Models\ProductVariant::findOrFail($request->variant_id);
        
        $cart = Cart::where('user_id', auth()->id())
            ->where('product_variant_id', $request->variant_id)
            ->first();

        $newQuantity = $cart ? $cart->quantity + $request->quantity : $request->quantity;

        if ($newQuantity > $variant->stock) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi'
                ], 400);
            }
            return back()->with('error', 'Stok produk tidak mencukupi (Tersisa: ' . $variant->stock . ')');
        }

        if ($cart) {
            $cart->update([
                'quantity' => $newQuantity
            ]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_variant_id' => $request->variant_id,
                'quantity' => $request->quantity
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk ditambahkan ke keranjang'
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::with('variant')->where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->variant->stock
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove($id)
    {
        $cart = Cart::where('user_id', auth()->id())->findOrFail($id);
        $cart->delete();

        return back()->with('success', 'Produk dihapus dari keranjang');
    }
}
