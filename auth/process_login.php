<?php
// auth/process_login.php
// Proses login untuk Kasir dan Client

require_once '../config/config.php';

// Redirect jika bukan POST request
if (!isPost()) {
    redirect('/auth/login.php');
}

// Ambil data dari form
$username = post('username');
$password = post('password');
$roleType = post('role_type'); // 'kasir' atau 'client'

// Validasi input kosong
if (empty($username) || empty($password)) {
    setFlashMessage('Username dan password harus diisi!', 'danger');
    redirect('/auth/login.php');
}

// Instance Auth class
$auth = new Auth();

// Proses login
$result = $auth->login($username, $password);

// Cek hasil login
if ($result['success']) {
    $userRole = $result['role'];
    
    // Validasi role sesuai dengan tab yang dipilih
    if ($roleType == 'kasir' && $userRole != 'kasir') {
        // User bukan kasir tapi login di tab kasir
        $auth->logout();
        setFlashMessage('Akses ditolak! Anda bukan kasir. Silakan login sebagai pembeli.', 'danger');
        redirect('/auth/login.php');
    }
    
    if ($roleType == 'client' && $userRole != 'client') {
        // User bukan client tapi login di tab client
        $auth->logout();
        setFlashMessage('Akses ditolak! Akun ini adalah kasir. Silakan login di tab Kasir.', 'danger');
        redirect('/auth/login.php');
    }
    
    // Login berhasil dan role sesuai
    setFlashMessage('Login berhasil! Selamat datang ' . $_SESSION['full_name'], 'success');
    
    // Redirect sesuai role
    switch ($userRole) {
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
            redirect('/index.php');
    }
} else {
    // Login gagal
    setFlashMessage($result['message'], 'danger');
    redirect('/auth/login.php');
}
?>
