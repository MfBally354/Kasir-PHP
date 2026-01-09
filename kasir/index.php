<?php
// ===================================
// admin/index.php
// kasir/index.php
// client/index.php
// Redirect ke dashboard sesuai role
// ===================================

require_once '../config/config.php';

// Cek login
if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

// Redirect ke dashboard sesuai role
$role = $_SESSION['role'];

switch ($role) {
    case 'admin':
        redirect('/admin/dashboard.php');
        break;
    case 'kasir':
        redirect('/kasir/dashboard.php');
        break;
    case 'client':
        redirect('/client/dashboard.php');
        break;
    default:
        redirect('/auth/login.php');
}
?>
