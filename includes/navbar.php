<?php
// includes/navbar.php - UPDATED
// Tambah notifikasi pending cancel requests untuk admin

$currentRole = $_SESSION['role'] ?? '';
$fullName = $_SESSION['full_name'] ?? 'User';

// Tentukan base path untuk link
$basePath = '';
if ($currentRole == 'admin') {
    $basePath = BASE_URL . '/admin';
} elseif ($currentRole == 'kasir') {
    $basePath = BASE_URL . '/kasir';
} elseif ($currentRole == 'client') {
    $basePath = BASE_URL . '/client';
}

// BARU: Hitung pending cancel requests untuk admin
$pendingCancelCount = 0;
if ($currentRole == 'admin') {
    $db = new Database();
    $pendingCancelCount = $db->count('cancellation_requests', 'status = :status', [':status' => 'pending']);
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E3A8A;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/index.php">
            <i class="bi bi-shop me-2"></i><?php echo APP_NAME; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($currentRole == 'admin'): ?>
                    <!-- Menu Admin -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/products.php">
                            <i class="bi bi-box-seam me-1"></i>Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/users.php">
                            <i class="bi bi-people me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/reports.php">
                            <i class="bi bi-file-bar-graph me-1"></i>Laporan
                        </a>
                    </li>
                    <!-- BARU: Menu Pembatalan -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?php echo $basePath; ?>/cancel_requests.php">
                            <i class="bi bi-exclamation-triangle me-1"></i>Pembatalan
                            <?php if ($pendingCancelCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $pendingCancelCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                <?php elseif ($currentRole == 'kasir'): ?>
                    <!-- Menu Kasir -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/transaction.php">
                            <i class="bi bi-cash-coin me-1"></i>Transaksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/history.php">
                            <i class="bi bi-clock-history me-1"></i>Riwayat
                        </a>
                    </li>
                    
                <?php elseif ($currentRole == 'client'): ?>
                    <!-- Menu Client -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/products.php">
                            <i class="bi bi-bag me-1"></i>Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/cart.php">
                            <i class="bi bi-cart me-1"></i>Keranjang
                            <?php 
                            $db = new Database();
                            $cartCount = $db->count('cart', 'user_id = :user_id', [':user_id' => $_SESSION['user_id']]);
                            if ($cartCount > 0):
                            ?>
                            <span class="badge bg-danger rounded-pill"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/my_orders.php">
                            <i class="bi bi-receipt me-1"></i>Pesanan Saya
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- User Menu -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i><?php echo $fullName; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <h6 class="dropdown-header">
                                <i class="bi bi-person me-1"></i><?php echo $fullName; ?>
                            </h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <span class="dropdown-item-text">
                                <small class="text-muted">Role: <?php echo roleBadge($currentRole); ?></small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo $basePath; ?>/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
