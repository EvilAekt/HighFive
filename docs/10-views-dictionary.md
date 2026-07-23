# 10. Panduan Tampilan (Views / UI)

Sistem *frontend* dibangun dengan *Blade Template Engine* dan *Tailwind CSS*. Struktur antarmuka di `resources/views/` dipisahkan secara sangat rapi berbasis *Component-Driven*.

## A. Direktori `resources/views/components/`
Merupakan potongan-potongan UI yang dapat digunakan kembali (*reusable components*).
- **`chatbot-fab.blade.php`**: Komponen tombol obrolan melayang (Floating Action Button) di sudut layar. Di dalamnya mengandung *script* Alpine.js untuk membuka antarmuka *chat* dan melakukan sinkronisasi dengan `ChatController`.
- **`product-card.blade.php`**: Komponen kotak produk (menampilkan foto, nama, harga). Digunakan berulang-ulang di halaman Katalog dan Beranda.
- **`navbar.blade.php` & `footer.blade.php`**: Header dan navigasi bawah standar.
- **`flash-message.blade.php`**: Kotak *alert* warna hijau (Sukses) atau merah (Error) yang muncul di atas layar untuk memberitahu pengguna jika *checkout* gagal atau stok habis.

## B. Direktori `resources/views/pages/`
Merupakan halaman-halaman utama bagi pembeli.
- **`home.blade.php`**: Antarmuka beranda depan. Memuat hero-banner dan deretan produk unggulan (Best Sellers/Hype Drops).
- **`catalog.blade.php`**: Halaman pencarian lengkap. Sering menggunakan Alpine.js untuk menyembunyikan/menampilkan filter (sidebar).
- **`product-detail.blade.php`**: Halaman untuk melihat informasi satu produk secara utuh. Logika Alpine.js di sini akan merubah harga atau peringatan 'Habis' ketika pelanggan mengklik opsi warna atau ukuran spesifik.
- **`cart.blade.php`**: Tabel yang berisi ringkasan keranjang.
- **`checkout.blade.php`**: Menggabungkan form isi alamat, pemilihan kurir, form kupon, dan Ringkasan Tagihan yang terhubung dengan Midtrans Snap UI.

## C. Direktori `resources/views/admin/`
Merupakan *dashboard* untuk operasional toko (Control Room). Tampilannya lebih padat data dan bersifat analitik.
- **`dashboard.blade.php`**: Halaman utama admin. Menampilkan grafik penjualan dan ringkasan pesanan baru.
- **`products.blade.php` & `products-create.blade.php`**: Tabel daftar seluruh produk, dan *form* pengisian produk baru beserta penambahan varian/stok.
- **`orders.blade.php`**: Halaman untuk mengupdate status resi kurir.
- **`chat-takeover.blade.php`**: UI khusus *Live Monitoring* admin untuk memantau pengunjung yang sedang berbalas pesan dengan Gemini AI.

## D. Direktori `resources/views/layouts/`
Kerangka utama HTML (File Master).
- **`app.blade.php`**: Master layout untuk bagian depan (pelanggan). Biasanya akan memuat `<head>`, mengimpor skrip Vite (Tailwind), memanggil *navbar*, memuat `@yield('content')`, lalu menutupnya dengan *footer* dan *chatbot-fab*.
- **`admin.blade.php`**: Master layout khusus untuk Control Room, biasanya berbeda karena membutuhkan *Sidebar* navigasi sebelah kiri dan warna desain yang lebih fungsional.
