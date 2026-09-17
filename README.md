# Trading Journey Tracker

Personal trading journal untuk mencatat dan menganalisis perjalanan trading crypto Anda — dari modal $100, $500, atau berapapun. Track setiap trade, pantau equity curve, dan temukan pola terbaik Anda.

---

## Fitur Lengkap

- **Auth** — Register, Login, Logout, Reset Password (Laravel Breeze)
- **Multi Akun Trading** — Kelola beberapa akun (Binance, Bybit, OKX, dll.) sekaligus
- **Trade Log (CRUD)** — Catat setiap trade dengan detail lengkap (pair, arah, quantity, entry, exit, SL, TP)
- **Auto P/L Calculation** — Kalkulasi otomatis: `(Exit - Entry) × Quantity`
- **Quick Close Position** — Tutup posisi open langsung dari halaman detail trade
- **Dashboard Statistik** per akun:
  - Equity Curve (Line Chart dinamis)
  - Win / Loss Donut Chart
  - Kalender P/L Harian (heatmap)
  - Win Rate, Profit Factor, Net P/L, Average Win/Loss, Expectancy
  - Performa per Pair dan per Strategi
- **Open Positions Alert** — Notifikasi posisi yang masih aktif di dashboard
- **Tags Fleksibel** — Strategi (📐), Emosi/Psikologi (💭), Kesalahan (❌)
- **Screenshot Upload** — Upload chart screenshot per trade (5MB)
- **Filter & Search** — Filter trade berdasarkan pair, status, arah, tag, rentang tanggal
- **Export CSV** — Download seluruh data trade dengan filter aktif
- **Laporan Journey** — Ringkasan bulanan + full trade log yang bisa dicetak/PDF
- **Data Isolation** — Setiap user hanya melihat data milik sendiri (multi-user safe)

---

## Tech Stack

- **Backend:** Laravel 13.x (PHP 8.3)
- **Auth:** Laravel Breeze (Blade)
- **Frontend:** Blade + Tailwind CSS (CDN) + Alpine-ready
- **Chart:** Chart.js 4.4
- **Database:** SQLite (default) atau MySQL/PostgreSQL
- **Font:** Inter + JetBrains Mono

---

## Setup dari Nol

### 1. Clone & Install Dependencies
```bash
git clone https://github.com/brannnrg/tradingjourney.git
cd tradingjourney
composer install
```

### 2. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

File `.env.example` sudah dikonfigurasi untuk SQLite. Edit sesuai kebutuhan:

```env
APP_NAME="Trading Journey Tracker"
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

# SQLite (default, tidak perlu setup tambahan)
DB_CONNECTION=sqlite

# Atau ganti ke MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=trading_journey
# DB_USERNAME=root
# DB_PASSWORD=
```

### 3. Migrasi Database & Seed Tags
```bash
php artisan migrate --seed
```

Seeder akan membuat 20+ tag default (Strategi, Emosi, Kesalahan) + demo user.

### 4. Storage Link (untuk screenshot upload)
```bash
php artisan storage:link
```

### 5. Jalankan Server
```bash
php artisan serve
```

Buka: **http://127.0.0.1:8000**

---

## Demo User

Setelah `migrate --seed`:

| Field | Value |
|-------|-------|
| Email | `test@example.com` |
| Password | `password` |

---

## Kalkulasi P/L Crypto

| Arah | Formula |
|------|---------|
| Long | `(exit_price - entry_price) × quantity` |
| Short | `(entry_price - exit_price) × quantity` |
| P/L % | `(profit_loss / (entry_price × quantity)) × 100` |

---

## Routes

| Route | Method | Deskripsi |
|-------|--------|-----------|
| `/dashboard` | GET | Dashboard utama |
| `/trading-accounts` | CRUD | Manajemen akun trading |
| `/trading-accounts/{id}/report` | GET | Laporan journey lengkap |
| `/trades` | CRUD | Trade log |
| `/trades/export` | GET | Export CSV (filter aktif) |
| `/trades/{id}` | GET | Detail trade + quick close |
| `/trades/{id}/close` | POST | Quick close posisi open |
| `/tags` | GET/POST/DELETE | Manajemen tags |

---

## Struktur Data

```
users
  └── trading_accounts (1 user bisa banyak akun)
        └── trades (1 akun bisa banyak trade)
              └── tags (many-to-many via trade_tag)
```

---

*Built for the $100 challenge — track every trade, learn from every loss.*
