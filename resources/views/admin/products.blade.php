@extends('layouts.admin')

@section('content')
<div x-data="{ modalOpen: false, editMode: false, currentId: null }">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Kelola Produk</h1>
        <button @click="modalOpen = true; editMode = false; currentId = null; document.getElementById('productForm').reset(); window.dispatchEvent(new CustomEvent('reset-images'));" class="btn-primary flex items-center gap-2 px-4 py-2 text-xs">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Produk
        </button>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 text-red-700 p-4 border border-red-200">
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-primary-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                        <th class="px-4 py-3 font-medium">Produk</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Harga</th>
                        <th class="px-4 py-3 font-medium">Varian</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-primary-100 last:border-0 hover:bg-primary-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-primary-100 flex-shrink-0 overflow-hidden">
                                        @if($product->thumbnail)
                                            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-primary-400 text-xs">
                                                No image
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium">{{ $product->name }}</p>
                                            @if($product->is_flash_sale)
                                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">
                                                    <i data-lucide="zap" class="w-3 h-3 fill-red-700"></i> Flash Sale
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-primary-500 truncate max-w-xs">{{ $product->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ formatPrice($product->price) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->variants->count() }} varian</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <script type="application/json" id="product-data-{{ $product->id }}">
                                        {!! json_encode([
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'description' => $product->description,
                                            'price' => $product->price,
                                            'category_id' => $product->category_id,
                                            'weight' => $product->weight,
                                            'is_active' => $product->is_active,
                                            'thumbnail' => $product->thumbnail,
                                            'is_flash_sale' => $product->is_flash_sale,
                                            'flash_sale_price' => $product->flash_sale_price,
                                            'flash_sale_end' => $product->flash_sale_end ? \Carbon\Carbon::parse($product->flash_sale_end)->format('Y-m-d\TH:i') : null,
                                            'images' => $product->images->toArray(),
                                            'variants' => $product->variants->toArray()
                                        ]) !!}
                                    </script>
                                    <button @click="modalOpen = true; editMode = true; currentId = '{{ $product->id }}'; loadProductData('{{ $product->id }}');" 
                                            class="p-2 hover:bg-primary-100 rounded transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 hover:bg-red-50 text-red-500 rounded transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-primary-500">Belum ada produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-primary-200">
                <h3 class="text-lg font-semibold" x-text="editMode ? 'Edit Produk' : 'Tambah Produk'"></h3>
                <button @click="modalOpen = false" class="p-1 hover:bg-primary-100 rounded">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" class="p-4 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Produk *</label>
                    <input type="text" name="name" id="name" class="input-field" required />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="input-field"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga (Rp) *</label>
                    <input type="number" name="price" id="price" class="input-field" min="0" required />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Berat (Gram) *</label>
                    <input type="number" name="weight" id="weight" class="input-field" min="1" value="1000" required />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Kategori *</label>
                    <select name="category_id" id="category_id" class="input-field" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-data="{ images: [''] }" @reset-images.window="images = ['']" @load-images.window="images = $event.detail.length ? $event.detail : ['']">
                    <label class="block text-sm font-medium mb-1">URL Gambar Produk (Multiple)</label>
                    <template x-for="(image, index) in images" :key="index">
                        <div class="flex gap-2 mb-2">
                            <input type="url" :name="'image_urls['+index+']'" x-model="images[index]" class="input-field flex-1" placeholder="https://example.com/image.jpg" />
                            <button type="button" @click="images.splice(index, 1)" x-show="images.length > 1" class="px-3 py-2 bg-red-50 text-red-500 hover:bg-red-100 border border-red-200">
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="images.push('')" class="text-xs font-semibold uppercase tracking-widest text-primary-600 hover:text-black mt-2 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Gambar Lainnya
                    </button>
                </div>

                <div x-data="{ variants: [{ id: '', size: '', color: '', stock: 0, additional_price: 0 }] }" @reset-images.window="variants = [{ id: '', size: '', color: '', stock: 0, additional_price: 0 }]" @load-variants.window="variants = $event.detail.length ? $event.detail : [{ id: '', size: '', color: '', stock: 0, additional_price: 0 }]">
                    <label class="block text-sm font-medium mb-2">Stok & Varian (Wajib)</label>
                    <template x-for="(variant, index) in variants" :key="index">
                        <div class="bg-primary-50 p-3 border border-primary-200 mb-2 relative">
                            <input type="hidden" :name="'variants['+index+'][id]'" x-model="variant.id" />
                            <button type="button" @click="variants.splice(index, 1)" x-show="variants.length > 1" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                            <div class="grid grid-cols-2 gap-3 mb-2 pr-6">
                                <div>
                                    <label class="text-xs text-primary-600 mb-1 block">Warna</label>
                                    <input type="text" :name="'variants['+index+'][color]'" x-model="variant.color" class="input-field py-1.5 text-sm" placeholder="Mis: Hitam" required />
                                </div>
                                <div>
                                    <label class="text-xs text-primary-600 mb-1 block">Ukuran</label>
                                    <input type="text" :name="'variants['+index+'][size]'" x-model="variant.size" class="input-field py-1.5 text-sm" placeholder="Mis: XL" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-primary-600 mb-1 block">Stok</label>
                                    <input type="number" :name="'variants['+index+'][stock]'" x-model="variant.stock" class="input-field py-1.5 text-sm" min="0" required />
                                </div>
                                <div>
                                    <label class="text-xs text-primary-600 mb-1 block">Harga Tambahan (Opsional)</label>
                                    <input type="number" :name="'variants['+index+'][additional_price]'" x-model="variant.additional_price" class="input-field py-1.5 text-sm" min="0" />
                                </div>
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="variants.push({ id: '', size: '', color: '', stock: 0, additional_price: 0 })" class="text-xs font-semibold uppercase tracking-widest text-primary-600 hover:text-black mt-2 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Varian Baru
                    </button>
                </div>

                <!-- Flash Sale Section -->
                <div x-data="{ isFlashSale: false }" @load-flash.window="isFlashSale = $event.detail; document.getElementById('is_flash_sale').checked = $event.detail;" @reset-images.window="isFlashSale = false; document.getElementById('is_flash_sale').checked = false;" class="bg-red-50 p-4 border border-red-200 mt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <input type="checkbox" name="is_flash_sale" id="is_flash_sale" value="1" x-model="isFlashSale" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" />
                        <label for="is_flash_sale" class="text-sm font-bold text-red-700 uppercase">Aktifkan Flash Sale</label>
                    </div>
                    
                    <div x-show="isFlashSale" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-red-700 mb-1">Harga Flash Sale (Rp) *</label>
                            <input type="number" name="flash_sale_price" id="flash_sale_price" class="input-field border-red-300 focus:border-red-500 focus:ring-red-500" min="0" :required="isFlashSale" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-red-700 mb-1">Batas Waktu (End Date) *</label>
                            <input type="datetime-local" name="flash_sale_end" id="flash_sale_end" class="input-field border-red-300 focus:border-red-500 focus:ring-red-500" :required="isFlashSale" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4" />
                    <label for="is_active" class="text-sm">Produk aktif</label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="modalOpen = false" class="flex-1 btn-secondary text-sm">Batal</button>
                    <button type="submit" class="flex-1 btn-primary text-sm" x-text="editMode ? 'Simpan Perubahan' : 'Tambah Produk'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadProductData(id) {
        try {
            let data = JSON.parse(document.getElementById('product-data-' + id).textContent);
            
            document.getElementById('name').value = data.name || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('price').value = data.price || 0;
            document.getElementById('weight').value = data.weight || 1000;
            document.getElementById('category_id').value = data.category_id || '';
            document.getElementById('is_active').checked = !!data.is_active;
            
            document.getElementById('flash_sale_price').value = data.flash_sale_price || '';
            document.getElementById('flash_sale_end').value = data.flash_sale_end || '';
            window.dispatchEvent(new CustomEvent('load-flash', { detail: !!data.is_flash_sale }));
            
            let images = [];
            if (data.images && data.images.length > 0) {
                images = data.images.map(i => i.image_path);
            } else if (data.thumbnail) {
                images.push(data.thumbnail);
            }
            if (images.length === 0) images.push('');
            
            window.dispatchEvent(new CustomEvent('load-images', { detail: images }));
            
            let variants = [];
            if (data.variants && data.variants.length > 0) {
                variants = data.variants.map(v => ({
                    id: v.id,
                    size: v.size,
                    color: v.color,
                    stock: v.stock,
                    additional_price: v.additional_price
                }));
            }
            if (variants.length === 0) variants.push({ id: '', size: '', color: '', stock: 0, additional_price: 0 });
            
            window.dispatchEvent(new CustomEvent('load-variants', { detail: variants }));
            
            document.getElementById('productForm').action = '/admin/products/' + id;
            document.getElementById('methodField').value = 'PUT';
        } catch (e) {
            console.error('Error parsing product data', e);
        }
    }
</script>
@endsection
