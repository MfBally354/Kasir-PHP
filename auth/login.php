<?php
// auth/login.php
// Halaman login untuk Kasir dan Client

require_once '../config/config.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
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
    }
}

$pageTitle = 'Login';
include '../includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Left Side - Form -->
                            <div class="col-lg-6 p-5">
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold">Selamat Datang</h3>
                                    <p class="text-muted">Login ke akun Anda</p>
                                </div>
                                
                                <?php displayFlashMessage(); ?>
                                
                                <!-- Tab Navigation -->
                                <ul class="nav nav-pills nav-fill mb-4" id="loginTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="kasir-tab" data-bs-toggle="pill" 
                                                data-bs-target="#kasir" type="button" role="tab">
                                            <i class="bi bi-person-badge me-1"></i> Kasir
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="client-tab" data-bs-toggle="pill" 
                                                data-bs-target="#client" type="button" role="tab">
                                            <i class="bi bi-person me-1"></i> Pembeli
                                        </button>
                                    </li>
                                </ul>
                                
                                <!-- Tab Content -->
                                <div class="tab-content" id="loginTabContent">
                                    <!-- Kasir Login -->
                                    <div class="tab-pane fade show active" id="kasir" role="tabpanel">
                                        <form action="process_login.php" method="POST" id="kasirLoginForm">
                                            <input type="hidden" name="role_type" value="kasir">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                    <input type="text" class="form-control" name="username" 
                                                           placeholder="Masukkan username" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                    <input type="password" class="form-control" name="password" 
                                                           placeholder="Masukkan password" required>
                                                </div>
                                            </div>
                                            
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Sebagai Kasir
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    
                                    <!-- Client Login -->
                                    <div class="tab-pane fade" id="client" role="tabpanel">
                                        <form action="process_login.php" method="POST" id="clientLoginForm">
                                            <input type="hidden" name="role_type" value="client">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                    <input type="text" class="form-control" name="username" 
                                                           placeholder="Masukkan username" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                    <input type="password" class="form-control" name="password" 
                                                           placeholder="Masukkan password" required>
                                                </div>
                                            </div>
                                            
                                            <div class="d-grid mb-3">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login Sebagai Pembeli
                                                </button>
                                            </div>
                                            
                                            <div class="text-center">
                                                <p class="mb-0">Belum punya akun? 
                                                    <a href="register.php" class="text-decoration-none">Daftar Sekarang</a>
                                                </p>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
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
                                        <i class="bi bi-shop display-1 mb-4"></i>
                                        <h4 class="fw-bold mb-3">Sistem Kasir Modern</h4>
                                        <p class="lead">Kelola transaksi bisnis Anda dengan mudah dan efisien</p>
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

<style>
.auth-page {
    background: linear-gradient(135deg, #F5F7FA 0%, #E8EEF7 100%);
    min-height: 100vh;
}

.auth-side-panel {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
    border-radius: 0 0.5rem 0.5rem 0;
}

.nav-pills .nav-link {
    color: #6c757d;
    border-radius: 0.5rem;
}

.nav-pills .nav-link.active {
    background-color: #1E3A8A;
}

.input-group-text {
    background-color: #F5F7FA;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-left: none;
}
</style>
