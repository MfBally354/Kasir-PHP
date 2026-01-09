<?php
// ===================================
// admin/reports.php
// ===================================
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Laporan Penjualan';
$transactionClass = new Transaction();

// Filter
$dateFrom = get('date_from', date('Y-m-01'));
$dateTo = get('date_to', date('Y-m-d'));

$filters = [
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'status' => 'completed'
];

$transactions = $transactionClass->getAllTransactions($filters);
$stats = $transactionClass->getStatistics($filters);
$bestSelling = $transactionClass->getBestSellingProducts(10, $filters);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Laporan Penjualan</h2>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="date_from" value="<?php echo $dateFrom; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="date_to" value="<?php echo $dateTo; ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5>Total Transaksi</h5>
                    <h2 class="text-primary fw-bold"><?php echo $stats['total_transactions']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5>Total Pendapatan</h5>
                    <h2 class="text-success fw-bold"><?php echo formatRupiah($stats['total_revenue']); ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Daftar Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Kasir</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trans): ?>
                        <tr>
                            <td><code><?php echo $trans['transaction_code']; ?></code></td>
                            <td><?php echo $trans['customer_name']; ?></td>
                            <td><?php echo $trans['kasir_name'] ?? 'Online'; ?></td>
                            <td class="text-end"><?php echo formatRupiah($trans['total_amount']); ?></td>
                            <td><span class="badge bg-info"><?php echo ucfirst($trans['payment_method']); ?></span></td>
                            <td><?php echo formatDateTime($trans['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
