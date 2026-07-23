# 4. Rute (Routes) dan Controller API

Rute-rute diatur secara modular di dalam `routes/web.php` berdasarkan hak akses:

## 1. Rute Publik (Public Routes)
Akses tanpa perlu login.
- `GET /` - Halaman Utama (`HomeController@index`)
- `GET /catalog` - Halaman Katalog Produk (`CatalogController@index`)
- `GET /product/{id}` - Detail Produk (`ProductController@show`)
- `POST /midtrans/callback` - *Webhook* notifikasi status dari Midtrans Gateway.

**Chat API:**
- Dapat diakses Publik. Fitur AI Chatbot menggunakan `ChatController` untuk menerima pesan (`/chat/send`) dan merender balasan otomatis.

## 2. Rute Terproteksi (User Authenticated)
Akses untuk pelanggan yang sudah login (`middleware: auth`).
- **Cart:** `/cart` - Manajemen keranjang.
- **Checkout & Orders:** `/checkout`, `/orders/{order}` - Proses pesanan.
- **Wishlist & Reviews:** `/wishlist`, `/reviews`.
- **Settings:** Mengelola profil, keamanan, dan pengaturan alamat pengiriman.
- **RajaOngkir API:** Menarik data ongkir `/api/rajaongkir/*`.

## 3. Rute Admin (Control Room)
Terproteksi dengan middleware `admin` (`/admin/*`).
- **Dashboard:** Ringkasan statistik toko.
- **Chat Management:** Memantau percakapan `/admin/chat`, mengambil alih sesi secara manual (`takeover`), atau mematikan fitur AI (Toggle Bot).
- **Products, Orders, Users:** Manajemen CRUD untuk seluruh data inti.
- **Coupons & Withdrawals:** Manajemen kode diskon dan persetujuan pencairan dana.

## 4. Rute Owner (Pemilik Toko)
Terproteksi dengan middleware `owner` (`/owner/*`).
- **Owner Dashboard:** Memantau keuntungan.
- **Withdraw:** Melakukan penarikan penghasilan bersih.
