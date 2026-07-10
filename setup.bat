@echo off
echo ===================================================
echo SETUP LARAVEL PROJECT - HIGH FIVE E-COMMERCE
echo ===================================================

echo 1. Memindahkan file sementara...
mkdir temp_backup
move app temp_backup\
move bootstrap temp_backup\
move database temp_backup\
move resources temp_backup\
move routes temp_backup\
move composer.json temp_backup\
move tailwind.config.js temp_backup\
move postcss.config.js temp_backup\
move vite.config.js temp_backup\

echo 2. Menginstal base Laravel 11...
composer create-project laravel/laravel temp_laravel

echo 3. Menggabungkan file kita ke dalam instalasi Laravel...
xcopy /E /Y temp_backup\* temp_laravel\
move temp_laravel\* .\
move temp_laravel\.* .\
rmdir /S /Q temp_laravel
rmdir /S /Q temp_backup

echo 4. Menginstal dependensi NPM...
call npm install
call npm install -D tailwindcss postcss autoprefixer
call npx tailwindcss init -p

echo 5. Menginstal dependensi Composer (Midtrans, dll)...
call composer require midtrans/midtrans-php

echo 6. Menginstal Laravel Breeze (Otentikasi)...
call composer require laravel/breeze --dev
call php artisan breeze:install blade

echo 7. Menggabungkan ulang routes dan views yang tertimpa Breeze...
:: Perhatian: Proses ini mungkin memerlukan Anda untuk memeriksa routes/web.php lagi jika Breeze menimpanya.

echo Setup selesai!
echo Silakan konfigurasi file .env Anda, lalu jalankan:
echo php artisan migrate:fresh --seed
echo npm run build
echo php artisan serve

pause
