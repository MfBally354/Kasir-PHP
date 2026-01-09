<?php
// ===================================
// admin/logout.php
// kasir/logout.php  
// client/logout.php
// Logout dan redirect ke login
// ===================================

require_once '../config/config.php';

// Instance Auth class
$auth = new Auth();

// Logout
$auth->logout();

// Redirect ke halaman login dengan pesan
setFlashMessage('Anda telah berhasil logout.', 'success');
redirect('/auth/login.php');
?>
