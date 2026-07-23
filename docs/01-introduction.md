# 1. Pendahuluan (Introduction)

## Tentang HIGH FIVE
**HIGH FIVE** adalah sebuah platform *e-commerce* bergaya modern yang difokuskan pada penjualan pakaian *streetwear*. Website ini tidak hanya berfungsi sebagai etalase produk, tetapi juga mengintegrasikan asisten AI (Gemini) yang bertindak sebagai *fashion stylist* interaktif.

## Teknologi yang Digunakan (Tech Stack)
Aplikasi ini dibangun menggunakan arsitektur modern berbasis Laravel:
- **Backend:** Laravel 11 (PHP)
- **Frontend (UI/UX):** Blade Templates, Tailwind CSS (untuk styling responsif dan estetika modern)
- **Frontend (Interaktivitas):** Alpine.js (untuk state management ringan tanpa overhead framework besar)
- **Database:** MySQL / SQLite (Sesuai konfigurasi `.env`, menggunakan Eloquent ORM)
- **AI Engine:** Google Gemini AI API
- **Payment Gateway:** Midtrans (Callback dan Pemrosesan Pembayaran)
- **Shipping API:** RajaOngkir (Perhitungan Ongkos Kirim)

## Struktur Direktori Utama
Berikut adalah struktur direktori penting dalam pengembangan aplikasi ini:
- `app/Http/Controllers`: Menyimpan seluruh logika bisnis dan kontroler aplikasi (Public, User, Admin, Owner).
- `app/Models`: Model Eloquent untuk interaksi dengan tabel database.
- `resources/views`: Tampilan frontend (Blade templates) termasuk komponen UI.
- `routes/web.php`: Pusat definisi rute aplikasi.
- `docs/`: Dokumentasi teknis aplikasi.
