# Sistem ERP Toko Bangunan

Proyek ini merupakan aplikasi berbasis web untuk membantu operasional toko bangunan menggunakan pendekatan ERP (Enterprise Resource Planning). Sistem ini dibuat dalam rangka memenuhi tugas akhir UAS mata kuliah **Corporate Information Systems (CIS)**.

## 📌 Fitur Utama
- Manajemen Data Barang
- Transaksi Penjualan
- Transaksi Pembelian
- Retur Barang
- Laporan Penjualan dan Retur
- Manajemen Data User
- Notifikasi Stok Menipis

## 🛠 Teknologi yang Digunakan
- Laravel 12
- MySQL
- Blade Templating
- Tailwind CSS
- Git & GitHub

## 🔗 Tautan Penting
- **Demo Website**: (https://m-ardian.ftiunwaha.my.id/public/login)
- **Source Code**: (https://github.com/Hanafi-sketch/projek-erp-toko-bangunan)

## 📂 Struktur Folder
- `app/Http/Controllers` – Logika backend
- `resources/views/kasir` – Tampilan antarmuka
- `routes/web.php` – Routing aplikasi
- `public/` – Akses frontend dan file statis

## 🧪 Cara Menjalankan Proyek (Developer)
```bash
git clone https://github.com/Hanafi-sketch/projek-erp-toko-bangunan.git
cd projek-erp-toko-bangunan
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
