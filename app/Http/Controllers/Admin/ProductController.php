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
            'existing_images' => 'nullable|array',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:15360',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'nullable|boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end' => 'nullable|date',
        ]);

        $productData = $request->except(['existing_images', 'image_files', 'variants']);
        // Format checkbox value
        $productData['is_flash_sale'] = $request->has('is_flash_sale');
        if (!$productData['is_flash_sale']) {
            $productData['flash_sale_price'] = null;
            $productData['flash_sale_end'] = null;
        }
        
        $imagePaths = [];
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $path = $file->store('products', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
        }
        
        if (count($imagePaths) > 0) {
            $productData['thumbnail'] = $imagePaths[0];
        }

        $product = Product::create($productData);

        // Store into product_images
        foreach ($imagePaths as $index => $path) {
            $product->images()->create([
                'image_path' => $path,
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
            'existing_images' => 'nullable|array',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:15360',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'is_flash_sale' => 'nullable|boolean',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end' => 'nullable|date',
        ]);

        $productData = $request->except(['existing_images', 'image_files', 'variants']);
        $productData['is_flash_sale'] = $request->has('is_flash_sale');
        if (!$productData['is_flash_sale']) {
            $productData['flash_sale_price'] = null;
            $productData['flash_sale_end'] = null;
        }
        
        $imagePaths = $request->existing_images ?? [];
        
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                $path = $file->store('products', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
        }
        
        if (count($imagePaths) > 0) {
            $productData['thumbnail'] = $imagePaths[0];
        }

        $product->images()->delete();
        foreach ($imagePaths as $index => $path) {
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $index === 0
            ]);
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
        try {
            Product::findOrFail($id)->delete();
            return back()->with('success', 'Produk berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Produk tidak bisa dihapus karena memiliki riwayat pesanan (Order). Silakan ubah status produk menjadi "Inactive".');
        }
    }
}
