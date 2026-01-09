<?php
// logout.php
// File ini bisa digunakan untuk admin, kasir, dan client
// Letakkan file ini di folder admin/, kasir/, dan client/

require_once '../config/config.php';

// Instance Auth class
$auth = new Auth();

// Logout
$auth->logout();

// Redirect ke halaman login dengan pesan
setFlashMessage('Anda telah berhasil logout.', 'success');
redirect('/auth/login.php');
?>
