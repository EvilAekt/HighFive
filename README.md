<div align="center">

<img src="https://placehold.co/1000x300/000000/FFFFFF.png?text=HIGH+FIVE+OFFICIAL" alt="HIGH FIVE Banner" width="100%">

# ⚡ HIGH FIVE ⚡
**Masa Depan Fashion Commerce Ada di Sini.**

[![Laravel](https://img.shields.io/badge/Laravel-11-Black?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Alpine](https://img.shields.io/badge/Alpine.js-Black?style=for-the-badge&logo=alpine.js)](https://alpinejs.dev)
[![Tailwind](https://img.shields.io/badge/Tailwind-CSS-Black?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![Gemini](https://img.shields.io/badge/Powered_by-Gemini_AI-Black?style=for-the-badge&logo=google)](https://ai.google.dev/)

*Bukan sekadar pakaian. Ini tentang gaya hidup.*

</div>

---

> **HIGH FIVE** mendefinisikan ulang pengalaman belanja *online*. Kami membangun platform *e-commerce* bergaya modern yang terintegrasi secara mulus dengan asisten AI masa depan. Belanja *streetwear* bukan lagi sekadar transaksi; ini adalah sebuah pengalaman interaktif.

## 🔥 The Vibe (Fitur Utama)

### 🧠 Gemini-Powered Sales Assistant
Lupakan *chatbot* kaku yang membosankan. AI kami bertindak layaknya *fashion stylist* pribadimu langsung di dalam *website*.
- **Rekomendasi Visual Pintar:** Tanya apa yang sedang tren, dan bot ini tidak hanya menjawab dengan teks—tapi langsung menampilkan **Kartu Produk** interaktif di dalam *chat*.
- **Paham Konteks:** AI ini hafal luar dalam soal inventaris, harga, dan rilis terbaru kita. Tanya soal warna atau ukuran, dan dia akan mengecek *database* secara *real-time*.
- **Unified Cross-Device Session:** Logika pintar kami menggabungkan obrolan pelanggan (meski berpindah dari HP ke Laptop) ke dalam satu riwayat utuh jika mereka sudah *login*.

### 🛍️ The Storefront (Etalase Belanja)
- **Katalog Eksklusif:** Halaman belanja yang sangat cepat, dinamis, dan memanjakan mata, dibangun dengan estetika Tailwind CSS.
- **Pengalaman Cart yang Mulus:** Mulai dari memilih varian (warna/ukuran) hingga *checkout*, setiap interaksi terasa sangat lancar (berkat Alpine.js).
- **Hype Drops:** Sistem hitung mundur (*countdown*) bawaan untuk peluncuran koleksi eksklusif atau *Flash Sale*.

### 🕶️ Control Room (Dashboard Admin)
- **Live Takeover:** Pantau obrolan pelanggan secara *real-time*. Kalau AI butuh bantuan, admin manusia bisa langsung mengambil alih obrolan kapan saja.
- **Satu Tombol Sakti:** Ganti mode operasional *website* sesuka hati—dari Gemini AI yang super cerdas, kembali ke *Rule-Based Bot* biasa, atau masuk ke mode Manual sepenuhnya. Semuanya cuma butuh satu klik dan langsung tersimpan di *Cache*.

---

## 🏗️ Under the Hood (Di Balik Layar)

Sistem kami dibangun di atas arsitektur *backend* yang solid dan terstruktur rapi.

### Arsitektur AI Chatbot Pipeline
```mermaid
graph LR
    User([Pelanggan]) -->|Ketik Pesan| Frontend(Chat UI)
    Frontend -->|POST Request| Backend{Engine Controller}
    
    Backend -->|Gemini Aktif| AI[Inject Data Stok + Prompt ke Google API]
    AI -->|Output JSON| Parser[Data Extractor]
    Parser -->|Ada ID Produk| UI1[Tampilkan Kartu Produk Visual]
    Parser -->|Teks Saja| UI2[Tampilkan Chat Biasa]
    Parser -->|Luar Konteks| Mute["Abaikan (Anti-Spam)"]
    
    Backend -->|Rule Bot Aktif| Rules[Keyword Matcher]
    Rules -->|Sesuai Keyword| UI2
```

### Alur Checkout & Validasi Stok
```mermaid
graph TD
    Cart([Keranjang Belanja]) -->|Klik Checkout| Auth{Cek Login}
    Auth -->|Belum Login| Login[Halaman Login]
    Auth -->|Sudah Login| StockCheck{Cek Ketersediaan Stok}
    
    StockCheck -->|Stok Habis| Error[Tampilkan Error di Keranjang]
    StockCheck -->|Stok Tersedia| Lock[Kunci Stok Sementara]
    
    Lock --> Payment[Proses Pembayaran]
    Payment -->|Berhasil| CreateOrder[Buat Pesanan & Kurangi Stok Permanen]
    Payment -->|Gagal/Batal| Release[Lepas Kunci Stok]
    
    CreateOrder --> Success([Halaman Sukses])
```

### Sistem Manajemen Inventaris (Admin)
```mermaid
graph LR
    Admin([Admin]) -->|Input Form| Dashboard[Control Room]
    Dashboard -->|Unggah Foto| Upload[Validasi Ukuran & Tipe File]
    Upload -->|Sukses| SaveDB[Simpan ke Database]
    
    Dashboard -->|Set Status| Toggle{Produk Aktif?}
    Toggle -->|Ya| Storefront[Tampil di Halaman Depan]
    Toggle -->|Tidak| Hidden[Disembunyikan dari Pembeli]
    
    Dashboard -->|Hapus Produk| DeleteCheck{Ada Riwayat Pesanan?}
    DeleteCheck -->|Ya| Reject[Tolak Hapus, Sarankan Inactive]
    DeleteCheck -->|Tidak| Delete[Hapus Permanen]
```

### Alur Pemrosesan Pesanan (Order Fulfillment)
```mermaid
graph TD
    NewOrder([Pesanan Baru Masuk]) --> AdminPanel[Admin Order Dashboard]
    AdminPanel --> Review{Tinjau Status Pembayaran}
    
    Review -->|Belum Dibayar| Pending[Status: Pending]
    Review -->|Sudah Dibayar| Process[Status: Processing]
    
    Process --> Pack[Siapkan & Packing Barang]
    Pack --> Ship[Input Resi Pengiriman]
    Ship -->|Update Database| Shipped[Status: Shipped]
    
    Shipped --> Delivered([Pesanan Selesai])
```

### Logika "Hype Drops" & Flash Sale
```mermaid
graph LR
    User([Pengunjung]) --> Home[Halaman Utama]
    Home --> Check{Cek Waktu Promo}
    
    Check -->|Sedang Berlangsung| Show[Tampilkan Banner Merah & Hitung Mundur]
    Check -->|Waktu Habis| Hide[Otomatis Sembunyikan Promo]
    
    Show --> ClickProduct[Klik Produk Flash Sale]
    ClickProduct --> VerifyPrice{Verifikasi Harga di Server}
    
    VerifyPrice -->|Masih Promo| Discount[Gunakan Harga Diskon]
    VerifyPrice -->|Waktu Habis| Normal[Kembalikan ke Harga Normal]
```

### Sistem Autentikasi & Hak Akses (Role-Based Access)
```mermaid
graph TD
    Visitor([Pengunjung]) --> Login[Login / Register]
    Login --> Verify{Verifikasi Kredensial}
    
    Verify -->|Gagal| Error[Pesan Error]
    Verify -->|Berhasil| CheckRole{Cek Role User}
    
    CheckRole -->|Role: User| RedirectHome[Arahkan ke Halaman Utama]
    CheckRole -->|Role: Admin| RedirectDash[Arahkan ke Control Room Dashboard]
    
    RedirectDash --> Middleware{Admin Middleware}
    Middleware -->|Akses Ditolak| Abort[Error 403 / Redirect]
    Middleware -->|Akses Diberikan| AdminArea[Akses Penuh Fitur Toko]
```

### Sinkronisasi Keranjang Belanja (Smart Cart)
```mermaid
graph LR
    User([Pelanggan]) --> AddToCart[Klik 'Tambah ke Keranjang']
    AddToCart --> AuthCheck{Cek Status Login}
    
    AuthCheck -->|Guest| LocalStorage["Simpan di LocalStorage (Browser)"]
    AuthCheck -->|Logged In| Database[Simpan ke Database Keranjang]
    
    LocalStorage --> Login[Saat Pelanggan Login]
    Login --> Sync[Sinkronisasi Keranjang]
    Sync --> Merge[Gabungkan LocalStorage dengan Database]
```

### Customer Service Routing (Live Takeover)
```mermaid
graph TD
    Message([Pesan Pelanggan]) --> ChatController[Server Chat]
    ChatController --> CheckStatus{Status Chatbot?}
    
    CheckStatus -->|AI Aktif| Gemini[Kirim ke Gemini AI]
    CheckStatus -->|Rule Bot Aktif| Keyword[Proses Keyword Manual]
    CheckStatus -->|Mode Manual| Wait[Tunggu Balasan Admin]
    
    Gemini --> SendBack[Kirim Balasan Otomatis ke User]
    
    Admin([Admin]) --> Monitor[Pantau Live Chat]
    Monitor --> Takeover[Klik 'Ambil Alih Obrolan']
    Takeover --> SetManual[Ubah Status Menjadi 'Mode Manual']
    SetManual --> Reply[Kirim Pesan Langsung ke Pelanggan]
```

### Sistem Loyalitas: Ulasan & Wishlist
```mermaid
graph LR
    Product([Halaman Produk]) --> Action{Aksi Pengguna}
    
    Action -->|Klik Hati| WishlistCheck{Sudah Login?}
    WishlistCheck -->|Belum| ToLogin[Redirect Login]
    WishlistCheck -->|Sudah| SaveWish[Simpan ke Wishlist Database]
    
    Action -->|Tulis Ulasan| OrderCheck{Pernah Beli?}
    OrderCheck -->|Belum| RejectReview[Tolak Ulasan]
    OrderCheck -->|Sudah & Selesai| SaveReview[Simpan Rating & Ulasan]
    SaveReview --> Calc[Hitung Ulang Rata-Rata Rating Produk]
```

### Struktur Database Utama
| Tabel Model | Fungsi & Peran |
| :--- | :--- |
| `User` | Mengatur otentikasi profil pelanggan dan hak akses privilese Admin. |
| `Product` | Katalog utama. Menyimpan nama koleksi, harga, deskripsi, thumbnail, dan *status hype/flash sale*. |
| `ProductVariant` | Detail spesifik produk. Mengatur ketersediaan warna, ukuran, dan melacak manajemen stok (*inventory*). |
| `Message` | Menyimpan seluruh riwayat *Live Chat*. Menggunakan arsitektur `reply_to_id` untuk menghubungkan *thread* percakapan. |
| `Order` & `OrderItem` | Rekam jejak transaksi pengguna, alamat pengiriman, dan rincian keranjang belanja yang telah di-*checkout*. |

### 🎮 Arsitektur Controller (Dokumentasi Pengembang)

<details>
<summary><strong>1. ChatController (Front-Facing API)</strong></summary>

Lokasi: `app/Http/Controllers/ChatController.php`
- **Tugas Utama:** Mengelola penerimaan pesan pelanggan dari *Frontend* (Alpine.js).
- **Integrasi AI:** Menarik data produk `is_active` dan jumlah *sold items* dari database, mem- *parsing* nya menjadi teks, lalu merangkainya ke dalam **Prompt Engineering** rahasia yang dikirim ke Google Gemini. 
- **Response Parsing:** Menerjemahkan respons JSON AI menjadi data yang bisa dirender menjadi *Product Card* (Kartu Produk) di UI klien.
</details>

<details>
<summary><strong>2. Admin\ChatController (Control Room)</strong></summary>

Lokasi: `app/Http/Controllers/Admin/ChatController.php`
- **Manajemen Sesi:** Secara cerdas melakukan *Group By Session* untuk menampilkan daftar pelanggan yang sedang *online*. Menarik nama pengguna (*User Name*) secara akurat meski pesan terakhir dikirimkan oleh sistem AI.
- **Toggle Cache Engine:** Memodifikasi status `ai_active` dan `bot_active` secara *global* menggunakan Redis/File Cache tanpa perlu melakukan perpindahan halaman (*reload*).
</details>

<details>
<summary><strong>3. CheckoutController & ProductController</strong></summary>

- Mengelola validasi keranjang belanja, memastikan ketersediaan stok fisik `ProductVariant` sebelum memproses pesanan, serta mengembalikan data *catalog* secara dinamis ke halaman *storefront*.
</details>

---

## 🛡️ Keamanan & Validasi (Security)

Kami tidak mengorbankan keamanan demi estetika. Platform ini dilengkapi dengan pengamanan setingkat standar industri:

1. **Anti-Prompt Injection (AI Security):**
   Model Gemini dibatasi oleh *Prompt Engineering* yang sangat ketat (instruksi berlapis). AI diwajibkan untuk **membisu (mengembalikan string kosong)** jika pelanggan mencoba membahas hal di luar pakaian, komplain di luar nalar, atau mencoba memanipulasi bot.
2. **CSRF Protection:**
   Setiap pertukaran data (termasuk *fetch request* dari *Live Chat*) diproteksi penuh dengan token `@csrf` bawaan Laravel untuk mencegah serangan *Cross-Site Request Forgery*.
3. **Data Sanitization & XSS Prevention:**
   Semua input pengguna (seperti pesan obrolan) dibatasi panjang karakternya (maksimal 1000 karakter via `$request->validate()`) dan di-*escape* secara otomatis oleh *Blade engine* untuk mencegah serangan skrip silang (*Cross-Site Scripting*).
4. **Role-Based Authorization:**
   Seluruh area *Control Room* / *Dashboard* dilindungi oleh *middleware* khusus sehingga hanya akun dengan hak akses `admin` yang bisa mengubah pengaturan AI atau membaca pesan masuk pelanggan.

---

## 🚀 Cara Menjalankan (Instalasi Lokal)

Ingin me-*running* sistem keren ini di komputermu? Ikuti langkah berikut:

**1. Clone Source Code**
```bash
git clone https://github.com/username/highfive.git
cd highfive/laravel
```

**2. Install Dependencies**
```bash
composer install
npm install && npm run build
```

**3. Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate
```
*Buka file `.env` dan atur koneksi `DB_` kamu. Sangat Penting: Masukkan **API Key Google Gemini** kamu di variabel `GEMINI_API_KEY=`.*

**4. Bangun Database**
```bash
php artisan migrate --seed
php artisan storage:link
```

**5. Launching**
```bash
php artisan serve
```
*Buka `http://localhost:8000` di browsermu dan nikmati pengalamannya.*

---

<div align="center">
  <p><strong>Stay hype. Stay stylish. Stay secure.</strong></p>
  <p>Dibuat dengan semangat penuh oleh <strong>Tim HIGH FIVE</strong>.</p>
</div>
