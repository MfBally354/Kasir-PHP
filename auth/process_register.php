<?php
// auth/process_register.php
// Proses registrasi untuk Client/Pembeli

require_once '../config/config.php';

// Redirect jika bukan POST request
if (!isPost()) {
    redirect('/auth/register.php');
}

// Ambil data dari form
$username = post('username');
$password = post('password');
$confirmPassword = post('confirm_password');
$fullName = post('full_name');
$email = post('email');
$phone = post('phone');
$address = post('address');

// Array untuk menampung error
$errors = [];

// Validasi input
if (empty($username)) {
    $errors[] = 'Username harus diisi';
}

if (empty($password)) {
    $errors[] = 'Password harus diisi';
}

if (strlen($password) < 6) {
    $errors[] = 'Password minimal 6 karakter';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Password dan konfirmasi password tidak cocok';
}

if (empty($fullName)) {
    $errors[] = 'Nama lengkap harus diisi';
}

if (empty($email)) {
    $errors[] = 'Email harus diisi';
}

if (!validateEmail($email)) {
    $errors[] = 'Format email tidak valid';
}

// Jika ada error, redirect kembali dengan pesan error
if (!empty($errors)) {
    $errorMessage = implode('<br>', $errors);
    setFlashMessage($errorMessage, 'danger');
    redirect('/auth/register.php');
}

// Instance Auth class
$auth = new Auth();

// Proses registrasi
$result = $auth->register($username, $password, $fullName, $email, $phone, $address);

if ($result['success']) {
    // Registrasi berhasil
    setFlashMessage($result['message'] . ' Silakan login.', 'success');
    redirect('/auth/login.php');
} else {
    // Registrasi gagal
    setFlashMessage($result['message'], 'danger');
    redirect('/auth/register.php');
}
?>
