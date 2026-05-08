# UlamSari App

## Deskripsi Singkat
UlamSari App adalah aplikasi kasir dan sistem pemesanan restoran sederhana berbasis Laravel 12. Aplikasi ini mendukung:
- Login awal menggunakan PIN untuk kasir dan dapur
- Kasir membuat pesanan (dine-in / takeaway)
- Pelanggan memesan melalui QR / nomor meja
- Dapur melihat status pesanan dan mengubah status masak
- Admin mengelola menu (tambah, edit, aktif/non-aktif)
- Ekspor laporan order berbayar ke file Excel/XLS

## Teknologi Utama
- PHP 8.2
- Laravel 12
- Vite + Tailwind CSS
- MySQL / SQLite (Laravel default)
- JavaScript + Axios

## Fitur Utama
1. Autentikasi PIN
   - PIN kasir: `111111`
   - PIN dapur: `222222`
   - Blokir sementara setelah 4 kali salah PIN

2. Modul Kasir
   - Melihat daftar menu aktif
   - Memilih meja dan membuat pesanan
   - Menentukan metode pembayaran atau bayar nanti
   - Melihat pesanan pending dan riwayat pembayaran
   - Ekspor laporan order berbayar ke Excel

3. Modul Pelanggan
   - Tampilan pemesanan untuk pelanggan berdasarkan nomor meja
   - Menu aktif ditampilkan untuk pemesanan

4. Modul Dapur
   - Lihat daftar pesanan yang belum selesai
   - Ubah status pesanan dari menunggu -> dimasak -> selesai
   - Filter berdasarkan jenis pesanan (dine-in / take-away)

5. Modul Admin Menu
   - Kelola daftar menu
   - Tambah menu baru
   - Ubah data menu
   - Toggle status aktif / tidak aktif
   - Hapus menu

## Struktur Utama Aplikasi
### Routes
- `/` -> redirect ke halaman login PIN
- `/login-pin` -> tampilan login PIN
- `/kasir` -> halaman kasir
- `/kasir/pesan` -> menyimpan pesanan kasir
- `/kasir/export` -> ekspor laporan order
- `/pesan/{meja}` -> halaman pelanggan untuk memesan
- `/pesan/store` -> simpan pesanan pelanggan
- `/dapur` -> halaman daftar pesanan dapur
- `/dapur/update/{id}` -> update status pesanan
- `/admin/menu` -> halaman manajemen menu admin
- `/admin/menu/store` -> tambah menu
- `/admin/menu/toggle/{id}` -> aktif/hapus menu
- `/admin/menu/update/{id}` -> update menu
- `/admin/menu/destroy/{id}` -> hapus menu

### Controller Utama
- `App\Http\Controllers\Auth\PinController` - login PIN dan proteksi blokir sementara
- `App\Http\Controllers\CashierController` - logika kasir, pesanan, dan ekspor laporan
- `App\Http\Controllers\PelangganController` - tampilan menu pelanggan dan simpan pesanan
- `App\Http\Controllers\DapurController` - status pesanan dapur
- `App\Http\Controllers\MenuController` - CRUD menu admin

### Model dan Tabel Database
- `tables` - daftar meja restoran
- `categories` - kategori menu
- `menus` - data menu makanan/minuman
- `order_statuses` - status pesanan (misal: Menunggu, Dimasak, Selesai)
- `orders` - header pesanan dengan total, meja, status, dan metode pembayaran
- `order_items` - detail item di dalam pesanan

## Database dan Field Penting
- `menus`: `category_id`, `name`, `description`, `price`, `image`, `is_active`
- `orders`: `table_id`, `order_number`, `total_price`, `order_status_id`, `payment_method`
- `order_items`: `order_id`, `menu_id`, `quantity`, `subtotal`, `notes`

## Setup Proyek
1. Install dependensi PHP:
   ```bash
   composer install
   ```
2. Copy `.env` dan buat key aplikasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```
4. Install dependensi front-end:
   ```bash
   npm install
   ```
5. Jalankan development server:
   ```bash
   npm run dev
   php artisan serve
   ```

> Catatan: Ada perintah `composer setup` yang mencakup langkah `composer install`, pembuatan `.env`, migrasi, `npm install`, dan `npm run build`.

## Cara Penggunaan
- Akses `/login-pin` untuk masuk ke sistem.
- Masukkan PIN sesuai peran.
- Untuk kasir, gunakan halaman `/kasir`.
- Untuk dapur, gunakan halaman `/dapur`.
- Untuk admin menu, gunakan halaman `/admin/menu`.

## Keterangan Khusus
- Sistem belum menggunakan autentikasi pengguna Laravel standar; akses dikendalikan hanya oleh PIN statis.
- Metode pembayaran disimpan di kolom `payment_method` di tabel `orders`.
- Ekspor laporan menghasilkan file `.xls` dari HTML tabel.

## Tujuan Proyek
Proyek ini dibuat untuk mengelola operasional restoran sederhana dengan fungsi kasir, dapur, pelanggan, dan manajemen menu. Dokumentasi ini dibuat agar pengembang lain dapat memahami alur, struktur, dan cara menjalankan aplikasi.
