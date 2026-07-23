# 5. Panduan Instalasi dan Deployment

Untuk menjalankan *High Five E-Commerce* secara lokal, ikuti tahapan berikut:

## Prasyarat Sistem
- **PHP** >= 8.2
- **Composer** (Package Manager PHP)
- **Node.js & npm** (Untuk kompilasi aset Frontend Tailwind/Alpine)
- **Database** (MySQL / PostgreSQL / SQLite)

## Langkah Instalasi

1. **Clone Repositori:**
   ```bash
   git clone <url-repo-anda>
   cd highfive/laravel
   ```

2. **Instalasi Dependency Backend:**
   ```bash
   composer install
   ```

3. **Instalasi Dependency Frontend:**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Lingkungan (.env):**
   - Salin file *template* `.env`:
     ```bash
     cp .env.example .env
     ```
   - Hasilkan *App Key*:
     ```bash
     php artisan key:generate
     ```
   - **PENTING**: Konfigurasi koneksi database Anda di bagian `DB_CONNECTION`.
   - **WAJIB**: Masukkan kunci API Google Gemini Anda di `GEMINI_API_KEY=` untuk mengaktifkan AI Chatbot.
   - Atur `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` untuk pembayaran.
   - Atur `RAJAONGKIR_API_KEY` untuk kalkulasi ongkos kirim.

5. **Migrasi Database & Seeding:**
   ```bash
   php artisan migrate --seed
   ```
   *Proses ini akan membuat tabel di database dan mengisi data *dummy* awal (jika tersedia).*

6. **Tautkan Storage:**
   Menautkan penyimpanan gambar produk agar dapat diakses dari Web.
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Web server akan berjalan di `http://localhost:8000`.
