<?php
// auth/register.php
// Halaman registrasi untuk Client/Pembeli

require_once '../config/config.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect('/client/dashboard.php');
}

$pageTitle = 'Registrasi';
include '../includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Left Side - Form -->
                            <div class="col-lg-6 p-5">
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold">Buat Akun Baru</h3>
                                    <p class="text-muted">Daftar sebagai pembeli</p>
                                </div>
                                
                                <?php displayFlashMessage(); ?>
                                
                                <form action="process_register.php" method="POST" id="registerForm">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                                            <input type="text" class="form-control" name="full_name" 
                                                   placeholder="Masukkan nama lengkap" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" name="username" 
                                                   placeholder="Masukkan username" required>
                                        </div>
                                        <small class="text-muted">Username harus unik</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" name="email" 
                                                   placeholder="contoh@email.com" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">No. Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="tel" class="form-control" name="phone" 
                                                   placeholder="08123456789">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <textarea class="form-control" name="address" rows="2" 
                                                      placeholder="Masukkan alamat lengkap"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input type="password" class="form-control" name="password" 
                                                   id="password" placeholder="Minimal 6 karakter" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" class="form-control" name="confirm_password" 
                                                   id="confirm_password" placeholder="Ulangi password" required>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <p class="mb-0">Sudah punya akun? 
                                            <a href="login.php" class="text-decoration-none">Login Di Sini</a>
                                        </p>
                                    </div>
                                </form>
                                
                                <div class="text-center mt-4">
                                    <a href="../index.php" class="text-muted text-decoration-none">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Right Side - Image/Info -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="auth-side-panel h-100 d-flex align-items-center justify-content-center p-5">
                                    <div class="text-center text-white">
                                        <i class="bi bi-cart-check display-1 mb-4"></i>
                                        <h4 class="fw-bold mb-3">Bergabung dengan Kami</h4>
                                        <p class="lead mb-4">Nikmati kemudahan berbelanja dengan akun Anda</p>
                                        <ul class="list-unstyled text-start">
                                            <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Riwayat transaksi tersimpan</li>
                                            <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Checkout lebih cepat</li>
                                            <li class="mb-2"><i class="bi bi-check-circle me-2"></i>Akses ke promo khusus</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Validasi password match
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak cocok!');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter!');
        return false;
    }
});
</script>

<style>
.auth-page {
    background: linear-gradient(135deg, #F5F7FA 0%, #E8EEF7 100%);
    min-height: 100vh;
}

.auth-side-panel {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
    border-radius: 0 0.5rem 0.5rem 0;
}

.input-group-text {
    background-color: #F5F7FA;
    border-right: none;
}

.input-group .form-control,
.input-group textarea {
    border-left: none;
}

.input-group .form-control:focus,
.input-group textarea:focus {
    border-left: none;
}
</style>
