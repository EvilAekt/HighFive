<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'user')->count();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        $topProducts = Product::withCount('reviews')
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();


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

        $botActive = \Illuminate\Support\Facades\Cache::get('bot_active', true);
        $aiActive = \Illuminate\Support\Facades\Cache::get('ai_active', true);

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalProducts', 'totalCustomers', 
            'ordersByStatus', 'recentOrders', 'topProducts',
            'lowStockProducts', 'recentReviews', 'pendingOrders', 'last7Days', 'sales7DaysData', 'botActive', 'aiActive'
        ));
    }
}
