# Sistem Pendaftaran Mahasiswa Baru (PMB)

Sistem pendaftaran mahasiswa baru berbasis web dengan PHP, MySQL, HTML, dan CSS.

## Fitur Sistem

### Untuk Admin:
- Dashboard statistik pendaftaran
- Kelola data calon mahasiswa (Add/Update/Delete)
- Kelola soal test seleksi (Add/Update/Delete)
- Lihat list semua pendaftar
- Lihat list hasil kelulusan (lulus/tidak lulus)
- Lihat list mahasiswa yang sudah daftar ulang

### Untuk User (Calon Mahasiswa):
- Registrasi akun dengan form lengkap
- Login ke sistem
- Mendapatkan nomor test otomatis
- Verifikasi nomor test sebelum mengikuti ujian
- Mengikuti test seleksi online (10 soal pilihan ganda)
- Melihat hasil test secara langsung
- Melakukan daftar ulang (jika lulus)
- Mendapatkan NIM otomatis setelah daftar ulang

## Cara Instalasi

### 1. Persiapan
Pastikan Anda sudah menginstall:
- XAMPP / WAMP / LAMP (Apache, MySQL, PHP)
- Web Browser (Chrome, Firefox, dll)

### 2. Import Database

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Buat database baru bernama `pmb_system`
3. Import file `database.sql` yang sudah disediakan:
   - Klik database `pmb_system`
   - Klik tab "Import"
   - Pilih file `database.sql`
   - Klik "Go"

### 3. Konfigurasi Database

Buka file `config.php` dan sesuaikan konfigurasi database jika perlu:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Kosongkan jika tidak ada password
define('DB_NAME', 'pmb_system');