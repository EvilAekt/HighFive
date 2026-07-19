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
            'weight' => 'required|integer|min:1',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'nullable|boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end' => 'nullable|date',
        ]);

        $productData = $request->except(['image_urls', 'variants']);
        // Format checkbox value
        $productData['is_flash_sale'] = $request->has('is_flash_sale');
        if (!$productData['is_flash_sale']) {
            $productData['flash_sale_price'] = null;
            $productData['flash_sale_end'] = null;
        }
        
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
            'weight' => 'required|integer|min:1',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|url',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'nullable|boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end' => 'nullable|date',
        ]);

        $productData = $request->except(['image_urls', 'variants']);
        $productData['is_flash_sale'] = $request->has('is_flash_sale');
        if (!$productData['is_flash_sale']) {
            $productData['flash_sale_price'] = null;
            $productData['flash_sale_end'] = null;
        }
        
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

        // Update variants (prevent FK constraint violation by syncing rather than blanket delete)
        $existingVariantIds = $product->variants()->pluck('id')->toArray();
        $submittedVariantIds = [];

        foreach ($request->variants as $variant) {
            if (isset($variant['id']) && in_array($variant['id'], $existingVariantIds)) {
                // Update existing
                $product->variants()->where('id', $variant['id'])->update([
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'stock' => $variant['stock'],
                    'additional_price' => $variant['additional_price'] ?? 0,
                ]);
                $submittedVariantIds[] = $variant['id'];
            } else {
                // Create new
                $newVariant = $product->variants()->create([
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'stock' => $variant['stock'],
                    'additional_price' => $variant['additional_price'] ?? 0,
                ]);
                $submittedVariantIds[] = $newVariant->id;
            }
        }

        // Handle deletions of variants that were removed in the UI
        $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
        if (!empty($variantsToDelete)) {
            foreach ($variantsToDelete as $varId) {
                try {
                    $product->variants()->where('id', $varId)->delete();
                } catch (\Illuminate\Database\QueryException $e) {
                    // Cannot delete because it's referenced in order_items
                    // Set stock to 0 instead of deleting
                    $product->variants()->where('id', $varId)->update(['stock' => 0]);
                }
            }
        }

        return back()->with('success', 'Produk dan varian berhasil diupdate');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Produk berhasil dihapus');
    }
}
