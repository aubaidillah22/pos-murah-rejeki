# 🏪 POS Murah Rejeki — Aplikasi Kasir Toko Bangunan

**Point of Sale (POS)** berbasis web untuk **Toko Bangunan Murah Rejeki**, dibangun dengan Laravel 11, Livewire 4, dan Tailwind CSS. Mendukung multi-outlet, multi-role, dan manajemen inventaris lengkap.

---

## 📋 Daftar Isi

- [Fitur Lengkap](#fitur-lengkap)
  - [1. POS / Kasir](#1-pos--kasir)
  - [2. Dashboard](#2-dashboard)
  - [3. Manajemen Produk](#3-manajemen-produk)
  - [4. Manajemen Kategori](#4-manajemen-kategori)
  - [5. Manajemen Satuan](#5-manajemen-satuan)
  - [6. Manajemen Pelanggan](#6-manajemen-pelanggan)
  - [7. Manajemen Supplier](#7-manajemen-supplier)
  - [8. Pembelian (Purchase Order)](#8-pembelian-purchase-order)
  - [9. Transaksi](#9-transaksi)
  - [10. Stok Opname](#10-stok-opname)
  - [11. Pengeluaran](#11-pengeluaran)
  - [12. Laporan](#12-laporan)
  - [13. Manajemen Pengguna (Admin)](#13-manajemen-pengguna-admin)
  - [14. Manajemen Outlet (Admin)](#14-manajemen-outlet-admin)
  - [15. Pengaturan Toko (Admin)](#15-pengaturan-toko-admin)
- [Teknologi](#teknologi)
- [Arsitektur](#arsitektur)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Akun Default](#akun-default)
- [Role & Permission](#role--permission)
- [Struktur Database](#struktur-database)
- [API Endpoints](#api-endpoints)
- [Panduan Kontribusi](#panduan-kontribusi)
- [Lisensi](#lisensi)

---

## Fitur Lengkap

### 1. POS / Kasir

Antarmuka kasir yang cepat dan responsif untuk memproses transaksi penjualan.

| Fitur | Detail |
|-------|--------|
| 🔍 **Pencarian Produk** | Cari produk berdasarkan nama, SKU, atau barcode. Deteksi stok habis otomatis. |
| 🏷️ **Kategori Cepat** | Filter berdasarkan kategori dengan tombol cepat. |
| 🛒 **Keranjang Belanja** | Tambah/hapus item, ubah kuantitas dengan tombol +/-. Validasi stok real-time. |
| 👤 **Pilih Pelanggan** | Pilih pelanggan dari daftar atau buat pelanggan baru langsung dari POS. |
| 💰 **Diskon & Pajak** | Input diskon nominal dan pajak (PPN) fleksibel. |
| 💳 **Multi Metode Pembayaran** | Tunai, QRIS, Transfer Bank, Debit. |
| 💵 **Hitung Kembalian** | Otomatis hitung kembalian berdasarkan jumlah dibayar. |
| 📋 **Piutang (Due)** | Jika pembayaran kurang dari total, otomatis dijadikan piutang. |
| 🧾 **Cetak Struk** | Tampilan struk setelah transaksi, siap cetak. |
| 🆕 **Transaksi Baru** | Reset keranjang untuk transaksi berikutnya tanpa reload halaman. |
| 📉 **Notifikasi Stok** | Peringatan jika stok produk menipis setelah transaksi. |

### 2. Dashboard

Halaman utama dengan ringkasan data real-time dan grafik.

| Fitur | Detail |
|-------|--------|
| 💵 **Kartu Statistik** | Penjualan hari ini, jumlah transaksi, total produk aktif, total pelanggan. |
| 📈 **Grafik Penjualan Bulanan** | Grafik area (ApexCharts) menampilkan tren penjualan per bulan sepanjang tahun. |
| 🏆 **Produk Terlaris** | Top 5 produk dengan penjualan tertinggi hari ini. |
| ⚠️ **Notifikasi Stok Menipis** | Daftar produk dengan stok di bawah batas minimum, dilengkapi link ke halaman pembelian. |
| 🕒 **Real-time** | Data diperbarui setiap kali halaman dimuat. |

### 3. Manajemen Produk

CRUD lengkap untuk produk dengan fitur import/export.

| Fitur | Detail |
|-------|--------|
| ➕ **Tambah Produk** | Form lengkap: nama, SKU, barcode, kategori, satuan, harga beli, harga jual, stok, min stok alert, deskripsi, status aktif. |
| ✏️ **Edit Produk** | Update semua field produk. |
| 🗑️ **Hapus Produk** | Konfirmasi sebelum hapus. |
| 🔍 **Cari & Filter** | Pencarian real-time berdasarkan nama, SKU, barcode. Filter berdasarkan kategori. |
| 🔄 **Sortir** | Klik header kolom untuk mengurutkan (asc/desc). |
| ⬇️ **Export Excel** | Ekspor semua produk ke file `.xlsx` dengan FastExcel. |
| ⬆️ **Import Excel** | Import massal produk dari file `.xlsx`, `.xls`, `.csv`. Update jika SKU sudah ada. |
| 📸 **Upload Gambar** | Upload foto produk (max 1MB). |
| 📊 **Pagination** | 15 produk per halaman. |
| 🔴 **Indikator Stok** | Warna merah jika stok ≤ min_stock_alert, hijau jika aman. |

### 4. Manajemen Kategori

Atur kategori produk.

| Fitur | Detail |
|-------|--------|
| ➕**Tambah Kategori** | Nama dan deskripsi. |
| ✏️ **Edit Kategori** | Update nama dan deskripsi. |
| 🔄 **Toggle Aktif/Nonaktif** | Nonaktifkan kategori tanpa menghapus (produk tetap aman). |
| 🏷️ **Unique Name** | Validasi nama kategori unik. |

### 5. Manajemen Satuan

Atur satuan unit produk.

| Fitur | Detail |
|-------|--------|
| ➕ **Tambah Satuan** | Nama satuan (Sak, Kg, Batang, Lembar, dll). |
| ✏️ **Edit Satuan** | Update nama satuan. |
| 🗑️ **Hapus Satuan** | Hapus satuan yang tidak digunakan. |

### 6. Manajemen Pelanggan

Kelola data pelanggan.

| Fitur | Detail |
|-------|--------|
| ➕ **Tambah Pelanggan** | Nama, telepon, email, alamat, status member. |
| ✏️ **Edit Pelanggan** | Update semua data. |
| 🔍 **Pencarian** | Cari berdasarkan nama atau telepon. |
| ⭐ **Status Member** | Tandai pelanggan sebagai member. |
| 💰 **Total Piutang** | Hitung otomatis total hutang pelanggan (from payment_status 'due'). |

### 7. Manajemen Supplier

Kelola data pemasok barang.

| Fitur | Detail |
|-------|--------|
| ➕ **Tambah Supplier** | Nama perusahaan, kontak person, telepon, email, alamat. |
| ✏️ **Edit Supplier** | Update semua data. |
| 🔍 **Pencarian** | Cari berdasarkan nama atau telepon. |
| 📋 **Riwayat PO** | Lihat semua purchase order dari supplier (via relasi). |

### 8. Pembelian (Purchase Order)

Manajemen pembelian barang dari supplier.

| Fitur | Detail |
|-------|--------|
| ➕ **Buat PO Baru** | Pilih supplier, cari & tambahkan produk, atur kuantitas. |
| 🧮 **Total Otomatis** | Hitung total amount dari semua item. |
| 📄 **Invoice Number** | Generate otomatis format `PO-YYYYMMDD-XXXX`. |
| ✅ **Terima Barang** | Konfirmasi penerimaan → stok produk otomatis bertambah. |
| 📊 **Status Tracking** | Draft → Ordered → Received. |
| 👤 **Pencatat** | Tercatat siapa yang membuat PO. |
| 🔄 **Stok Update** | Saat barang diterima, stok produk langsung di-increment. |

### 9. Transaksi

Riwayat dan detail seluruh transaksi penjualan.

| Fitur | Detail |
|-------|--------|
| 🔍 **Pencarian** | Cari berdasarkan nomor invoice. |
| 📅 **Filter Tanggal** | Filter dari/tanggal. |
| 💳 **Filter Pembayaran** | Filter metode pembayaran (Cash/QRIS/Transfer/Debit). |
| 📊 **Filter Status** | Filter status pembayaran (Lunas/Piutang). |
| 👁️ **Detail Transaksi** | Modal detail: item, harga, diskon, pajak, grand total, metode bayar. |
| 📋 **Informasi Lengkap** | Invoice, tanggal, pelanggan, kasir, grand total, status. |
| 💰 **Status Piutang** | Tampilan berbeda untuk transaksi lunas vs piutang. |

### 10. Stok Opname

Penyesuaian stok fisik.

| Fitur | Detail |
|-------|--------|
| ➕ **Opname Baru** | Cari produk, input stok aktual (fisik). |
| 📊 **Selisih Otomatis** | Hitung otomatis selisih antara stok sistem vs aktual. |
| 🏷️ **Tipe Penyesuaian** | Surplus (stok lebih), Shortage (stok kurang), Koreksi. |
| 📝 **Catatan** | Input alasan penyesuaian (barang rusak, hilang, dll). |
| 🔄 **Update Stok** | Stok produk langsung diperbarui sesuai stok aktual. |
| 📋 **Riwayat Opname** | Lihat semua opname dengan detail lengkap. |
| 👤 **Petugas** | Tercatat siapa yang melakukan opname. |
| 🔍 **Filter** | Filter berdasarkan tipe (surplus/shortage/koreksi). |
| 🆔 **No. Opname** | Generate otomatis format `SO-YYYYMMDD-XXXX`. |

### 11. Pengeluaran

Catat pengeluaran operasional toko.

| Fitur | Detail |
|-------|--------|
| ➕ **Catat Pengeluaran** | Deskripsi, jumlah, tanggal, kategori. |
| 📂 **Kategori** | Listrik, Air, Gaji, Transport, Sewa, ATK, Lainnya. |
| 💰 **Total Pengeluaran** | Footer otomatis menjumlah total. |
| 🔄 **Arus Kas** | Setiap pengeluaran otomatis tercatat di arus kas. |

### 12. Laporan

Laporan lengkap dengan export PDF.

| Tab | Fitur |
|-----|-------|
| 📊 **Penjualan** | Tabel laporan penjualan dengan filter tanggal. Total, diskon, grand total. **Export PDF** |
| 💹 **Laba/Rugi** | Total penjualan, HPP (Harga Pokok Penjualan), laba kotor, total pengeluaran, laba bersih. **Export PDF** |
| 📦 **Stok** | Laporan stok semua produk: nama, kategori, satuan, stok, min stok, status (Aman/Menipis). |
| 💵 **Arus Kas** | Semua pemasukan dan pengeluaran dengan tipe dan jumlah. |
| 💸 **Pengeluaran** | Laporan pengeluaran dengan filter kategori. |

### 13. Manajemen Pengguna (Admin)

Manajemen user dan role.

| Fitur | Detail |
|-------|--------|
| 👥 **Daftar Pengguna** | Semua user dengan informasi nama, email, outlet, role. |
| 🔄 **Status Aktif** | Tampilan status aktif/nonaktif. |
| 🎭 **Role Badge** | Menampilkan semua role yang dimiliki user. |
| 🔒 **Route Protection** | Hanya role Admin yang bisa akses. |

### 14. Manajemen Outlet (Admin)

Manajemen cabang toko.

| Fitur | Detail |
|-------|--------|
| 🏢 **Daftar Outlet** | Nama, alamat, telepon, status aktif. |
| 🔒 **Route Protection** | Hanya role Admin yang bisa akses. |

### 15. Pengaturan Toko (Admin)

Halaman pengaturan toko (read-only).

| Fitur | Detail |
|-------|--------|
| 🏪 **Nama Toko** | Dari konfigurasi `APP_NAME`. |
| 💰 **Pajak Default** | PPN default 11%. |
| 💵 **Mata Uang** | IDR (Rupiah). |
| 🕐 **Timezone** | Asia/Jakarta (WIB). |

---

## Teknologi

| Stack | Teknologi | Versi |
|-------|-----------|-------|
| **Backend** | Laravel | 11.x |
| **Language** | PHP | ^8.2 |
| **Database** | MySQL / MariaDB / SQLite | - |
| **Frontend** | Livewire | ^4.3 |
| **CSS Framework** | Tailwind CSS | ^3.4 |
| **Auth & RBAC** | spatie/laravel-permission | ^6.25 |
| **Activity Log** | spatie/laravel-activitylog | ^4.12 |
| **Excel Export/Import** | Rap2hpoutre/FastExcel | ^5.9 |
| **PDF Export** | Barryvdh/laravel-dompdf | ^3.1 |
| **Charts** | ApexCharts | ^5.15 |
| **Build Tool** | Vite | ^6.0 |
| **JS Package** | Axios | ^1.7 |

---

## Arsitektur

```
app/
├── Http/
│   └── Controllers/
│       ├── AuthController.php      # Login/Logout
│       └── Controller.php          # Base Controller
├── Livewire/                       # Livewire Components
│   ├── Pos/Index.php               # POS / Kasir
│   ├── Product/ProductList.php     # Manajemen produk
│   ├── Category/CategoryList.php   # Manajemen kategori
│   ├── Unit/UnitList.php           # Manajemen satuan
│   ├── Customer/CustomerList.php   # Manajemen pelanggan
│   ├── Supplier/SupplierList.php   # Manajemen supplier
│   ├── Purchase/PurchaseList.php   # Purchase Order
│   ├── Transaction/TransactionList.php  # Riwayat transaksi
│   ├── Report/ReportIndex.php      # Laporan
│   ├── Expense/ExpenseList.php     # Pengeluaran
│   ├── Dashboard/Index.php         # Dashboard
│   └── StockOpnameList.php         # Stok opname
├── Models/                         # Eloquent Models (14 models)
├── Repositories/                   # Repository Pattern
│   ├── BaseRepository.php
│   ├── ProductRepository.php
│   ├── TransactionRepository.php
│   └── Contracts/                  # Interfaces
├── Services/                       # Business Logic
│   ├── POSService.php              # Proses transaksi POS
│   └── ReportService.php           # Generate laporan
├── Providers/
│   └── AppServiceProvider.php
database/
├── migrations/                     # 18 migration files
└── seeders/                        # 9 seeders
resources/
├── css/app.css                     # Tailwind CSS
├── js/                             # JavaScript (Bootstrap, Axios)
└── views/
    ├── auth/login.blade.php        # Halaman login
    ├── layouts/app.blade.php       # Layout utama + sidebar
    ├── livewire/                   # 14+ Blade views
    └── exports/                    # PDF export templates
routes/
└── web.php                         # Web routes
```

### Pola Arsitektur

- **Repository Pattern**: ABstraction layer untuk database queries (ProductRepository, TransactionRepository)
- **Service Layer**: Business logic terpusat (POSService, ReportService)
- **Livewire Components**: Stateful components untuk UI interaktif
- **RBAC**: Permission-based access control dengan Spatie
- **Activity Log**: Audit trail untuk semua operasi penting

---

## Persyaratan Sistem

- **PHP** 8.2+
- **Composer** 2.x
- **Database** MySQL 8.0 / MariaDB 10.4+ (atau SQLite untuk development)
- **Node.js** 18+ & NPM
- **PHP Extensions**: PDO, mbstring, xml, gd, zip, bcmath, json, openssl, tokenizer

---

## Instalasi

### 1. Clone & Install Dependencies

```bash
git clone <repository-url> pos-murah-rejeki
cd pos-murah-rejeki

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Build assets
npm run build
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:

```env
APP_NAME="Murah Rejeki - POS Toko Bangunan"
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_murah_rejeki
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Buat Database & Jalankan Migrasi

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE pos_murah_rejeki CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# Link storage untuk upload gambar
php artisan storage:link
```

### 4. Jalankan Aplikasi (Development)

```bash
# Option 1: PHP built-in server
php artisan serve

# Option 2: Full stack (server + queue + logs + Vite)
composer run dev
```

Akses di browser: **http://localhost:8000**

---

## Akun Default

Setelah menjalankan seeder, akun berikut tersedia:

| Role | Email | Password |
|------|-------|----------|
| 👑 **Admin** | admin@murahrejeki.com | password |
| 👔 **Manager** | manager@murahrejeki.com | password |
| 🏪 **Kasir 1** | kasir1@murahrejeki.com | password |
| 🏪 **Kasir 2** | kasir2@murahrejeki.com | password |

---

## Role & Permission

### Roles (3 roles)

| Role | Deskripsi |
|------|-----------|
| **Admin** | Akses penuh ke semua fitur termasuk manajemen pengguna, outlet, dan pengaturan |
| **Manager** | Manajemen operasional: produk, stok, pembelian, laporan, pelanggan, supplier |
| **Kasir** | Terbatas: transaksi POS, lihat produk, kelola pelanggan |

### Permissions (30+ permissions)

| Grup | Permissions |
|------|-------------|
| **Produk** | `view-products`, `create-products`, `edit-products`, `delete-products`, `import-products`, `export-products` |
| **Kategori** | `view-categories`, `create-categories`, `edit-categories`, `delete-categories` |
| **Satuan** | `view-units`, `create-units`, `edit-units`, `delete-units` |
| **Transaksi** | `create-transactions`, `view-transactions`, `edit-transactions`, `delete-transactions` |
| **Laporan** | `view-reports`, `export-reports` |
| **Pelanggan** | `view-customers`, `create-customers`, `edit-customers`, `delete-customers` |
| **Supplier** | `view-suppliers`, `create-suppliers`, `edit-suppliers`, `delete-suppliers` |
| **Pembelian** | `view-purchase-orders`, `create-purchase-orders`, `edit-purchase-orders`, `receive-purchase-orders` |
| **Pengguna** | `view-users`, `create-users`, `edit-users`, `delete-users` |
| **Outlet** | `view-outlets`, `create-outlets`, `edit-outlets`, `delete-outlets` |
| **Pengaturan** | `manage-settings` |
| **Stok Opname** | `view-stock-opname`, `create-stock-opname` |
| **Pengeluaran** | `view-expenses`, `create-expenses`, `edit-expenses`, `delete-expenses` |
| **Keuangan** | `view-cash-flow`, `view-profit-loss` |

### Route Protection

Route dilindungi dengan kombinasi middleware:
- `auth` — Semua route kecuali login
- `permission:{permission-name}` — Route spesifik permission
- `role:Admin` — Route khusus admin (users, outlets, settings)

---

## Struktur Database

### Entity Relationship

```
outlets (1) ──── (N) users
outlets (1) ──── (N) products
outlets (1) ──── (N) categories
outlets (1) ──── (N) transactions
outlets (1) ──── (N) purchase_orders
outlets (1) ──── (N) expenses
outlets (1) ──── (N) stock_opnames

categories (1) ──── (N) products
units (1) ──── (N) products

suppliers (1) ──── (N) purchase_orders
purchase_orders (1) ──── (N) purchase_order_details
purchase_order_details (N) ──── (1) products

customers (1) ──── (N) transactions
users (1) ──── (N) transactions
transactions (1) ──── (N) transaction_details
transaction_details (N) ──── (1) products

stock_opnames (N) ──── (1) products
```

### 14 Tables

| Table | Keterangan |
|-------|------------|
| `users` | Pengguna sistem (outlet_id, is_active) |
| `outlets` | Cabang toko |
| `categories` | Kategori produk (outlet_id, is_active) |
| `units` | Satuan produk |
| `products` | Produk (SKU, harga, stok, kategori, satuan, outlet) |
| `suppliers` | Pemasok barang |
| `purchase_orders` | Purchase order dari supplier (status: draft/ordered/received) |
| `purchase_order_details` | Detail item PO |
| `customers` | Pelanggan (is_member) |
| `transactions` | Transaksi penjualan (metode bayar, status bayar, pajak, diskon) |
| `transaction_details` | Detail item transaksi |
| `expenses` | Pengeluaran operasional (kategori) |
| `cash_flows` | Arus kas (income/expense) |
| `stock_opnames` | Stok opname (tipe: surplus/shortage/correction) |

### Migration Files (18 files)

- `0001_01_01_000000_create_users_table.php` — Users, password_reset_tokens, sessions
- `0001_01_01_000001_create_cache_table.php` — Cache
- `0001_01_01_000002_create_jobs_table.php` — Jobs queue
- `2026_06_22_000001` s/d `000013` — Tabel bisnis utama
- `2026_06_22_163634` — Spatie Permission tables
- `2026_06_22_163635` s/d `163637` — Spatie Activitylog tables

---

## Fitur per Role (Ringkasan)

| Fitur | Admin | Manager | Kasir |
|-------|:-----:|:-------:|:-----:|
| Dashboard | ✅ | ✅ | ✅ |
| POS / Kasir | ✅ | ✅ | ✅ |
| Produk (CRUD) | ✅ | ✅ | ❌ |
| Produk (Lihat) | ✅ | ✅ | ✅ |
| Produk (Import/Export) | ✅ | ✅ | ❌ |
| Kategori | ✅ | ✅ | ❌ |
| Satuan | ✅ | ✅ | ❌ |
| Pelanggan | ✅ | ✅ | ✅ (create) |
| Supplier | ✅ | ✅ | ❌ |
| Purchase Order | ✅ | ✅ | ❌ |
| Transaksi (Lihat) | ✅ | ✅ | ✅ |
| Laporan | ✅ | ✅ | ❌ |
| Stok Opname | ✅ | ✅ | ❌ |
| Pengeluaran | ✅ | ✅ | ❌ |
| Manajemen Pengguna | ✅ | ❌ | ❌ |
| Manajemen Outlet | ✅ | ❌ | ❌ |
| Pengaturan | ✅ | ❌ | ❌ |

---

## Pengembangan

### Commands

```bash
composer run dev       # Jalankan full development stack
npm run build          # Build production assets
npm run dev            # Build development assets + watch
php artisan serve      # PHP development server
```

### Menambahkan Livewire Component Baru

```bash
php artisan make:livewire NamaComponent
```

---

## Lisensi

Hak Cipta © 2026 **Murah Rejeki**. All rights reserved.

---

> Dibangun dengan ❤️ menggunakan Laravel, Livewire, dan Tailwind CSS.
