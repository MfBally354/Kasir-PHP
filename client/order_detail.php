<?php
// ===================================
// client/order_detail.php - FIXED
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Detail Pesanan';
$transactionClass = new Transaction();

$id = get('id');
if (!$id) {
    redirect('/client/my_orders.php');
}

$order = $transactionClass->getTransactionById($id);
$details = $transactionClass->getTransactionDetails($id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    setFlashMessage('Pesanan tidak ditemukan', 'danger');
    redirect('/client/my_orders.php');
}

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Detail Pesanan</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="my_orders.php">Pesanan Saya</a></li>
                    <li class="breadcrumb-item active"><?php echo $order['transaction_code']; ?></li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="my_orders.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Order Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Kode Pesanan</small>
                            <h6><strong><?php echo $order['transaction_code']; ?></strong></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Status</small>
                            <h6><?php echo statusBadge($order['status']); ?></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Tanggal Pesanan</small>
                            <h6><?php echo formatDateTime($order['created_at'], 'd F Y, H:i'); ?></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Metode Pembayaran</small>
                            <h6><span class="badge bg-info"><?php echo ucfirst($order['payment_method']); ?></span></h6>
                        </div>
                    </div>
                    
                    <?php if ($order['notes']): ?>
                    <hr>
                    <div>
                        <small class="text-muted">Catatan</small>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['image']): ?>
                                            <img src="<?php echo getImageUrl($item['image']); ?>" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                            <strong><?php echo $item['product_name']; ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo formatRupiah($item['price']); ?></td>
                                    <td class="text-end"><strong><?php echo formatRupiah($item['subtotal']); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Payment Summary -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span><?php echo formatRupiah($order['total_amount']); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Total</h5>
                        <h4 class="text-success fw-bold mb-0"><?php echo formatRupiah($order['total_amount']); ?></h4>
                    </div>
                    
                    <?php if ($order['status'] == 'pending'): ?>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="bi bi-clock-history me-2"></i>
                            Menunggu Konfirmasi Kasir
                        </h6>
                        <hr>
                        <p class="mb-2"><strong>Langkah Selanjutnya:</strong></p>
                        <ol class="mb-2 ps-3">
                            <li>Tunjukkan <strong>Kode Pesanan</strong> ke kasir</li>
                            <li>Lakukan pembayaran via <strong><?php echo ucfirst($order['payment_method']); ?></strong></li>
                            <li>Tunggu konfirmasi dari kasir</li>
                        </ol>
                        <div class="bg-white p-2 rounded text-center">
                            <small class="text-muted d-block">Kode Pesanan Anda:</small>
                            <h4 class="mb-0 fw-bold text-dark"><?php echo $order['transaction_code']; ?></h4>
                        </div>
                    </div>
                    <?php elseif ($order['status'] == 'completed'): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Pesanan Selesai</strong><br>
                        <small>Terima kasih atas pesanan Anda!</small>
                    </div>
                    <?php elseif ($order['status'] == 'cancelled'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Pesanan Dibatalkan</strong><br>
                        <small>Stok sudah dikembalikan</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <?php if ($order['status'] == 'completed'): ?>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer me-2"></i>Cetak Invoice
                        </button>
                        <?php endif; ?>
                        
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="bi bi-bag me-2"></i>Belanja Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .navbar, .breadcrumb, .btn, footer {
        display: none !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>
