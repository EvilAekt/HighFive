# 7. Alur Checkout & Manajemen Stok (Checkout Flow)

Krusial bagi sebuah *e-commerce* adalah kehandalan saat memproses transaksi (jangan sampai terjadi *Overselling* dimana stok tinggal 1 tapi dibeli 2 orang bersamaan). Aplikasi HIGH FIVE memiliki arsitektur *checkout* yang sangat kokoh.

Penjelasan teknis ini didasarkan pada logika di `CheckoutController.php`.

## A. Kalkulasi Keranjang & Diskon
Saat pengunjung membuka halaman `/checkout` (`CheckoutController@index`):
1. **Perhitungan Subtotal:** Harga `current_price` produk dikali `quantity`.
2. **Kupon:** Sistem mengecek `session('applied_coupon')`. Jika ada, sistem akan memvalidasi *Coupon Model* (Apakah valid? Apakah mencapai `min_purchase`? Apakah tipe diskonnya `fixed` atau `percentage`?).
3. **Subsidi Ongkir:** Jika subtotal pesanan menembus Rp 500.000, biaya ongkir di-*hardcode* menjadi Rp 0 (Gratis Ongkir), jika tidak maka akan dihitung.

## B. Proses Penyimpanan Pesanan (`CheckoutController@store`)
Ini adalah fase *critical path* saat tombol "Bayar" diklik:

1. **Database Transaction (Atomicity):**
   Seluruh proses di bawah dibungkus dalam `DB::beginTransaction()`. Artinya, jika sistem gagal/error di tengah jalan, tidak akan ada perubahan data (stok tidak jadi dikurangi, pesanan tidak jadi dibuat) – di- *Rollback* secara aman.

2. **Pessimistic Locking (Mencegah Race Conditions):**
   ```php
   $lockedVariants = \App\Models\ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get();
   ```
   Sistem mengunci (*lock*) baris varian produk di tabel database (menggunakan klausa `FOR UPDATE` di SQL). Ini mencegah pembeli B membeli barang saat stok sedang dihitung oleh pembeli A di sepersekian milidetik yang sama.

3. **Validasi Stok Riil:**
   Sistem melakukan verifikasi lapis kedua. Jika `lockedVariant->stock < cart->quantity`, *checkout* langsung digagalkan dan di- *Rollback*.

4. **Integrasi Eksternal (API):**
   - **RajaOngkir:** Mengirim request HTTP POST ke `api.rajaongkir.com/starter/cost` membawa berat total keranjang dan kota tujuan. Biaya akurat dimasukkan ke *invoice*.
   - **Midtrans:** Membuat pesanan (`Order::create`) lalu meminta `snapToken` melalui `MidtransService`.

5. **Pengurangan Stok (*Decrement*):**
   Setelah semua API eksternal sukses, stok fisik benar-benar dikurangi: `$lockedVariant->decrement('stock', $cart->quantity);`

6. **Pembersihan (Cleanup):**
   Keranjang (`Cart`) milik *user* tersebut dikosongkan. Sesi kupon dihapus. `DB::commit()` dijalankan. Pengguna otomatis dialihkan ke halaman pelacakan pesanan dengan *pop-up* Midtrans terbuka.
