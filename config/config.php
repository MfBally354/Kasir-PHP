<?php
// config/config.php
// Konfigurasi umum aplikasi

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definisi konstanta aplikasi
define('APP_NAME', 'Sistem Kasir');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://192.168.1.16:8090'); // Sesuaikan dengan URL kamu

// Path direktori
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Include file database
require_once ROOT_PATH . '/config/database.php';

// Include semua class yang diperlukan
require_once ROOT_PATH . '/classes/Database.php';
require_once ROOT_PATH . '/classes/Auth.php';
require_once ROOT_PATH . '/classes/User.php';
require_once ROOT_PATH . '/classes/Product.php';
require_once ROOT_PATH . '/classes/Transaction.php';

// Include functions
require_once ROOT_PATH . '/includes/functions.php';

// Setting error reporting (set ke 0 untuk production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fungsi untuk redirect
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Fungsi untuk cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk cek role user
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Fungsi untuk protect halaman berdasarkan role
function requireRole($role) {
    if (!isLoggedIn()) {
        redirect('/auth/login.php');
    }
    
    if (!hasRole($role)) {
        redirect('/index.php');
    }
}

// Fungsi untuk format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk generate kode transaksi
function generateTransactionCode() {
    return 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>
