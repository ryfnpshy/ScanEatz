# Runbook ScanEatz

Panduan lengkap untuk menjalankan aplikasi ScanEatz di komputer lokal (Development) dan produksi.

## Prasyarat System
- PHP 8.2+ (Extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_mysql, bcmath, intl, gd, exif, iconv, fileinfo)
- Composer
- Node.js & NPM
- MySQL 8.0+ atau PostgreSQL
- Redis (Wajib untuk Cache & Queue)

## Instalasi Lokal (Development)

### 1. Clone & Setup Environment
```bash
git clone https://github.com/username/scaneatz.git
cd scaneatz
cp .env.example .env
```

Edit `.env` dan sesuaikan konfigurasi database:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scaneatz
DB_USERNAME=root
DB_PASSWORD=

# Redis Config
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue & Cache
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

### 2. Install Dependencies
```bash
# Backend
composer install

# Frontend
npm install
```

### 3. Generate Key & Link Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 4. Database Setup & Seeding (Dummy Data)
Jalankan migrasi dan seed data dummy (10 produk, 3 kategori, 2 outlet) yang telah disiapkan.
```bash
php artisan migrate:fresh --seed
```

> **Data Login Dummy:**
> - Admin: `admin@scaneatz.com` / `password`
> - Customer: `customer@example.com` / `password`

### 5. Menjalankan Aplikasi
Anda perlu menjalankan beberapa terminal terpisah:

**Terminal 1 (Laravel Server):**
```bash
php artisan serve
```

**Terminal 2 (Vite Frontend):**
```bash
npm run dev
```

**Terminal 3 (Queue Worker - untuk email/jobs):**
```bash
php artisan queue:work
```

**Terminal 4 (Reverb/WebSocket - untuk real-time tracking):**
```bash
php artisan reverb:start
```

Akses aplikasi di: [http://localhost:8000](http://localhost:8000)

---

## Fitur Utama yang Sudah Siap
1. **Katalog Produk**: Browsing menu dengan filter kategori, pencarian, dan sorting.
2. **Detail Produk**: Varian (level pedas/porsi) dan addons.
3. **Keranjang Belanja**: Add to cart, update qty, remove item.
4. **Checkout**:
   - Pilihan Delivery / Pickup.
   - Deteksi radius pengiriman outlet.
   - Pilihan metode pembayaran (COD/Transfer).
   - Input kupon diskon.
5. **Tracking Order**: Real-time status update dengan timeline visual.
6. **API Endpoints**: `/api/catalog`, `/api/cart`, `/api/checkout`, `/api/tracking/{code}`.

## Test Flow (Skenario Pengujian)

1. **Login sebagai Customer** (`customer@example.com`).
2. Masuk ke halaman **Home** -> Klik "Pesan Sekarang".
3. Pilih Kategori -> Pilih Produk (misal: "Nasi Ayam Geprek").
4. Masuk ke **Keranjang** -> Review item -> "Lanjut ke Pembayaran".
5. Di Halaman **Checkout**:
   - Pilih "Delivery".
   - Pilih Alamat (sudah ada alamat dummy kantor).
   - Pilih Outlet terdekat (otomatis terpilih).
   - Pilih metode bayar (misal: COD).
   - Masukkan Kupon `HEMAT10` (opsional).
   - Klik "Buat Pesanan".
6. Anda akan diarahkan ke halaman **Order Tracking**.
   - Perhatikan status "PENDING".
   - (Simulasi Admin) Buka database/tinker, ubah status order jadi `CONFIRMED`.
   - Halaman tracking akan update otomatis (polling 10s).
