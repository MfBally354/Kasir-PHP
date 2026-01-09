<?php
// ===================================
// client/my_orders.php
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Pesanan Saya';
$transactionClass = new Transaction();

$statusFilter = get('status', '');

$filters = [
    'user_id' => $_SESSION['user_id']
];

if ($statusFilter) {
    $filters['status'] = $statusFilter;
}

$orders = $transactionClass->getAllTransactions($filters);

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Pesanan Saya</h2>
            <p class="text-muted">Riwayat pembelian Anda</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <form method="GET">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo $statusFilter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="bi bi-receipt display-1 text-muted"></i>
                <h5 class="mt-3">Belum ada pesanan</h5>
                <p class="text-muted">Mulai belanja dan pesanan Anda akan muncul di sini</p>
                <a href="products.php" class="btn btn-primary mt-3">
                    <i class="bi bi-bag me-2"></i>Mulai Belanja
                </a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo $order['transaction_code']; ?></strong></td>
                            <td><?php echo formatDateTime($order['created_at'], 'd/m/Y H:i'); ?></td>
                            <td class="text-end"><?php echo formatRupiah($order['total_amount']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo ucfirst($order['payment_method']); ?></span>
                            </td>
                            <td><?php echo statusBadge($order['status']); ?></td>
                            <td class="text-center">
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Detail
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
