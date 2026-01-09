<?php
// admin/dashboard.php
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Dashboard Admin';

// Get statistics
$db = new Database();
$productClass = new Product();
$userClass = new User();
$transactionClass = new Transaction();

// Total products
$totalProducts = $productClass->getTotalProducts();

// Total users
$totalKasir = $userClass->getTotalUsersByRole('kasir');
$totalClient = $userClass->getTotalUsersByRole('client');

// Today's statistics
$today = date('Y-m-d');
$todayStats = $transactionClass->getStatistics([
    'date_from' => $today,
    'date_to' => $today
]);

// This month statistics
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');
$monthStats = $transactionClass->getStatistics([
    'date_from' => $monthStart,
    'date_to' => $monthEnd
]);

// Low stock products
$lowStockProducts = $productClass->getLowStockProducts(5);

// Recent transactions
$recentTransactions = $transactionClass->getAllTransactions(['limit' => 5]);

// Best selling products this month
$bestSelling = $transactionClass->getBestSellingProducts(5, [
    'date_from' => $monthStart,
    'date_to' => $monthEnd
]);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Dashboard Admin</h2>
            <p class="text-muted">Selamat datang, <?php echo $_SESSION['full_name']; ?>!</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Produk -->
        <div class="col-md-3">
            <div class="card stats-card info border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Produk</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $totalProducts; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Kasir -->
        <div class="col-md-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Kasir</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $totalKasir; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Client -->
        <div class="col-md-3">
            <div class="card stats-card success border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Pembeli</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $totalClient; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Transaksi Hari Ini -->
        <div class="col-md-3">
            <div class="card stats-card warning border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Transaksi Hari Ini</h6>
                            <h3 class="mb-0 fw-bold"><?php echo $todayStats['total_transactions']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-calendar-day text-primary me-2"></i>Pendapatan Hari Ini
                    </h5>
                    <h2 class="text-success fw-bold"><?php echo formatRupiah($todayStats['total_revenue']); ?></h2>
                    <p class="text-muted mb-0">
                        <small><?php echo $todayStats['total_transactions']; ?> transaksi</small>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-calendar-month text-primary me-2"></i>Pendapatan Bulan Ini
                    </h5>
                    <h2 class="text-success fw-bold"><?php echo formatRupiah($monthStats['total_revenue']); ?></h2>
                    <p class="text-muted mb-0">
                        <small><?php echo $monthStats['total_transactions']; ?> transaksi</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <!-- Low Stock Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Menipis
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($lowStockProducts)): ?>
                        <p class="text-muted text-center py-3">Tidak ada produk dengan stok menipis</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th class="text-end">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-danger"><?php echo $product['stock']; ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Best Selling Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-star text-warning me-2"></i>Produk Terlaris Bulan Ini
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($bestSelling)): ?>
                        <p class="text-muted text-center py-3">Belum ada data penjualan</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Terjual</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bestSelling as $product): ?>
                                    <tr>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-success"><?php echo $product['total_sold']; ?></span>
                                        </td>
                                        <td class="text-end"><?php echo formatRupiah($product['total_revenue']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>Transaksi Terbaru
                    </h5>
                    <a href="reports.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentTransactions)): ?>
                        <p class="text-muted text-center py-3">Belum ada transaksi</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Customer</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentTransactions as $trans): ?>
                                    <tr>
                                        <td><strong><?php echo $trans['transaction_code']; ?></strong></td>
                                        <td><?php echo $trans['customer_name']; ?></td>
                                        <td><?php echo $trans['kasir_name'] ?? '-'; ?></td>
                                        <td class="text-end"><?php echo formatRupiah($trans['total_amount']); ?></td>
                                        <td><?php echo statusBadge($trans['status']); ?></td>
                                        <td><?php echo formatDateTime($trans['created_at'], 'd/m/Y H:i'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
