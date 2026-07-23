# 2. Fitur Utama Aplikasi (Mendetail)

Aplikasi HIGH FIVE tidak hanya sekadar toko online konvensional, melainkan sebuah platform interaktif yang dilengkapi berbagai fitur *advanced*. Berikut adalah penjelasan teknis untuk masing-masing fitur.

## A. Sistem Autentikasi & Otorisasi
- **Multi-Role Login:** Sistem membedakan tiga jenis *role* pengguna: `user` (pembeli), `admin` (pengelola harian), dan `owner` (pemilik modal/toko).
- **Socialite Integration:** Mendukung *login* menggunakan akun sosial (tersedia *fields* di *database* untuk *social login*).
- **Middleware Proteksi:**
  - `auth`: Melindungi halaman keranjang, *checkout*, dan pengaturan profil.
  - `admin`: Melindungi rute `/admin/*` untuk manajemen katalog dan pesanan.
  - `owner`: Melindungi rute `/owner/*` khusus untuk pencairan dana (*withdrawals*).

## B. Katalog & Etalase Produk (Storefront)
- **Struktur Varian Fleksibel:** Satu produk induk (`Product`) dapat memiliki banyak varian (`ProductVariant`). Setiap varian dikombinasikan berdasarkan `color` (warna) dan `size` (ukuran), serta memiliki **jumlah stok independen**.
- **Multi-Image Support:** Produk dapat memiliki banyak foto yang disimpan melalui model `ProductImage`.
- **Hype Drops & Flash Sale:** 
  - Produk memiliki status `is_hype` dan `flash_sale`. Jika aktif, *frontend* (dibangun dengan Alpine.js) akan menampilkan efek *countdown* dan *banner* khusus.
  - Harga yang digunakan saat *checkout* adalah `current_price` yang dikalkulasi secara dinamis di level Model/Database jika diskon sedang aktif.

## C. Manajemen Keranjang (Smart Cart)
- **Validasi Real-time:** Saat menambah barang ke keranjang (`CartController@add`), sistem akan langsung memverifikasi ketersediaan stok fisik dari tabel `product_variants`.
- **Pencegahan Overselling:** Jika *user* menambahkan *quantity* melebihi stok yang ada, *backend* akan menolak aksi tersebut dan memberikan pesan peringatan sisa stok.

## D. Control Room (Dashboard Admin)
- **Manajemen Order Komprehensif:** Admin dapat mengubah status pesanan (Pending -> Processing -> Shipped -> Delivered).
- **Chat Takeover:** Admin memiliki *dashboard* khusus `/admin/chat` yang terus tersinkronisasi. Jika AI salah menjawab atau pengunjung butuh bantuan manusia, admin bisa mengirim pesan langsung. Aksi ini dapat disetel untuk mematikan *bot* otomatis (*Live Takeover*).
- **Pengaturan Kode Kupon:** Admin bisa membuat kode promo (`Coupon`) dengan tipe diskon statis (`fixed`) atau persentase (`percentage`), beserta batasan minimum pembelian (`min_purchase`) dan kuota penggunaan.

## E. Profil & Preferensi Pelanggan
- **Buku Alamat (Address Book):** Pelanggan dapat menyimpan banyak alamat pengiriman di menu `Settings`. Salah satu alamat dapat di-set sebagai *Primary Address*.
- **Riwayat Belanja & Ulasan:** Pelanggan bisa melacak paket mereka. Setelah status pesanan `delivered`, pelanggan bisa memberikan ulasan (Rating 1-5 bintang, teks, hingga melampirkan foto/video ulasan).
