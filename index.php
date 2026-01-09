<?php
// index.php
// Halaman utama / landing page

require_once 'config/config.php';

// Jika sudah login, redirect ke dashboard sesuai role
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
        default:
            redirect('/auth/login.php');
    }
}

$pageTitle = 'Selamat Datang';
include 'includes/header.php';
?>

<div class="landing-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4">Sistem Kasir Modern</h1>
                <p class="lead mb-4">
                    Kelola transaksi penjualan dengan mudah, cepat, dan efisien. 
                    Sistem kasir yang dirancang untuk kemudahan bisnis Anda.
                </p>
                
                <div class="d-grid d-sm-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="auth/login.php" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                    <a href="auth/register.php" class="btn btn-outline-primary btn-lg px-4">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card shadow-sm feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-speedometer2 fs-1 text-primary"></i>
                                </div>
                                <h5 class="card-title">Cepat & Efisien</h5>
                                <p class="card-text text-muted small">
                                    Proses transaksi dalam hitungan detik
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="card shadow-sm feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-graph-up fs-1 text-success"></i>
                                </div>
                                <h5 class="card-title">Laporan Real-time</h5>
                                <p class="card-text text-muted small">
                                    Pantau penjualan secara langsung
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="card shadow-sm feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-shield-check fs-1 text-info"></i>
                                </div>
                                <h5 class="card-title">Aman & Terpercaya</h5>
                                <p class="card-text text-muted small">
                                    Data transaksi tersimpan dengan aman
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="card shadow-sm feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-phone fs-1 text-warning"></i>
                                </div>
                                <h5 class="card-title">Mudah Digunakan</h5>
                                <p class="card-text text-muted small">
                                    Interface yang user-friendly
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="row mt-5 pt-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Fitur Unggulan</h2>
                <p class="text-muted">Sistem kasir dengan berbagai fitur menarik</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-cart-check fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">Manajemen Produk</h5>
                        <p class="card-text text-muted">
                            Kelola produk, stok, dan kategori dengan mudah
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-receipt fs-1 text-success mb-3"></i>
                        <h5 class="card-title">Cetak Struk</h5>
                        <p class="card-text text-muted">
                            Cetak struk transaksi otomatis untuk pelanggan
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-file-earmark-bar-graph fs-1 text-info mb-3"></i>
                        <h5 class="card-title">Laporan Penjualan</h5>
                        <p class="card-text text-muted">
                            Analisis penjualan harian, bulanan, dan tahunan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.landing-page {
    background: linear-gradient(135deg, #F5F7FA 0%, #E8EEF7 100%);
    min-height: 100vh;
}

.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.feature-icon {
    transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
}
</style>
