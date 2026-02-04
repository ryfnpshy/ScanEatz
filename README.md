# 🍔 ScanEatz - Premium Food Ordering Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-blue.svg)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**ScanEatz** adalah platform pemesanan makanan modern yang dirancang untuk memberikan pengalaman kuliner terbaik tanpa hambatan. Dibangun dengan teknologi terbaru dari ekosistem Laravel, aplikasi ini menawarkan antarmuka yang sangat responsif, visual yang memukau, dan sistem manajemen yang tangguh.

---

## ✨ Fitur Utama

- **🚀 Reactive Shopping Experience**: Pemesanan makanan tanpa reload halaman menggunakan TALL Stack (Tailwind, Alpine, Laravel, Livewire).
- **🛒 Dynamic Cart System**: Mendukung kustomisasi menu dengan varian (level pedas, ukuran) dan tambahan (addons) yang fleksibel.
- **📍 Multi-Outlet Management**: Sistem cerdas yang mendeteksi outlet terdekat dan menghitung biaya pengiriman otomatis berdasarkan jarak.
- **📊 Real-time Order Tracking**: Lacak status pesanan Anda mulai dari persiapan (cooking) hingga sampai di tangan Anda.
- **💎 Premium Design Aesthetics**: Antarmuka modern dengan efek *glassmorphism*, micro-animations, dan desain kartu produk yang terinspirasi dari standar aplikasi kelas atas.
- **🛡️ Enterprise-Grade Security**: Dilengkapi dengan pengerasan keamanan CSRF, proteksi XSS, Content Security Policy (CSP), dan Automated Testing (Feature Tests).

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Frontend**: [Livewire](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev)
- **Styling**: [Tailwind CSS](https://tailwindcss.com)
- **Database**: PHP Eloquent ORM (MariaDB/MySQL/SQLite)
- **Pattern**: Service-Action Pattern, API Resources
- **Testing**: PHPUnit / Laravel Test Runner

---

## 🚀 Instalasi Cepat

Ikuti langkah-langkah di bawah untuk menjalankan ScanEatz di mesin lokal Anda:

### 1. Clone Repository
```bash
git clone https://github.com/username/scaneatz.git
cd scaneatz
```

### 2. Instal Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database & Seeding
ScanEatz dilengkapi dengan data demo premium (termasuk foto makanan berkualitas tinggi dari Unsplash).
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Aplikasi
Gunakan perintah `concurrent` untuk menjalankan server dan build asset sekaligus:
```bash
npm run dev
```
Buka `http://localhost:8000` di browser Anda.

---

## 📸 Tampilan Aplikasi

![alt text](<project 3 (1).png>) ![alt text](<project 3 (2).png>) ![alt text](<project 3 (3).png>)

---

## 🛡️ Audit & Kualitas Kode

Proyek ini telah melalui tahap audit ketat sebagai **Senior Engineer**:
- **N+1 Logic Resolved**: Optimalisasi query database menggunakan Eager Loading.
- **Harden Security**: Penerapan Middleware keamanan kustom untuk header produksi.
- **Automated Testing**: Menjamin alur kritis (Add to Cart -> Checkout) berjalan sempurna melalui PHPUnit Feature Tests.

---

## 📄 Lisensi

Proyek ini berada di bawah lisensi [MIT](LICENSE).

---

<p align="center">Dibuat dengan ❤️ oleh <b>Muhammad Rayfan Pashya</b>.</p>
