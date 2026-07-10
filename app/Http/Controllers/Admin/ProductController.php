<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'variants', 'images'])->latest()->get();
        $categories = Category::all();
        return view('admin.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
        ]);

        $productData = $request->except(['image_urls', 'variants']);
        
        // Get the first valid image url as thumbnail
        $imageUrls = array_filter($request->image_urls ?? []);
        if (count($imageUrls) > 0) {
            $productData['thumbnail'] = reset($imageUrls);
        }

        $product = Product::create($productData);

        // Store into product_images
        foreach ($imageUrls as $index => $url) {
            $product->images()->create([
                'image_path' => $url,
                'is_primary' => $index === 0
            ]);
        }

        // Store variants
        foreach ($request->variants as $variant) {
            $product->variants()->create([
                'size' => $variant['size'],
                'color' => $variant['color'],
                'stock' => $variant['stock'],
                'additional_price' => $variant['additional_price'] ?? 0,
            ]);
        }

        return back()->with('success', 'Produk dan varian berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
        ]);

        $productData = $request->except(['image_urls', 'variants']);
        
        $imageUrls = array_filter($request->image_urls ?? []);
        if (count($imageUrls) > 0) {
            $productData['thumbnail'] = reset($imageUrls);
            
            // Delete old images and insert new ones
            $product->images()->delete();
            foreach ($imageUrls as $index => $url) {
                $product->images()->create([
                    'image_path' => $url,
                    'is_primary' => $index === 0
                ]);
            }
        }

        $product->update($productData);

        // Update variants (delete old and insert new for simplicity, or sync if id exists)
        $product->variants()->delete();
        foreach ($request->variants as $variant) {
            $product->variants()->create([
                'size' => $variant['size'],
                'color' => $variant['color'],
                'stock' => $variant['stock'],
                'additional_price' => $variant['additional_price'] ?? 0,
            ]);
        }

        return back()->with('success', 'Produk dan varian berhasil diupdate');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Produk berhasil dihapus');
    }
}
