# 11. Kamus Lengkap Models (Relasi & Eloquent)

Tabel di database dihubungkan menggunakan ORM (Object-Relational Mapping) Laravel yang disebut **Eloquent**. File model terletak di `app/Models/`.

## A. Fitur Khusus (Traits) pada Model
Sebagian besar model utama (seperti `Product`, `Order`, `ProductVariant`) menggunakan *Traits* khusus:
- **`HasUuids`**: Menggantikan *auto-increment* bawaan agar `id` tabel berupa string UUID (Universal Unique Identifier). Ini sangat aman karena mencegah peretas menebak URL (misalnya `id=1`, `id=2`).
- **`HasFactory`**: Mengizinkan model diisi data palsu (*dummy/seeder*) untuk keperluan testing.

## B. Penjelasan Metode Relasi (Relationships)

### 1. `User` Model
Merupakan pusat autentikasi yang menggunakan *trait* `Notifiable`.
- **`isAdmin()`, `isOwner()`, `isUser()`**: *Helper method* kustom yang mengecek `role` pengguna. Sangat sering dipanggil di file *Blade* dengan format `auth()->user()->isAdmin()`.
- **`addresses()`**: Relasi *One-to-Many* (`hasMany`) ke tabel `UserAddress`.

### 2. `Product` Model
Entitas pusat toko.
- **`category()`**: *BelongsTo* ke `Category`.
- **`variants()`**: *HasMany* ke `ProductVariant`. Artinya satu model kaos bisa ditarik seluruh datanya sekaligus (warna hitam, putih, dll).
- **`images()`**: *HasMany* ke `ProductImage`.
- **`reviews()`**: *HasMany* ke `Review`.
- **Accessors `getCurrentPriceAttribute()`**: Ini adalah fungsi magis. Di mana pun kita memanggil `$product->current_price`, Laravel otomatis mengecek apakah status promo `flash_sale` sedang aktif atau diskon *hype* berjalan. Jika ya, nilai kembaliannya bukan harga normal.

### 3. `Order` Model
Merupakan catatan transaksi induk.
- **`user()`**: *BelongsTo* `User` (Siapa pembelinya).
- **`items()`**: *HasMany* ke `OrderItem` (Apa saja yang ada di dalam keranjang saat checkout).
- **`payment()`**: *HasOne* ke `Payment`. 

### 4. `Message` Model (Chatbot AI)
- **`replyTo()`**: Relasi *BelongsTo* ke `Message` itu sendiri (*Self-Referencing*). Digunakan untuk melacak bahwa Pesan B adalah balasan dari Pesan A.
- **`product()`**: Relasi *BelongsTo* ke `Product`. Jika kolom ini terisi berkat output JSON Gemini, UI chat akan memanggil relasi ini dan me-render kotak produk.

### 5. `Cart` Model
- **`variant()`**: *BelongsTo* ke `ProductVariant`. Berbeda dengan `Order` yang menyimpan snapshot `price`, keranjang belanja selalu mengambil relasi langsung ke varian agar ketika harga di- *update* oleh admin, keranjang pelanggan otomatis mengikuti harga terbaru.

### 6. `Coupon` Model
- **`isValid()`**: Fungsi kustom yang mengembalikan boolean (`true/false`). Logikanya: `return $this->is_active && $this->current_uses < $this->max_uses && ($this->valid_until == null || $this->valid_until >= now());`
