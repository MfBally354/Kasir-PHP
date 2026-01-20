<?php
// ===================================
// auth/reset_password.php
// Halaman untuk set password baru
// ===================================
require_once '../config/config.php';

$token = get('token');

if (!$token) {
    setFlashMessage('Token tidak valid!', 'danger');
    redirect('/auth/forgot_password.php');
}

$db = new Database();

// Validasi token
$sql = "SELECT pr.*, u.username, u.full_name, u.email 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = :token 
        AND pr.used = 0
        AND pr.expires_at > NOW()
        LIMIT 1";

$resetData = $db->fetch($sql, [':token' => $token]);

if (!$resetData) {
    setFlashMessage(
        'Link reset password tidak valid atau sudah kadaluarsa!<br>' .
        '<small>Silakan request link baru</small>',
        'danger'
    );
    redirect('/auth/forgot_password.php');
}

$pageTitle = 'Reset Password';
include '../includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-lock display-1 text-success"></i>
                            </div>
                            <h3 class="fw-bold">Reset Password</h3>
                            <p class="text-muted">Buat password baru untuk akun Anda</p>
                        </div>
                        
                        <!-- Info User -->
                        <div class="alert alert-info">
                            <strong><i class="bi bi-person me-2"></i>Akun:</strong> 
                            <?php echo htmlspecialchars($resetData['full_name']); ?>
                            <br>
                            <small class="text-muted">@<?php echo htmlspecialchars($resetData['username']); ?></small>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form action="process_reset_password.php" method="POST" id="resetForm">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" name="new_password" 
                                           id="newPassword" placeholder="Minimal 6 karakter" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword1">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control" name="confirm_password" 
                                           id="confirmPassword" placeholder="Ulangi password baru" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword2">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatch" class="form-text"></div>
                            </div>
                            
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Reset Password
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">
                                    <a href="login.php" class="text-decoration-none">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Login
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
.auth-page {
    background: linear-gradient(135deg, #F5F7FA 0%, #E8EEF7 100%);
    min-height: 100vh;
}

.input-group-text {
    background-color: #F5F7FA;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
    border-right: none;
}

.input-group .form-control:focus {
    border-left: none;
    border-right: none;
}
</style>

<script>
// Toggle password visibility
document.getElementById('togglePassword1').addEventListener('click', function() {
    const input = document.getElementById('newPassword');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

document.getElementById('togglePassword2').addEventListener('click', function() {
    const input = document.getElementById('confirmPassword');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Password match validation
const newPassword = document.getElementById('newPassword');
const confirmPassword = document.getElementById('confirmPassword');
const passwordMatch = document.getElementById('passwordMatch');

function checkPasswordMatch() {
    if (confirmPassword.value === '') {
        passwordMatch.textContent = '';
        confirmPassword.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (newPassword.value === confirmPassword.value) {
        passwordMatch.textContent = '✓ Password cocok';
        passwordMatch.className = 'form-text text-success';
        confirmPassword.classList.remove('is-invalid');
        confirmPassword.classList.add('is-valid');
    } else {
        passwordMatch.textContent = '✗ Password tidak cocok';
        passwordMatch.className = 'form-text text-danger';
        confirmPassword.classList.remove('is-valid');
        confirmPassword.classList.add('is-invalid');
    }
}

newPassword.addEventListener('input', checkPasswordMatch);
confirmPassword.addEventListener('input', checkPasswordMatch);

// Form validation
document.getElementById('resetForm').addEventListener('submit', function(e) {
    const password = newPassword.value;
    const confirm = confirmPassword.value;
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter!');
        return false;
    }
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak cocok!');
        return false;
    }
});
</script>