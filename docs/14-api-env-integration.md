# 14. Panduan Ekstensif API Eksternal & Konfigurasi Lingkungan (.env)

Aplikasi HIGH FIVE tidak berdiri sendiri; ia sangat bergantung pada 3 API eksternal utama: **Google Gemini (Kecerdasan Buatan)**, **Midtrans (Pembayaran)**, dan **RajaOngkir (Logistik)**. 

Dokumen ini membedah konfigurasi `.env` yang diwajibkan dan bentuk *request HTTP* spesifik yang dilakukan oleh sistem di belakang layar.

---

## 1. Google Gemini AI API (Chatbot Engine)

AI Chatbot adalah fitur paling kompleks di sistem ini. Pengaturannya murni mengandalkan variabel *environment* dan API resmi dari Google.

### A. Konfigurasi `.env` yang Dibutuhkan
Anda wajib menambahkan baris ini secara manual di dalam file `.env` proyek Anda (karena tidak ada di `.env.example` bawaan):
```env
GEMINI_API_KEY="AIzaSy...kunci-rahasia-google-anda"
```

### B. Spesifikasi Panggilan API (Deep Dive)
Panggilan dilakukan di `app/Http/Controllers/ChatController.php` pada *line* 112 menggunakan *facade* `Http` bawaan Laravel.

- **URL Endpoint:** `https://generativelanguage.googleapis.com/v1beta/models/gemma-4-26b-a4b-it:generateContent`
- **Model yang Digunakan:** `gemma-4-26b-a4b-it`. Ini adalah model *open-weights* terbaru dari keluarga Gemma yang dioptimalkan untuk instruksi teks dan kecepatan (*low-latency*), sangat cocok untuk *chatbot e-commerce*.
- **Parameter URL:** `?key={GEMINI_API_KEY}`
- **Headers & Konfigurasi Paksa:**
  Sistem memaksa AI untuk *selalu* membalas dengan format JSON yang ketat untuk mencegah *error parsing* di Frontend.
  ```php
  'generationConfig' => [
      'responseMimeType' => 'application/json'
  ]
  ```
- **Body Request (Payload):**
  Menggunakan struktur *prompt* standar Gemini API.
  ```json
  {
    "contents": [
      {
        "parts": [
          {"text": "[PROMPT RAHASIA BERISI INSTRUKSI DAN DATA STOK]"}
        ]
      }
    ]
  }
  ```
- **Penanganan Error (*Error Handling*):** Jika request *timeout* (dibatasi 60 detik) atau HTTP Code bukan 200 OK, kode di dalam blok `catch` akan memicu penulisan `Log::error()` ke `storage/logs/laravel.log` dan UI otomatis beralih ke *Rule-Based Bot*.

---

## 2. Midtrans API (Payment Gateway)

Sistem pembayaran *Cashless* (Transfer Bank, GoPay, QRIS, Kartu Kredit).

### A. Konfigurasi `.env`
Di dalam `.env.example` sudah disiapkan kerangkanya. Anda harus mendaftar di *Midtrans Dashboard* untuk mendapatkan kuncinya.
```env
MIDTRANS_SERVER_KEY="SB-Mid-server-xxxxxxxxx"  # Digunakan Backend untuk generate token
MIDTRANS_CLIENT_KEY="SB-Mid-client-xxxxxxxxx"  # (Opsional) Digunakan Frontend untuk Snap.js
MIDTRANS_IS_PRODUCTION=false                   # Ubah ke true jika sudah rilis resmi (Live)
```

### B. Spesifikasi Panggilan API (Deep Dive)
Di `app/Services/MidtransService.php`, kita menggunakan paket resmi `Midtrans/Snap`.
- **Parameter Penting (Payload):**
  - `transaction_details`: Wajib berisi `order_id` (contoh: `ORD-AB12XY`) dan `gross_amount` (Total pembayaran dalam Integer riil, misal 250000).
  - `customer_details`: Diambil dari `auth()->user()`, memuat nama, email, dan nomor HP agar pembeli otomatis dikenali oleh UI Midtrans.
  - `item_details`: Rincian barang. Sangat vital untuk keamanan. Total harga di `item_details` **wajib sama persis** dengan `gross_amount`, jika tidak API Midtrans akan mengembalikan *error 400 Bad Request*. Oleh karena itu, *Ongkos Kirim* (Shipping) dimasukkan sebagai satu *item* terpisah.
- **Keamanan:** `Config::$is3ds = true;` diaktifkan untuk mewajibkan verifikasi OTP/SMS saat pengguna membayar pakai Kartu Kredit (Mencegah pencurian *Carding*).

---

## 3. RajaOngkir API (Sistem Logistik)

Untuk menghitung ongkos kirim JNE, POS, TIKI secara *real-time* dan akurat berdasarkan kecamatan dan berat pakaian.

### A. Konfigurasi `.env`
Tambahkan baris berikut di `.env`:
```env
RAJAONGKIR_API_KEY="kunci-api-rajaongkir-anda"
RAJAONGKIR_BASE_URL="https://api.rajaongkir.com/starter" # atau /basic atau /pro tergantung tipe akun
RAJAONGKIR_ORIGIN_CITY=501                               # ID Kota asal pengiriman toko Anda (contoh 501 = Yogyakarta)
```

### B. Spesifikasi Panggilan API (Deep Dive)
Berada di `CheckoutController.php` (Line 157).
- **Endpoint:** `POST {RAJAONGKIR_BASE_URL}/cost`
- **Header Khusus:**
  API ini membutuhkan Header `key`, bukan Bearer token standar.
  ```php
  \Illuminate\Support\Facades\Http::withHeaders([
      'key' => env('RAJAONGKIR_API_KEY')
  ])
  ```
- **Body Request:**
  - `origin`: Mengambil `RAJAONGKIR_ORIGIN_CITY` dari .env.
  - `destination`: Mengambil dari pilihan `shipping_city_id` saat pengguna memilih kota.
  - `weight`: Akumulasi kolom `weight` dari tabel `products` dikalikan `quantity`. Jika kosong, menggunakan standar aman `1000` gram (1 Kg).
  - `courier`: JNE, POS, atau TIKI (ditulis huruf kecil).
- **Fallback Mechanism:**
  Dilengkapi dengan blok `try-catch`. Jika koneksi ke server RajaOngkir mati terputus atau API Key *expired*, aplikasi tidak akan hancur (Error 500), melainkan menggunakan ongkos kirim statis (*mock cost*) sebesar Rp 25.000 atau Rp 45.000 agar pembeli tetap bisa menyelesaikan pembayarannya tanpa terhambat masalah teknis.
