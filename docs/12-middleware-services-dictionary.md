# 12. Kamus Sistem Keamanan (Middleware), Layanan (Services), & Helpers

Tingkat detail terdalam di arsitektur aplikasi ini ada pada bagaimana sistem menjaga rute dari penyusup (*Middleware*), berintegrasi dengan pihak ketiga (*Services*), dan memiliki alat bantu global (*Helpers*).

## A. Layanan Pihak Ketiga (`app/Services/`)

### `MidtransService.php`
Kelas independen (bukan bawaan Laravel) yang murni bertugas berkomunikasi dengan Server Midtrans (Payment Gateway).
- **Fungsi Konstruktor (`__construct`)**: 
  Mengatur `Config::$serverKey`, `Config::$isProduction`, dan memaksa opsi 3D Secure (`Config::$is3ds = true`) demi keamanan transaksi kartu kredit.
- **Metode `createSnapToken($order, $user, $items)`**:
  - Menyusun *array* `item_details` secara persis (memasukkan satu per satu produk keranjang dan menambahkan 'Ongkos Kirim' sebagai satu *item* terpisah agar ditagih Midtrans).
  - Menyusun *array* `customer_details`.
  - Mendaftarkan *callback URLs* (`finish`, `error`, `unfinish`) agar setelah Pop-Up Midtrans ditutup, pengguna diarahkan kembali ke halaman struk HIGH FIVE (`route('orders.show')`).
  - Mengeksekusi permintaan HTTP ke *backend* Midtrans: `Snap::getSnapToken($params)`. Mengembalikan String unik (token).

## B. Pelindung Rute / RBAC (`app/Http/Middleware/`)

*Middleware* bekerja bagai satpam. Sebelum memuat halaman yang diminta pengguna, laravel akan menjalankan skrip *middleware*.

### 1. `AdminMiddleware.php`
- Menghadang semua *request* ke alamat `/admin/*`.
- **Logika:** Jika pengguna belum login `!auth()->check()`, langsung tendang (*redirect*) ke `/login`. Jika pengguna sudah login tetapi *role*-nya bukan admin (`auth()->user()->role !== 'admin'`), tolak akses dengan kode error `403 Unauthorized`.

### 2. `OwnerMiddleware.php`
- Satpam lapis tertinggi. Menghadang seluruh akses ke `/owner/*`.
- **Logika:** Mengecek apakah peran (`role`) dari pengguna persis sama dengan `owner`. Hanya pemilik ini yang bisa melihat grafik laba bersih toko dan melakukan *Withdrawal* (Menarik dana ke rekening Bank).

## C. Fungsi Pembantu Global (`app/Helpers/helpers.php`)

Karena Laravel mengizinkan injeksi fungsi ke ruang lingkup *Global*, HIGH FIVE memiliki `helpers.php` (yang di- *load* melalui `composer.json` -> `autoload`). Ini berarti fungsi-fungsi ini bisa langsung dipanggil tanpa perlu mendeklarasikan `class` di seluruh sistem (terutama sangat membantu saat *rendering Blade UI*).

1. **`formatPrice($amount)`**
   - Merubah angka integer `150000` menjadi format mata uang rapi standar Indonesia: `Rp 150.000`. Memanfaatkan fungsi asli PHP `number_format()`.
2. **`formatDate($date)`**
   - Merubah format *timestamp database* (`2026-07-25 15:30:00`) menggunakan pustaka `Carbon`. Diubah menjadi bahasa Indonesia berkat `locale('id')` menjadi `25 Juli 2026, 15:30`.
3. **`getStatusColor($status)`**
   - Fungsi luar biasa yang mengembalikan **Class Tailwind CSS**. Jika `$status = 'delivered'`, otomatis mengembalikan warna hijau (`bg-green-100 text-green-800`). Jika `pending`, kuning.
4. **`getStatusLabel($status)`**
   - Mengubah istilah database/Inggris menjadi bahasa Indonesia cantik. (`shipped` menjadi `Dikirim`, `delivered` menjadi `Selesai`).
