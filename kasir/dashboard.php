<?php
// ===================================
// kasir/dashboard.php
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Dashboard Kasir';
$transactionClass = new Transaction();

$today = date('Y-m-d');
$todayStats = $transactionClass->getStatistics([
    'date_from' => $today,
    'date_to' => $today,
    'kasir_id' => $_SESSION['user_id']
]);

$recentTransactions = $transactionClass->getAllTransactions([
    'kasir_id' => $_SESSION['user_id'],
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
    
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card stats-card success border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Transaksi Hari Ini</h5>
                    <h2 class="fw-bold text-primary"><?php echo $todayStats['total_transactions']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stats-card info border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Penjualan Hari Ini</h5>
                    <h2 class="fw-bold text-success"><?php echo formatRupiah($todayStats['total_revenue']); ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Riwayat Transaksi Terbaru</h5>
        </div>
        <div class="card-body">
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
                        <?php foreach ($recentTransactions as $trans): ?>
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
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
