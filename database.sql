-- Database untuk Sistem Pendaftaran Mahasiswa Baru
CREATE DATABASE IF NOT EXISTS pmb_system;
USE pmb_system;

-- Tabel Admin
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel User/Calon Mahasiswa
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    nomor_test VARCHAR(20) UNIQUE,
    status_test ENUM('belum', 'lulus', 'tidak_lulus') DEFAULT 'belum',
    nilai_test INT DEFAULT 0,
    status_daftar_ulang ENUM('belum', 'sudah') DEFAULT 'belum',
    nim VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Soal Test
CREATE TABLE soal_test (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    jawaban_benar ENUM('A', 'B', 'C', 'D') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Hasil Test
CREATE TABLE hasil_test (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    soal_id INT NOT NULL,
    jawaban ENUM('A', 'B', 'C', 'D') NOT NULL,
    benar BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (soal_id) REFERENCES soal_test(id) ON DELETE CASCADE
);

-- Insert Admin Default
INSERT INTO admin (username, password, nama) VALUES 
('admin', MD5('admin123'), 'Administrator');

-- Insert Soal Test Sample
INSERT INTO soal_test (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES
('Apa kepanjangan dari HTML?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlinks and Text Markup Language', 'A'),
('Bahasa pemrograman apa yang digunakan untuk styling website?', 'JavaScript', 'CSS', 'Python', 'Java', 'B'),
('Apa fungsi dari tag <p> dalam HTML?', 'Membuat paragraf', 'Membuat gambar', 'Membuat link', 'Membuat tabel', 'A'),
('Database yang umum digunakan dengan PHP adalah?', 'MongoDB', 'MySQL', 'Oracle', 'SQLite', 'B'),
('Apa kepanjangan dari CSS?', 'Computer Style Sheets', 'Creative Style Sheets', 'Cascading Style Sheets', 'Colorful Style Sheets', 'C'),
('Protokol yang digunakan untuk transfer data di web adalah?', 'FTP', 'SMTP', 'HTTP', 'POP3', 'C'),
('Bahasa pemrograman server-side yang populer adalah?', 'HTML', 'CSS', 'PHP', 'Bootstrap', 'C'),
('Apa fungsi dari tag <a> dalam HTML?', 'Membuat paragraf', 'Membuat link', 'Membuat gambar', 'Membuat tabel', 'B'),
('Framework CSS yang populer adalah?', 'Laravel', 'Bootstrap', 'Django', 'Flask', 'B'),
('Nilai passing grade test adalah?', '60', '70', '80', '90', 'B');
