# 3. Struktur Database (Skema Detail)

Sistem HIGH FIVE menggunakan arsitektur *relational database* (MySQL/PostgreSQL/SQLite). Berikut adalah rincian kolom (skema) untuk tabel-tabel krusial berdasarkan *file* migrasi:

## A. Tabel `users`
Tabel utama untuk autentikasi dan profil semua jenis peran.
- `id`: *Primary Key* (BigInt / UUID)
- `name`: Nama lengkap
- `email`: Alamat email unik
- `password`: *Hashed password*
- `role`: Enum/String (`user`, `admin`, `owner`). Diperbarui via migrasi `modify_role_column_in_users_table`.
- `avatar`: URL foto profil (opsional)
- Kolom tambahan dari Social Login (`provider_id`, `provider_name`).

## B. Katalog Produk
**`products`**
- `id`: UUID (Primary Key)
- `category_id`: Relasi ke tabel kategori
- `name`: Nama artikel pakaian
- `description`: Deskripsi lengkap
- `price`: Harga dasar (Decimal 12,2)
- `weight`: Berat barang (untuk perhitungan ongkir)
- `thumbnail`: Foto utama produk
- `is_active`: Status aktif/non-aktif katalog
- `is_hype` & `flash_sale`: Status promo kilat.

**`product_variants`**
- `id`: UUID (Primary Key)
- `product_id`: *Foreign Key* ke tabel `products`
- `size`: Ukuran (S, M, L, XL, dll)
- `color`: Warna (Hitam, Putih, dll)
- `stock`: **Kuantitas fisik riil di gudang**. (Integer)

## C. Tabel Pesanan & Pembayaran
**`orders`**
- `id`: UUID (Primary Key)
- `user_id`: Pembeli
- `order_code`: Kode resi/pesanan unik (e.g. `ORD-AB12XY`)
- `total_price`: Total tagihan akhir (setelah diskon + ongkir)
- `status`: Enum (`pending`, `processing`, `shipped`, `delivered`, `cancelled`)
- `shipping_address`: Alamat lengkap pengiriman
- `shipping_cost`: Biaya ongkir
- `shipping_courier`, `shipping_service`: Kurir & Layanan (e.g., JNE REG)
- `coupon_code` & `discount_amount`: Rekaman diskon yang dipakai.

**`order_items`**
- `id`: UUID
- `order_id`: Relasi ke pesanan
- `product_variant_id`: Merujuk secara spesifik ke *variant* (bukan *product* induk), karena ini menentukan barang persis mana (warna/ukuran) yang dikurangi stoknya.
- `quantity`: Jumlah beli
- `price`: Harga saat barang dibeli (mencegah perubahan harga produk merusak riwayat transaksi lama).

**`payments`**
- `id`: UUID
- `order_id`: Relasi pesanan
- `amount`: Jumlah dibayar
- `status`: Status dari Midtrans (`pending`, `settlement`, `expire`, dll)
- `snap_token`: Token khusus dari Midtrans untuk merender UI pembayaran (Pop-up Snap).

## D. Tabel Obrolan (`messages`)
- `id`: UUID
- `session_id`: Mengelompokkan riwayat *chat*. Jika *user* login, menggunakan `user_id`. Jika *guest*, menggunakan UUID unik (disimpan di *session browser*).
- `user_id`: (Nullable) Jika tamu, ini kosong.
- `is_admin`: Boolean (True jika dibalas Admin/Bot, False jika diketik Pelanggan)
- `content`: Teks isi obrolan
- `reply_to_id`: *Self-referencing Foreign Key* untuk menandakan sebuah pesan adalah balasan spesifik dari pesan lain (penting untuk UI *threaded chat*).
- `product_id`: (Nullable) Jika diisi, *frontend* akan merender **Kartu Produk** di dalam gelembung obrolan alih-alih teks biasa.
