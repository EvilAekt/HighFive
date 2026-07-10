<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $brandProfile = BrandProfile::first();
        $categories = Category::all();
        $featuredProducts = Product::with(['images', 'reviews'])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('pages.home', compact('brandProfile', 'categories', 'featuredProducts'));
    }
}
