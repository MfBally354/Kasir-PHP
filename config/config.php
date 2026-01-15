<?php
// ===================================
// config/config.php - FINAL VERSION
// Main Configuration File
// ===================================

// Error reporting (ubah ke 0 untuk production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===================================
// APPLICATION CONSTANTS
// ===================================
define('APP_NAME', 'Sistem Kasir PHP');
define('APP_VERSION', '1.0.0');

// Detect environment (Docker or Native PHP)
$isDocker = getenv('APACHE_DOCUMENT_ROOT') !== false || file_exists('/.dockerenv');

// Base URL - Auto detect
if ($isDocker) {
    // Running di Docker
    define('BASE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8090'));
} else {
    // Running di Native PHP
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptPath === '/' || $scriptPath === '\\') ? '' : $scriptPath;
    define('BASE_URL', $protocol . '://' . $host . $basePath);
}

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// Timezone
date_default_timezone_set('Asia/Jakarta');

// ===================================
// DATABASE CONFIGURATION
// ===================================
require_once __DIR__ . '/database.php';

// ===================================
// AUTOLOAD CLASSES
// ===================================
spl_autoload_register(function($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// ===================================
// INCLUDE HELPER FUNCTIONS
// ===================================
require_once __DIR__ . '/../includes/functions.php';

// ===================================
// CORE HELPER FUNCTIONS
// ===================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Require specific role - with Admin bypass
 */
function requireRole($role) {
    // Must be logged in
    if (!isLoggedIn()) {
        setFlashMessage('Silakan login terlebih dahulu', 'warning');
        redirect('/auth/login.php');
        exit;
    }
    
    $userRole = getUserRole();
    
    // Admin can access everything
    if ($userRole === 'admin') {
        return true;
    }
    
    // Check if user has the required role
    if ($userRole !== $role) {
        setFlashMessage('Akses ditolak! Anda tidak memiliki hak akses ke halaman ini.', 'danger');
        
        // Redirect to appropriate dashboard
        switch ($userRole) {
            case 'kasir':
                redirect('/kasir/dashboard.php');
                break;
            case 'client':
                redirect('/client/dashboard.php');
                break;
            default:
                redirect('/auth/login.php');
        }
        exit;
    }
    
    return true;
}

/**
 * Redirect helper
 */
function redirect($path) {
    // If already full URL, redirect directly
    if (strpos($path, 'http') === 0) {
        header('Location: ' . $path);
        exit;
    }
    
    // Add BASE_URL
    header('Location: ' . BASE_URL . $path);
    exit;
}

/**
 * Format number as Rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Generate unique transaction code
 */
function generateTransactionCode() {
    $prefix = 'TRX';
    $date = date('Ymd');
    $random = strtoupper(substr(uniqid(), -6));
    return $prefix . '-' . $date . '-' . $random;
}

/**
 * Debug helper - hanya untuk development
 */
function debug($data, $die = false) {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    print_r($data);
    echo '</pre>';
    if ($die) die();
}

// ===================================
// TIMEZONE & LOCALE
// ===================================
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian');

// ===================================
// CONFIG LOADED
// ===================================
// Ready to use!
