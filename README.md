# Jadwal Shalat Jumat - Masjid An Nur Tong Tji

Aplikasi web sederhana untuk mengelola jadwal shalat Jumat di Masjid An Nur Tong Tji. Aplikasi ini memungkinkan pengguna untuk membuat, melihat, mengedit, dan mengekspor jadwal shalat Jumat.

## Fitur

- Pembuatan jadwal shalat Jumat bulanan
- Penyimpanan data khatib dan muadzin
- Pencatatan pasaran Jawa (Legi, Pahing, Pon, Wage, Kliwon)
- Ekspor jadwal ke format Excel dan PDF
- Tampilan yang responsif dan mudah digunakan

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer (untuk mengelola dependensi)

## Dependensi

- [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) - Untuk ekspor Excel
- [FPDF](http://www.fpdf.org/) - Untuk ekspor PDF

## Instalasi

1. Clone repositori ini ke direktori web server Anda:

```bash
git clone https://github.com/username/jadwal-shalat-jumat.git
cd jadwal-shalat-jumat
```

2. Instal dependensi menggunakan Composer:

```bash
composer install
```

3. Konfigurasi database:
   - Edit file `config/database.php` sesuai dengan pengaturan database Anda

4. Akses aplikasi melalui browser web:
   - Buka `http://localhost/jadwal-shalat-jumat` (sesuaikan dengan konfigurasi server Anda)

## Penggunaan

### Membuat Jadwal Baru

1. Klik tombol "Buat Jadwal" di halaman utama
2. Pilih bulan dan tahun untuk jadwal yang ingin dibuat
3. Isi data khatib dan muadzin untuk setiap Jumat dalam bulan tersebut
4. Klik "Simpan" untuk menyimpan jadwal

### Melihat Jadwal

1. Pada halaman utama, lihat daftar jadwal yang tersimpan
2. Klik "Lihat" pada jadwal yang ingin dilihat

### Mengedit Jadwal

1. Pada halaman utama, klik "Edit" pada jadwal yang ingin diubah
2. Lakukan perubahan yang diperlukan
3. Klik "Simpan" untuk menyimpan perubahan

### Mengekspor Jadwal

1. Pada halaman utama, klik "Export" pada jadwal yang ingin diekspor
2. Pilih format ekspor (Excel atau PDF)
3. File akan diunduh secara otomatis

## Struktur Database

Aplikasi ini menggunakan dua tabel utama:

1. `schedules` - Menyimpan informasi jadwal bulanan
   - `id` - ID unik jadwal
   - `year` - Tahun jadwal
   - `month` - Bulan jadwal (1-12)
   - `created_at` - Waktu pembuatan jadwal
   - `updated_at` - Waktu pembaruan terakhir

2. `schedule_details` - Menyimpan detail untuk setiap Jumat
   - `id` - ID unik detail
   - `schedule_id` - ID jadwal (foreign key ke schedules.id)
   - `date` - Tanggal Jumat
   - `pasaran` - Pasaran Jawa (Legi, Pahing, Pon, Wage, Kliwon)
   - `preacher` - Nama khatib
   - `muadzin` - Nama muadzin

## Kontribusi

Kontribusi untuk perbaikan dan pengembangan aplikasi ini sangat diterima. Silakan buat pull request atau laporkan masalah melalui issue tracker.

## Lisensi

[MIT License](LICENSE)
```