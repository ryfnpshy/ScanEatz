# ScanEatz - Setup Guide

Panduan instalasi dan menjalankan ScanEatz Food Ordering Platform secara lokal.

## Prerequisites

### 1. Install PHP 8.2+

**Download PHP untuk Windows:**
1. Kunjungi https://windows.php.net/download/
2. Download **PHP 8.2+ (Thread Safe)** versi x64
3. Extract ke `C:\php`
4. Tambahkan `C:\php` ke System PATH:
   - Klik kanan "This PC" → Properties → Advanced system settings
   - Environment Variables → System variables → Path → Edit → New
   - Tambahkan: `C:\php`
   - Klik OK semua dialog

**Konfigurasi PHP:**
```powershell
cd C:\php
copy php.ini-development php.ini
```

Edit `C:\php\php.ini`, aktifkan ekstensi (hapus `;` di depan):
```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=redis
extension=zip
```

**Verifikasi:**
```powershell
php --version
# Output: PHP 8.2.x ...
```

### 2. Install Composer

**Download:**
1. Kunjungi https://getcomposer.org/download/
2. Download dan jalankan `Composer-Setup.exe`
3. Ikuti wizard instalasi (otomatis mendeteksi PHP)

**Verifikasi:**
```powershell
composer --version
# Output: Composer version 2.x.x
```

### 3. Install MySQL

**Option A - XAMPP (Recommended untuk Windows):**
1. Download XAMPP dari https://www.apachefriends.org/
2. Install dan jalankan MySQL dari Control Panel
3. Default credentials: `root` / `<no password>`

**Option B - MySQL Standalone:**
1. Download dari https://dev.mysql.com/downloads/mysql/
2. Install dan set password untuk user `root`
3. Update `.env` dengan password MySQL Anda

### 4. Install Redis (Optional tapi direkomendasikan)

**Download Redis untuk Windows:**
1. Download dari https://github.com/microsoftarchive/redis/releases
2. Download `Redis-x64-3.0.504.msi`
3. Install dengan default settings
4. Redis akan jalan sebagai Windows Service

**Verifikasi:**
```powershell
redis-cli ping
# Output: PONG
```

**Alternatif (jika Redis tidak wajib untuk development):**
Edit `.env` dan ubah:
```env
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### 5. Install Node.js & npm

**Download:**
1. Kunjungi https://nodejs.org/
2. Download versi LTS (Long Term Support)
3. Install dengan default settings

**Verifikasi:**
```powershell
node --version
npm --version
```

---

## Installation Steps

### 1. Install Dependencies

```powershell
# Navigate to project
cd C:\Users\rayfa\Documents\ScanEatz

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Generate Application Key

```powershell
php artisan key:generate
```

### 3. Setup Database

**Buat database MySQL:**
```sql
CREATE DATABASE scaneatz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Atau via MySQL Command Line:**
```powershell
mysql -u root -p
# Enter password (kosong jika XAMPP)

CREATE DATABASE scaneatz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Run Migrations & Seed Data

```powershell
# Run migrations
php artisan migrate

# Seed dummy data (outlets, products, categories, coupons)
php artisan db:seed
```

### 5. Install Livewire & Filament

```powershell
# Publish Livewire assets
php artisan livewire:publish --assets

# Install Filament Admin Panel
php artisan filament:install --panels

# Create admin user
php artisan make:filament-user
# Email: admin@scaneatz.com
# Password: (pilih password Anda)
```

### 6. Build Frontend Assets

```powershell
# Development (dengan hot reload)
npm run dev

# Production build
npm run build
```

---

## Running the Application

### Development Mode (Recommended)

**Terminal 1 - Laravel Server:**
```powershell
php artisan serve
# Server berjalan di: http://localhost:8000
```

**Terminal 2 - Queue Worker:**
```powershell
php artisan queue:work
```

**Terminal 3 - Vite Dev Server:**
```powershell
npm run dev
```

**Terminal 4 - Laravel Reverb (WebSocket untuk real-time):**
```powershell
php artisan reverb:start
```

### Production Mode

```powershell
# Build assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run with production server (e.g., Apache/Nginx)
```

---

## Quick Start (Semua dalam satu command)

Jika semua dependencies sudah terinstall:

```powershell
# Clone atau ensure you're in project directory
cd C:\Users\rayfa\Documents\ScanEatz

# Setup lengkap
composer setup

# Run development server (semua services sekaligus)
composer dev
```

Buka browser: **http://localhost:8000**

---

## Access Points

- **Customer Web App**: http://localhost:8000
- **Admin Panel (Filament)**: http://localhost:8000/admin
- **API Endpoint**: http://localhost:8000/api
- **Health Check**: http://localhost:8000/healthz

---

## Default Credentials (Seeder)

### Admin User
- Email: `admin@scaneatz.com`
- Password: `password` (ubah di production!)

### Test Customer
- Email: `customer@example.com`
- Password: `password`

### Sample Outlets
- **Gajah Mada Food Street - Pusat** (otl-1)
- **Gajah Mada Food Street - Senen** (otl-2)

### Sample Coupons
- `HEMAT10`: 10% off, min. IDR 50,000, max discount IDR 20,000

---

## Troubleshooting

### "Class not found" errors
```powershell
composer dump-autoload
```

### Migration errors
```powershell
# Fresh start (WARNING: deletes all data)
php artisan migrate:fresh --seed
```

### Cache issues
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission errors (storage/logs)
```powershell
# Windows: pastikan folder writable
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

### Redis connection failed
Jika Redis tidak tersedia, update `.env`:
```env
QUEUE_CONNECTION=database
CACHE_STORE=file
```

---

## Next Steps

1. ✅ Install prerequisites (PHP, Composer, MySQL, Node.js)
2. ✅ Run setup commands
3. 📖 Baca dokumentasi API di `/docs` (coming soon)
4. 🎨 Customize design system di `tailwind.config.js`
5. 🛒 Test checkout flow di browser
6. 📱 Test responsive design (mobile/tablet)
7. 🔐 Setup payment gateway (production)

---

## Support

Jika menemui masalah:
1. Check log Laravel: `storage/logs/laravel.log`
2. Check browser console untuk JS errors
3. Pastikan semua services running (MySQL, Redis, queue worker)

**Selamat coding! 🚀**
