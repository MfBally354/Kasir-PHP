<?php
// ===================================
// auth/process_reset_password.php
// Proses update password baru
// ===================================
require_once '../config/config.php';

if (!isPost()) {
    redirect('/auth/forgot_password.php');
}

$token = post('token');
$newPassword = post('new_password');
$confirmPassword = post('confirm_password');

// Validasi
$errors = [];

if (empty($token)) {
    $errors[] = 'Token tidak valid';
}

if (empty($newPassword)) {
    $errors[] = 'Password baru harus diisi';
}

if (strlen($newPassword) < 6) {
    $errors[] = 'Password minimal 6 karakter';
}

if ($newPassword !== $confirmPassword) {
    $errors[] = 'Password dan konfirmasi password tidak cocok';
}

if (!empty($errors)) {
    setFlashMessage(implode('<br>', $errors), 'danger');
    redirect('/auth/reset_password.php?token=' . urlencode($token));
}

$db = new Database();

try {
    // Validasi token
    $sql = "SELECT pr.*, u.id as user_id, u.username 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = :token 
            AND pr.used = 0
            AND pr.expires_at > NOW()
            LIMIT 1";
    
    $resetData = $db->fetch($sql, [':token' => $token]);
    
    if (!$resetData) {
        throw new Exception('Token tidak valid atau sudah kadaluarsa!');
    }
    
    // Hash password baru
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
    // Begin transaction
    $db->beginTransaction();
    
    // Update password user
    $updateResult = $db->update('users', 
        ['password' => $hashedPassword], 
        'id = :id', 
        [':id' => $resetData['user_id']]
    );
    
    if (!$updateResult) {
        throw new Exception('Gagal mengupdate password');
    }
    
    // Mark token as used
    $db->update('password_resets',
        ['used' => 1],
        'id = :id',
        [':id' => $resetData['id']]
    );
    
    // Commit transaction
    $db->commit();
    
    // Log activity
    error_log("Password reset successful for user: " . $resetData['username']);
    
    // Success message
    setFlashMessage(
        '<strong>Password Berhasil Direset!</strong><br>' .
        'Anda sekarang dapat login dengan password baru Anda.',
        'success'
    );
    
    redirect('/auth/login.php');
    
} catch (Exception $e) {
    // Rollback jika error
    if ($db->getConnection()->inTransaction()) {
        $db->rollback();
    }
    
    error_log("Reset Password Error: " . $e->getMessage());
    
    setFlashMessage(
        'Error: ' . $e->getMessage() . '<br>' .
        '<small>Silakan request link reset baru</small>',
        'danger'
    );
    
    redirect('/auth/forgot_password.php');
}
?>