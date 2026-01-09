<?php
// index.php
// File ini ada di folder admin/, kasir/, dan client/
// Redirect ke dashboard

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
