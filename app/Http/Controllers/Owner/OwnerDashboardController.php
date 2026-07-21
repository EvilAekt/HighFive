<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Gross Revenue: Sum of all paid orders
        $grossRevenue = Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total_price');
        
        // Count total paid orders
        $totalOrders = Order::whereIn('status', ['processing', 'shipped', 'delivered'])->count();

        // Total Withdrawn
        $totalWithdrawn = Withdrawal::where('status', 'completed')->sum('amount');
        
        // Available Balance = Gross Revenue - Total Withdrawn
        $availableBalance = $grossRevenue - $totalWithdrawn;

        // Bestseller Products (By counting order items)
        $bestsellers = Product::withCount(['variants as sold_count' => function($query) {
            $query->join('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
                  ->join('orders', 'orders.id', '=', 'order_items.order_id')
                  ->whereIn('orders.status', ['processing', 'shipped', 'delivered']);
        }])->orderByDesc('sold_count')->take(5)->get();

        // Recent Withdrawals
        $withdrawals = Withdrawal::with('user')->latest()->take(5)->get();

        // Low Stock Products (variants with stock < 5)
        $lowStockProducts = \App\Models\ProductVariant::with('product')
            ->where('stock', '<', 5)
            ->get();

        // Recent Reviews
        $recentReviews = \App\Models\Review::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        // Pending / Processing Orders
        $pendingOrders = Order::with('user')
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->take(5)
            ->get();

        // 7-day Sales Chart
        $last7Days = collect();
        $sales7DaysData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $last7Days->push(now()->subDays($i)->format('d M'));
            
            $dailySalesPayment = \App\Models\Payment::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('amount');
                
            $sales7DaysData->push($dailySalesPayment);
        }

        return view('owner.dashboard', compact(
            'grossRevenue', 'totalOrders', 'totalWithdrawn', 'availableBalance', 'bestsellers', 'withdrawals',
            'lowStockProducts', 'recentReviews', 'pendingOrders', 'last7Days', 'sales7DaysData'
        ));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $grossRevenue = Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total_price');
        $totalWithdrawn = Withdrawal::where('status', 'completed')->sum('amount');
        $pendingWithdrawal = Withdrawal::where('status', 'pending')->sum('amount');
        
        $availableBalance = $grossRevenue - $totalWithdrawn - $pendingWithdrawal;

        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi untuk ditarik. (Harap perhatikan penarikan yang masih pending)');
        }

        Withdrawal::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Permintaan tarik dana berhasil dibuat dan sedang diproses admin keuangan.');
    }
}
