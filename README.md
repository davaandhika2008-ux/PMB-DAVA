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
```

### 4. Upload File ke Server

1. Copy semua file ke folder `htdocs` (XAMPP) atau `www` (WAMP)
2. Buat folder baru misalnya: `htdocs/pmb-system/`
3. Paste semua file ke folder tersebut

### 5. Struktur Folder

```
pmb-system/
├── admin/
│   ├── dashboard.php
│   ├── kelola-maba.php
│   ├── kelola-soal.php
│   ├── list-pendaftar.php
│   ├── list-kelulusan.php
│   └── list-daftar-ulang.php
├── user/
│   ├── dashboard.php
│   ├── test.php
│   └── daftar-ulang.php
├── config.php
├── database.sql
├── index.html
├── login.php
├── register.php
├── logout.php
└── README.md
```

## Cara Menggunakan

### 1. Akses Landing Page
Buka browser dan akses: `http://localhost/pmb-system/`

### 2. Login Admin
- URL: `http://localhost/pmb-system/login.php`
- Pilih role: **Admin**
- Username: `admin`
- Password: `admin123`

### 3. Registrasi User (Calon Mahasiswa)
1. Klik "Daftar Sekarang" di landing page
2. Isi formulir pendaftaran lengkap
3. Setelah berhasil, akan mendapat nomor test
4. Catat nomor test tersebut

### 4. Login User
1. Login dengan email dan password yang didaftarkan
2. Pilih role: **Calon Mahasiswa**

### 5. Mengikuti Test
1. Di dashboard user, klik "Mulai Test"
2. Masukkan nomor test yang sudah didapat saat registrasi
3. Kerjakan 10 soal pilihan ganda
4. Timer: 30 menit
5. Klik "Submit Test" setelah selesai
6. Hasil langsung muncul (passing grade: 70)

### 6. Daftar Ulang (Jika Lulus)
1. Jika nilai ≥ 70, user dinyatakan lulus
2. Klik menu "Daftar Ulang"
3. Konfirmasi data
4. Dapatkan NIM otomatis

## Fitur Keamanan
- Password di-hash menggunakan MD5
- Session management untuk autentikasi
- Input validation dan sanitization
- SQL injection protection

## Teknologi yang Digunakan
- **Frontend**: HTML5, CSS3
- **Backend**: PHP 7+
- **Database**: MySQL 5.7+
- **Server**: Apache

## Default Data

### Admin Default:
- Username: `admin`
- Password: `admin123`

### Soal Test:
Sudah terisi 10 soal sample tentang teknologi dasar

## Tips & Catatan

1. **Nomor Test**: Simpan nomor test dengan baik, diperlukan untuk verifikasi sebelum test
2. **Passing Grade**: Nilai minimal lulus adalah 70
3. **Timer**: Test memiliki waktu 30 menit, akan otomatis submit jika waktu habis
4. **Admin**: Dapat mengelola semua data termasuk menambah/edit/hapus user dan soal

## Troubleshooting

### Database Connection Error
- Pastikan MySQL service sudah running
- Cek konfigurasi di `config.php`
- Pastikan database `pmb_system` sudah dibuat

### Nomor Test Tidak Muncul
- Pastikan registrasi berhasil
- Cek di tabel `users` di database

### Test Tidak Bisa Submit
- Pastikan semua soal sudah dijawab
- Cek koneksi internet jika menggunakan online server

## Support & Contact

Untuk pertanyaan atau masalah, silakan hubungi administrator sistem.

---

**Sistem Pendaftaran Mahasiswa Baru v1.0**
