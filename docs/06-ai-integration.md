# 6. Integrasi Gemini AI (Deep Dive)

Sistem chatbot pada HIGH FIVE dibangun dengan arsitektur hibrida (*hybrid architecture*) yang menggabungkan Google Gemini AI dan sistem kecerdasan berbasis aturan (*Rule-Based Fallback*). Berikut adalah alur kerjanya berdasarkan source code di `ChatController.php`.

## A. Alur Pemrosesan Pesan (`ChatController@store`)
1. **Identifikasi Sesi:** Sistem membaca `session_id`. Jika pengguna masuk, menggunakan ID pengguna. Jika *guest*, sistem menghasilkan UUID di *session/cookie* agar riwayat obrolan tidak hilang meski di-*refresh*.
2. **Penyimpanan Input:** Pesan pengguna disimpan ke tabel `messages` dengan flag `is_admin = false`.
3. **Pengecekan Engine:** 
   Sistem mengecek status Cache global:
   - `Cache::get('ai_active')`: Apakah AI Gemini sedang dinyalakan Admin?
   - `Cache::get('bot_active')`: Apakah Bot otomatis aktif secara umum?

## B. Dynamic Prompt Engineering (Gemini Injection)
Ini adalah inti kecerdasan AI. Controller tidak hanya meneruskan teks dari pengguna, melainkan "menyuapi" (*inject*) Gemini dengan konteks database *real-time*.

1. **Pengumpulan Data:**
   Controller me-*load* seluruh produk yang aktif beserta seluruh *variant*-nya. Ia juga melakukan *query* ke tabel `order_items` yang berstatus *paid* (lunas) untuk menghitung angka **Terjual** per produk.
2. **Kompilasi Konteks:**
   Sistem merangkai teks format panjang untuk AI. Contoh bentuk injeksi:
   > "ID: 9a2b... | Kaos Oversize (Harga: Rp 150.000 | Terjual: 145 pcs | Rilis: 20 Jul 2026). Varian: Warna Hitam Ukuran L (45 pcs), Warna Putih Ukuran M (0 pcs)."
3. **Instruksi Ketat (System Prompt):**
   AI diikat oleh aturan ketat:
   - Wajib memanggil "kak".
   - Wajib menjawab SANGAT SINGKAT (1-2 kalimat).
   - Dilarang keras melayani obrolan di luar konteks toko pakaian. (Jika ditanya aneh, AI dipaksa diam/mengembalikan string kosong).
   - Wajib memprioritaskan rekomendasi barang dengan status "Terjual" terbanyak (Best Seller) jika pengguna minta saran umum.
4. **Structured Output (JSON Parsing):**
   AI dipaksa (`responseMimeType: application/json`) untuk membalas dengan format pasti:
   ```json
   {"reply": "Tentu kak, Kaos Oversize Hitam ukuran L masih ready 45 pcs ya!", "product_id": "9a2b..."}
   ```
   Jika `product_id` diisi, Alpine.js di bagian Frontend akan langsung meng-API (*fetch*) data katalog produk tersebut dan menampilkannya sebagai tombol belanja interaktif (bukan teks biasa).

## C. Rule-Based Fallback Mechanism
Jika API Google Gemini *down*, *timeout* (> 60 detik), mengembalikan respons kosong akibat proteksi *out-of-context*, atau Cache `ai_active` sengaja dimatikan admin, sistem otomatis turun (*fallback*) ke mode *Rule-Based*.

- *Regex Matcher*: Mencari kata kunci seperti `harga`, `ongkir`, `ready`, atau `stok`.
- Merespons dengan *template* *hardcode*, misal: "Halo kak! Semua produk yang bisa dipilih ukurannya di website artinya READY ya..."

## D. Keamanan Integrasi
- Seluruh *raw response* dari Gemini di-log oleh Laravel `Log::info` dan `Log::error` untuk keperluan audit (mencegah bot diakali oleh *prompt injection* dari pengunjung usil).
