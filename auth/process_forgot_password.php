<?php
// ===================================
// auth/process_forgot_password.php
// Proses request reset password
// ===================================
require_once '../config/config.php';

if (!isPost()) {
    redirect('/auth/forgot_password.php');
}

$identifier = post('identifier'); // Username atau email

if (empty($identifier)) {
    setFlashMessage('Username atau email harus diisi!', 'danger');
    redirect('/auth/forgot_password.php');
}

$db = new Database();

// Cari user berdasarkan username atau email
$sql = "SELECT * FROM users 
        WHERE (username = :identifier OR email = :identifier) 
        AND status = 'active' 
        LIMIT 1";

$user = $db->fetch($sql, [':identifier' => $identifier]);

if (!$user) {
    // SECURITY: Jangan beri tahu user tidak ditemukan (prevent user enumeration)
    setFlashMessage(
        'Jika akun Anda terdaftar, link reset password telah dibuat. Hubungi admin untuk mendapatkan link.',
        'info'
    );
    redirect('/auth/forgot_password.php');
}

try {
    // Generate token
    $token = bin2hex(random_bytes(32)); // 64 karakter
    
    // Token berlaku 1 jam
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Hapus token lama untuk user ini
    $db->delete('password_resets', 'user_id = :user_id', [':user_id' => $user['id']]);
    
    // Insert token baru
    $resetData = [
        'user_id' => $user['id'],
        'token' => $token,
        'expires_at' => $expiresAt,
        'used' => 0
    ];
    
    $resetId = $db->insert('password_resets', $resetData);
    
    if ($resetId) {
        // Generate reset link
        $resetLink = BASE_URL . '/auth/reset_password.php?token=' . $token;
        
        // SUCCESS MESSAGE dengan reset link (karena no email)
        setFlashMessage(
            '<strong>Link Reset Password Berhasil Dibuat!</strong><br><br>' .
            'Copy link berikut untuk reset password:<br>' .
            '<div class="alert alert-warning mt-2 mb-0">' .
            '<strong>Reset Link:</strong><br>' .
            '<input type="text" class="form-control" value="' . htmlspecialchars($resetLink) . '" readonly onclick="this.select()">' .
            '<small class="d-block mt-2">Link ini berlaku selama 1 jam</small>' .
            '</div>' .
            '<br><small>Atau hubungi admin untuk mendapatkan link reset</small>',
            'success'
        );
    } else {
        throw new Exception('Gagal membuat reset token');
    }
    
} catch (Exception $e) {
    error_log("Forgot Password Error: " . $e->getMessage());
    setFlashMessage('Terjadi kesalahan. Silakan coba lagi.', 'danger');
}

redirect('/auth/forgot_password.php');
?>