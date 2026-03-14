# 🍔 ScanEatz — Premium Food Ordering Experience

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4e56d8?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![AlpineJS](https://img.shields.io/badge/AlpineJS-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

**ScanEatz** adalah platform pemesanan makanan modern yang dirancang untuk memberikan pengalaman kuliner terbaik tanpa hambatan. Dibangun dengan estetika *premium-first*, aplikasi ini menawarkan antarmuka yang sangat responsif, visual yang memukau, dan sistem manajemen pesanan yang tangguh menggunakan **TALL Stack** tercanggih.

---

## ✨ Fitur Unggulan

- **🚀 Seamless Ordering**: Pengalaman berbelanja tanpa reload halaman (SPA feel) berkat integrasi penuh Livewire 3.
- **🛒 Dynamic Cart & Customization**: Mendukung varian (level pedas, topping) dan tambahan (add-ons) yang fleksibel untuk setiap menu.
- **📍 Multi-Outlet Smart System**: Pengelompokan menu berdasarkan outlet dengan kalkulasi biaya pengiriman otomatis.
- **📊 Real-time Tracking**: Pantau perjalanan makanan Anda mulai dari dapur hingga ke tangan Anda dengan status update instan.
- **💎 High-End UI/UX**: Desain modern dengan efek *Glassmorphism*, mikro-animasi halus, dan tipografi elegan.
- **🛡️ Enterprise Architecture**: Menggunakan *Service-Action Pattern* untuk logika bisnis yang bersih, teruji, dan mudah dikelola.

---

## 🛠️ Tech Stack & Architecture

Proyek ini dibangun menggunakan standar pengembangan perangkat lunak modern:

- **Backend**: [Laravel 12 (latest)](https://laravel.com) dengan PHP 8.2+ core.
- **Frontend Interaction**: [Livewire 3](https://livewire.laravel.com) & [Alpine.js](https://alpinejs.dev) untuk reaktivitas tinggi.
- **Styling**: [Tailwind CSS 4.0](https://tailwindcss.com) (integrasi Vite Engine).
- **Architecture**:
  - **Service-Action Pattern**: Memisahkan logika bisnis dari Controller untuk skalabilitas tinggi.
  - **Eager Loading Optimization**: Bebas dari masalah query N+1 untuk performa database maksimal.
  - **Secure Headers**: Proteksi bawaan terhadap serangan web umum (XSS, CSRF, CSP).

---

## 📸 Tampilan Aplikasi

<div align="center">
  <img src="project 3 (1).png" width="32%" alt="Home Page" />
  <img src="project 3 (2).png" width="32%" alt="Menu Catalog" />
  <img src="project 3 (3).png" width="32%" alt="Cart System" />
</div>

---

## 🚀 Panduan Instalasi Cepat

Ikuti langkah-langkah di bawah untuk menjalankan **ScanEatz** di lingkungan lokal Anda:

### 1. Persiapan Awal
Pastikan Anda memiliki [PHP 8.2+](https://php.net), [Composer](https://getcomposer.org), dan [Node.js](https://nodejs.org) terinstal.

```bash
# Clone repository
git clone https://github.com/username/scaneatz.git
cd scaneatz
```

### 2. Instalasi Dependensi
```bash
# Instal PHP packages
composer install

# Instal Node modules
npm install
```

### 3. Konfigurasi Lingkungan
```bash
# Salin environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 4. Setup Database & Demo Data
ScanEatz menyertakan data demo berkualitas tinggi (kategori, produk unggulan, dan gambar dari Unsplash).
```bash
# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed
```

### 5. Jalankan Aplikasi
Gunakan perintah `concurrent` untuk menjalankan server PHP dan Vite secara bersamaan:
```bash
npm run dev
```
Aplikasi akan tersedia di [http://localhost:8000](http://localhost:8000).

---

## 🧪 Pengujian & Kontrol Kualitas

Proyek ini menjamin kualitas kode dengan:
- **Feature Tests**: Menjamin alur kritis (Checkout & Payment) berfungsi sempurna.
- **Pint & Static Analysis**: Kode yang bersih dan mengikuti standar PSR-12.

```bash
# Menjalankan test suite
php artisan test
```

---

## 📄 Lisensi

Proyek ini bersifat open-source di bawah lisensi [MIT](LICENSE).

---

<p align="center">
  Dikembangkan dengan dedikasi oleh <b>Muhammad Rayfan Pashya</b>.
</p>
