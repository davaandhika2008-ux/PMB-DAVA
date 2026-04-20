<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pmb_system');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");

// Function untuk membersihkan input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Function untuk generate nomor test
function generateNomorTest() {
    return 'TEST-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Function untuk generate NIM
function generateNIM() {
    return date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
}

// Function untuk cek login admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Function untuk cek login user
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
