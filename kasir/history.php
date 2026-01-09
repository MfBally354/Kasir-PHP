<?php
// ===================================
// kasir/history.php
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Riwayat Transaksi';

$transactionClass = new Transaction();
$dateFilter = get('date', date('Y-m-d'));

$transactions = $transactionClass->getAllTransactions([
    'kasir_id' => $_SESSION['user_id'],
    'date_from' => $dateFilter,
    'date_to' => $dateFilter
]);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Riwayat Transaksi</h2>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <form method="GET">
                        <div class="input-group">
                            <input type="date" class="form-control" name="date" value="<?php echo $dateFilter; ?>">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada transaksi pada tanggal ini
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $trans): ?>
                            <tr>
                                <td><code><?php echo $trans['transaction_code']; ?></code></td>
                                <td><?php echo $trans['customer_name']; ?></td>
                                <td class="text-end"><?php echo formatRupiah($trans['total_amount']); ?></td>
                                <td><span class="badge bg-info"><?php echo ucfirst($trans['payment_method']); ?></span></td>
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
