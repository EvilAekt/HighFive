<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with(['images', 'variants', 'category'])
            ->findOrFail($id);

        $reviews = $product->reviews()->with('user')->latest()->paginate(5);

        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        // Recently Viewed Logic
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Remove current product if exists
        $recentlyViewed = array_diff($recentlyViewed, [$id]);
        
        // Add to front
        array_unshift($recentlyViewed, $id);
        
        // Keep max 5 items in session
        $recentlyViewed = array_slice($recentlyViewed, 0, 5);
        session()->put('recently_viewed', $recentlyViewed);
        
        // Fetch products excluding current one
        $recentlyViewedIds = array_diff($recentlyViewed, [$id]);
        $recentProducts = Product::whereIn('id', $recentlyViewedIds)
            ->where('is_active', true)
            ->get();

        return view('pages.product-detail', compact('product', 'relatedProducts', 'reviews', 'recentProducts'));
    }
    // Review logic moved to ReviewController
}
