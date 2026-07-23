# 8. Kamus Lengkap Database (Tabel & Kolom)

Dokumen ini menjelaskan secara definitif SETIAP TABEL dan SETIAP KOLOM yang ada di dalam database HIGH FIVE berdasarkan file migrasi (`database/migrations/`).

## 1. Tabel `users`
Menyimpan data pengguna terdaftar (pembeli, admin, owner).
- `id` (bigint/uuid): Primary Key.
- `name` (string): Nama lengkap pengguna.
- `email` (string): Email (unik) untuk login.
- `password` (string): Kata sandi terenkripsi (bcrypt).
- `role` (enum): Menentukan tingkat akses (`user`, `admin`, `owner`).
- `avatar` (string, nullable): URL foto profil pengguna.
- `provider_id` & `provider_name` (string, nullable): ID dan Nama platform jika menggunakan Social Login (Google/Facebook).
- `created_at`, `updated_at`: Timestamp standar Laravel.

## 2. Tabel `products`
Menyimpan entitas utama dari pakaian (katalog induk).
- `id` (uuid): Primary key.
- `category_id` (uuid): Foreign key ke tabel `categories`.
- `name` (string): Nama produk (contoh: "Kaos Oversize Black").
- `description` (text): Penjelasan produk.
- `price` (decimal 12,2): Harga dasar.
- `weight` (decimal/integer): Berat barang dalam gram, penting untuk API RajaOngkir.
- `thumbnail` (string): Path gambar sampul.
- `is_active` (boolean): Apakah produk ini ditampilkan di katalog pengunjung?
- `is_hype` (boolean): Penanda produk eksklusif.
- `flash_sale` (boolean): Penanda apakah sedang didiskon kilat.

## 3. Tabel `product_variants`
Anak dari tabel `products`. Menyimpan detail variasi dan STOK FISIK.
- `id` (uuid): Primary key.
- `product_id` (uuid): Foreign key ke `products`.
- `size` (string): Ukuran (S, M, L, XL).
- `color` (string): Varian warna.
- `stock` (integer): Jumlah barang fisik riil yang ada di gudang. Ini yang dikurangi saat Checkout!
- `additional_price` (decimal 12,2): Tambahan harga jika varian tertentu (misal ukuran XXL) lebih mahal.

## 4. Tabel `product_images`
Menyimpan galeri foto produk tambahan.
- `id` (uuid)
- `product_id` (uuid)
- `image_path` (string): Lokasi file di server.
- `is_primary` (boolean): Menentukan apakah ini gambar utama (opsional jika sudah ada thumbnail).

## 5. Tabel `orders`
Menyimpan data transaksi/keranjang belanja yang sudah diselesaikan.
- `id` (uuid)
- `user_id` (bigint): Pembeli.
- `order_code` (string): Kode resi unik.
- `total_price` (decimal 12,2): Total akhir yang harus dibayar pembeli.
- `status` (enum): `pending`, `processing`, `shipped`, `delivered`, `cancelled`.
- `shipping_address` (text): Alamat lengkap pengiriman yang dipilih pembeli.
- `shipping_cost` (decimal 12,2): Ongkos kirim dari API RajaOngkir.
- `shipping_courier`, `shipping_service` (string): Kurir terpilih (JNE, REG, dsb).
- `coupon_code` (string, nullable): Kupon yang diterapkan.
- `discount_amount` (decimal, nullable): Besaran potongan kupon.

## 6. Tabel `order_items`
Rincian pernak-pernik barang di dalam satu pesanan (`orders`).
- `id` (uuid)
- `order_id` (uuid): Merujuk ke pesanan induk.
- `product_variant_id` (uuid): Merujuk ke varian spesifik agar warna dan ukuran terekam jelas.
- `quantity` (integer): Jumlah barang ini yang dibeli.
- `price` (decimal 12,2): Harga snapshot (harga pada saat dibeli).

## 7. Tabel `payments`
Gateway transaksi dan status pembayaran Midtrans.
- `id` (uuid)
- `order_id` (uuid): Pesanan yang dibayar.
- `amount` (decimal): Nominal.
- `status` (enum): `pending`, `paid`, `failed`.
- `snap_token` (string): Token khusus Midtrans agar Pop-up Checkout bisa terbuka di frontend.
- `midtrans_order_id`, `midtrans_transaction_id`: Referensi tracking API Midtrans.

## 8. Tabel `carts`
Keranjang belanja sementara milik pengguna (sebelum Checkout).
- `id` (uuid)
- `user_id` (bigint)
- `product_variant_id` (uuid)
- `quantity` (integer): Jumlah dimasukkan ke keranjang. (Sistem akan memblokir jika quantity melebihi `product_variants.stock`).

## 9. Tabel `coupons`
Manajemen kode diskon oleh admin.
- `id` (bigint)
- `code` (string): Kode voucher (contoh: "PROMO20").
- `type` (enum): `percentage` (persen) atau `fixed` (potongan statis misal Rp 50.000).
- `value` (decimal): Besaran nilai diskon.
- `min_purchase` (decimal): Minimal belanja agar kupon berlaku.
- `max_discount` (decimal): Batas atas diskon untuk tipe persentase.
- `current_uses` (integer): Berapa kali kupon ini sudah terpakai.

## 10. Tabel `messages`
Sistem riwayat Live Chat (Manusia & Gemini AI).
- `id` (bigint)
- `session_id` (string): Pengelompokan utas chat.
- `user_id` (bigint, nullable): ID pengguna jika login.
- `is_admin` (boolean): True jika ini pesan balasan dari Admin atau AI. False jika dari pelanggan.
- `content` (text): Isi pesan obrolan.
- `reply_to_id` (bigint, nullable): Merujuk pada ID pesan sebelumnya, berfungsi menyusun hirarki (thread) balasan.
- `product_id` (uuid, nullable): (Ditambahkan di migrasi akhir) Jika diisi, sistem akan menampilkan Visual Product Card di dalam ruang chat.

## 11. Tabel `reviews` & `wishlists`
- `reviews`: Berisi `rating`, `comment`, `image`, `video`. Divalidasi hanya bisa diisi jika status pesanan `delivered`.
- `wishlists`: Penghubung Many-to-Many antara `users` dan `products` favorit.
