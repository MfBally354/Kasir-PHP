<?php
// ===================================
// kasir/dashboard.php - FIXED VERSION
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Dashboard Kasir';
$transactionClass = new Transaction();

$today = date('Y-m-d');

// FIXED: Hitung semua transaksi hari ini (tidak hanya yang dilakukan kasir ini)
$todayStats = $transactionClass->getStatistics([
    'date_from' => $today,
    'date_to' => $today
    // TIDAK FILTER kasir_id, agar termasuk transaksi dari client
]);

// Transaksi yang dibuat oleh kasir ini
$myTransactions = $transactionClass->getAllTransactions([
    'kasir_id' => $_SESSION['user_id'],
    'limit' => 5
]);

// BARU: Transaksi pending dari client yang perlu approval
$pendingOrders = $transactionClass->getAllTransactions([
    'status' => 'pending',
    'transaction_type' => 'client',
    'limit' => 10
]);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Dashboard Kasir</h2>
            <p class="text-muted">Selamat datang, <?php echo $_SESSION['full_name']; ?>!</p>
        </div>
        <div class="col-auto">
            <a href="transaction.php" class="btn btn-primary btn-lg">
                <i class="bi bi-cash-coin me-2"></i>Transaksi Baru
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stats-card success border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Transaksi Hari Ini</h6>
                            <h2 class="fw-bold text-primary mb-0"><?php echo $todayStats['total_transactions']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stats-card info border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Penjualan Hari Ini</h6>
                            <h2 class="fw-bold text-success mb-0"><?php echo formatRupiah($todayStats['total_revenue']); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stats-card warning border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Pesanan Pending</h6>
                            <h2 class="fw-bold text-warning mb-0"><?php echo count($pendingOrders); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BARU: Pending Orders Section -->
    <?php if (!empty($pendingOrders)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Pesanan Menunggu Konfirmasi
                </h5>
                <span class="badge bg-warning"><?php echo count($pendingOrders); ?> Pesanan</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Metode Pembayaran</th>
                            <th>Waktu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingOrders as $order): ?>
                        <tr>
                            <td><code><?php echo $order['transaction_code']; ?></code></td>
                            <td><strong><?php echo $order['customer_name']; ?></strong></td>
                            <td class="text-end"><?php echo formatRupiah($order['total_amount']); ?></td>
                            <td><span class="badge bg-info"><?php echo ucfirst($order['payment_method']); ?></span></td>
                            <td><?php echo timeAgo($order['created_at']); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="approve_order.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-outline-success" title="Approve">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                    <a href="reject_order.php?id=<?php echo $order['id']; ?>" 
                                       class="btn btn-outline-danger" title="Tolak">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Riwayat Transaksi Kasir -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Transaksi yang Saya Handle
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($myTransactions)): ?>
                <p class="text-muted text-center py-4">Belum ada transaksi</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myTransactions as $trans): ?>
                        <tr>
                            <td><code><?php echo $trans['transaction_code']; ?></code></td>
                            <td><?php echo $trans['customer_name']; ?></td>
                            <td class="text-end"><?php echo formatRupiah($trans['total_amount']); ?></td>
                            <td><?php echo statusBadge($trans['status']); ?></td>
                            <td><?php echo formatDateTime($trans['created_at'], 'd/m/Y H:i'); ?></td>
                            <td>
                                <a href="print_receipt.php?id=<?php echo $trans['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-printer"></i> Cetak
                                </a>
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

<?php include '../includes/footer.php'; ?>
