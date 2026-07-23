# 9. Kamus Lengkap Controllers (Fungsi & File)

Berikut adalah daftar setiap file Controller di direktori `app/Http/Controllers/` beserta fungsi spesifiknya:

## Controller Publik & Pengunjung
1. **`HomeController.php`**
   - Menangani logika halaman depan (Beranda). 
   - Fungsi utama: Mengambil daftar koleksi terbaru, promo Hype Drops, atau kategori populer.
2. **`CatalogController.php`**
   - Mengelola halaman pencarian dan daftar produk (`/catalog`).
   - Fungsi utama: Menangani filter (ukuran, warna, kategori), sorting harga, dan pagination.
3. **`ProductController.php`**
   - Menangani detail satu produk tunggal (`/product/{id}`).
   - Fungsi utama: Me-load gambar tambahan (`product_images`) dan varian (`product_variants`) agar frontend bisa menampilkan opsi tombol warna dan ukuran.

## Controller Transaksi (User)
4. **`CartController.php`**
   - Menangani logika *Add to Cart*, pembaruan *quantity*, dan hapus keranjang.
   - PENTING: Melakukan pengecekan ketat terhadap `product_variants.stock` secara *real-time*.
5. **`CheckoutController.php`**
   - Controller paling krusial dalam sistem.
   - Fungsi utama: Memulai transaksi database, melakukan *Pessimistic Locking* pada produk yang dibeli, memotong stok, membuat `Order`, memanggil API RajaOngkir, dan memanggil `MidtransService` untuk menghasilkan `snap_token`.
6. **`OrderController.php`**
   - Fungsi utama: Menampilkan riwayat pesanan (history) dan halaman status pesanan bagi *user*.
7. **`MidtransController.php`**
   - Fungsi utama: Menangani *Webhook/Callback* dari server Midtrans. Mengubah status `payments` dari `pending` menjadi `paid` secara otomatis di latar belakang tanpa interaksi pengguna.

## Controller AI & Layanan Pelanggan
8. **`ChatController.php`**
   - Sistem *Front-Facing* untuk chatbot pengunjung.
   - Fungsi utama: Menerima teks dari pelanggan, menginjeksi informasi ketersediaan stok (`Prompt Engineering`), memanggil Google Gemini API, dan merender teks / ID Produk kembali ke Frontend.
9. **`ReviewController.php`**
   - Menangani penyerahan ulasan dari pengguna, memvalidasi apakah mereka pernah membeli (`status = delivered`), dan menyimpannya.

## Controller Admin (Control Room)
Terletak di folder `app/Http/Controllers/Admin/`:
10. **`Admin/ChatController.php`**
    - Berbeda dengan ChatController publik. File ini digunakan admin untuk *memantau* (Live Monitoring).
    - Fungsi utama: Memiliki method `toggleBot()` untuk mematikan/menghidupkan Gemini AI, dan `reply()` untuk melakukan *Takeover* manual.
11. **`Admin/DashboardController.php`**
    - Menyediakan statistik: Jumlah pesanan bulan ini, laba, pengunjung, produk paling laku (Best Seller).
12. **`Admin/ProductController.php`**
    - Manajemen CRUD Produk: Mengunggah foto produk, membuat varian baru, menambah stok dari supplier, atau men-set status `is_hype`.
13. **`Admin/OrderController.php`**
    - Admin menggunakan ini untuk mengemas barang, menginput nomor resi, dan mengupdate pesanan menjadi `shipped`.
14. **`Admin/CouponController.php`**
    - Membuat kupon diskon baru (tipe persen/statis).
15. **`Admin/UserController.php`**
    - Manajemen staf. Bisa mempromosikan user biasa menjadi admin.

## Controller Owner (Pemilik Modal)
16. **`Owner/OwnerDashboardController.php`**
    - Controller eksklusif untuk tingkat pemilik (`owner`). 
    - Fungsi utama: Melakukan kalkulasi keuntungan bersih dan mengajukan `Withdrawal` (pencairan dana/gajian).
