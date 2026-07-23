# 13. Arsitektur Frontend (Tailwind CSS & Alpine.js)

Berbeda dengan proyek Laravel tradisional yang mungkin mengandalkan jQuery atau Vue.js yang berat, HIGH FIVE menggunakan pendekatan **TALL Stack modern** (Tailwind, Alpine, Laravel, Livewire - meski di sini difokuskan pada Alpine.js murni tanpa Livewire). Kombinasi ini menghasilkan *website* yang secepat kilat (*blazing fast*) karena tidak ada beban memori dari *Virtual DOM*.

## A. Desain Sistem (Tailwind CSS)

Konfigurasi antarmuka diatur secara terpusat di `tailwind.config.js`.

### 1. Palet Warna Kustom
- **`primary`**: Skema warna abu-abu ke hitam (Monokromatik). Digunakan untuk teks utama, garis batas (*borders*), dan elemen UI kasual. `primary-950` adalah hitam pekat.
- **`onyx`**: Palet warna khusus untuk **Mode Gelap (Dark Mode)**. Menghasilkan warna abu-abu gelap kebiruan yang elegan (`#121212` hingga `#0a0a0a`), menghindari penggunaan warna `#000000` mentah yang bisa membuat mata sakit di ruang gelap.
- **`accent`**: Warna penegasan (*highlight*).

### 2. Tipografi
- Memaksa penggunaan *font* **Inter** (`font-sans`) untuk kesan modern, bersih, dan *legible* (mudah dibaca) ala *streetwear brand* masa kini.

### 3. Micro-Animations (Animasi Mikro)
- Tailwind dikonfigurasi untuk memiliki *keyframes* khusus seperti `fade-in` dan `fade-up`. Ini digunakan secara ekstensif saat me-render *Product Card* atau saat bot Gemini membalas pesan, sehingga elemen seolah-olah "muncul melayang" dari bawah.

---

## B. State Management (Alpine.js)

Alpine.js disematkan langsung di dalam *tag* HTML (atribut `x-data`, `x-show`, `x-bind`). Ini membuat logika interaktif sangat dekat dengan elemen visualnya.

### 1. Sistem Chatbot AI (`chatbot-fab.blade.php`)
Komponen ini adalah keajaiban *frontend* yang sebenarnya.
- **Variabel State (`x-data`)**: Menyimpan status `open` (apakah jendela chat terbuka?), array `messages` (riwayat obrolan), dan `replyingTo` (konteks pesan yang sedang dibalas).
- **Auto-Polling (Penyegaran Otomatis)**: Saat jendela chat dibuka (`this.$watch('open')`), Alpine.js akan menjalankan `setInterval` setiap 3 detik (`3000ms`) untuk me-*fetch* (menarik) data pesan terbaru dari `ChatController`. Ini membuat chat terasa statis *real-time* meski tanpa teknologi WebSockets (Pusher/Socket.io) yang mahal.
- **Pengiriman Asynchronous**: Metode `sendMessage()` menggunakan `fetch API` (AJAX) murni tanpa perlu me-*reload* halaman. Pesan pengguna langsung di-*push* secara lokal ke dalam array `messages` agar muncul secara instan di UI, sementara permintaan POST dikirim ke *background*.
- **Pendeteksi Waktu Sapaan**: Fungsi `init()` membaca waktu lokal perangkat pengguna (`new Date().getHours()`) untuk menyapa dengan "Selamat Pagi", "Siang", atau "Malam" secara akurat.

### 2. Logika Halaman Detail Produk (`product-detail.blade.php`)
Halaman ini sangat reaktif, sepenuhnya dikendalikan Alpine.js:
- **Image Lightbox & Slider**: Diatur melalui state `activeImage`. Transisi gambar disetel dengan `x-transition` untuk menghasilkan efek *fade* mulus tanpa perlu pustaka *slider* eksternal seperti Swiper.js.
- **Pemilihan Varian**: Menggunakan variabel `selectedVariant`. Setiap kali tombol radio warna/ukuran diklik, Alpine mencatat ID varian tersebut. Jika stok varian adalah 0, maka tombol Add to Cart langsung di- *disable* secara reaktif (`:disabled="!selectedVariant"`).
- **Sinkronisasi Add to Cart Tanpa Reload**: Saat form Add to Cart di-*submit*, metode `submitCart(e)` mencegat *request* normal (`e.preventDefault()`). Ia mengirim data ke *backend* via `fetch()`. Jika sukses, ia akan **mem-parsing ulang HTML halaman tersebut di latar belakang**, mengekstrak elemen `cart-count-badge` (angka keranjang di navbar), dan memperbaruinya secara *live* tanpa menggeser posisi layar (*scroll*) pengguna sama sekali!
- **Sticky Buy Bar**: Menggunakan fungsi `@scroll.window`, sistem mendeteksi jika jendela di-*scroll* melebihi 600 pixel. Jika ya, sebuah panel "Add to Cart" akan melayang statis (*sticky bar*) di bagian bawah layar HP/Monitor pengguna agar mereka tidak perlu memutar balik ke atas untuk membeli.

### 3. Flash Sale Countdown Timer
Alih-alih menggunakan *script* eksternal, komponen waktu mundur berjalan mandiri.
Sistem mengambil data waktu dari *database* (`flash_sale_end`), kemudian fungsi `setInterval` di Alpine.js membandingkannya dengan waktu saat ini (`new Date().getTime()`), membaginya ke dalam variabel `days`, `hours`, `minutes`, dan `seconds`, lalu me-rendernya ke layar setiap detiknya. Jika waktu habis, harga akan otomatis kembali normal berkat Accessor di *backend*, namun secara *frontend*, *banner* promonya akan langsung disembunyikan menggunakan `x-show="distance > 0"`.
