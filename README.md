# 🚀 Trading Journey Tracker — Crypto Edition

Personal journal untuk mencatat dan menganalisis perjalanan trading crypto Anda. Dari modal berapapun, track setiap trade, pantau equity curve, dan analisis performa.

## ✨ Fitur

- **Autentikasi** — Register, Login, Profile (Laravel Breeze)
- **Manajemen Akun Trading** — Dukung multiple akun (Binance, Bybit, OKX, dll.)
- **Trade Log** — Catat setiap transaksi dengan detail lengkap
- **Auto P/L Calculation** — Kalkulasi otomatis: `(Exit - Entry) × Quantity`
- **Dashboard Statistik** per akun:
  - 📈 Equity Curve (Line Chart)
  - 🍩 Win vs Loss (Donut Chart)
  - 📊 Performa per Pair
  - Win Rate, Profit Factor, Net P/L
- **Tags** — Kategori Strategi, Emosi, Kesalahan (many-to-many)
- **Screenshot Upload** — Upload chart screenshot per trade
- **Data Isolation** — Setiap user hanya melihat data miliknya

---

## Tech Stack

- Laravel 11 (PHP 8.3), Laravel Breeze (Blade)
- SQLite, Tailwind CSS (CDN), Chart.js 4

---

## Setup dari Nol

### 1. Install Dependencies
```bash
composer install
```

### 2. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan `.env` menggunakan SQLite:
```env
DB_CONNECTION=sqlite
```

### 3. Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### 4. Migrasi dan Seed
```bash
php artisan migrate --seed
```

### 5. Storage Link
```bash
php artisan storage:link
```

### 6. Jalankan
```bash
php artisan serve
```

Buka: **http://127.0.0.1:8000**

---

## Kalkulasi P/L Crypto

| Arah | Formula |
|------|---------|
| Long | `(exit_price - entry_price) × quantity` |
| Short | `(entry_price - exit_price) × quantity` |
| P/L % | `(profit_loss / (entry_price × quantity)) × 100` |

---

## Demo User

Email: `test@example.com` | Password: `password`
