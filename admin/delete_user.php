<?php
// ===================================
// admin/delete_user.php
// ===================================
require_once '../config/config.php';
requireRole('admin');

$id = get('id');
if (!$id) {
    redirect('/admin/users.php');
}

$userClass = new User();
$user = $userClass->getUserById($id);

if (!$user) {
    setFlashMessage('User tidak ditemukan', 'danger');
    redirect('/admin/users.php');
}

// Prevent deleting own account
if ($user['id'] == $_SESSION['user_id']) {
    setFlashMessage('Tidak dapat menghapus akun yang sedang login', 'danger');
    redirect('/admin/users.php');
}

$result = $userClass->deleteUser($id);

if ($result['success']) {
    setFlashMessage($result['message'], 'success');
} else {
    setFlashMessage($result['message'], 'danger');
}

redirect('/admin/users.php');
?>
