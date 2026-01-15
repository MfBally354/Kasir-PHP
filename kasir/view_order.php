<?php
// ===================================
// kasir/view_order.php - BARU
// Halaman detail pesanan untuk approval
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Detail Pesanan';
$transactionClass = new Transaction();

$id = get('id');
if (!$id) {
    redirect('/kasir/dashboard.php');
}

$order = $transactionClass->getTransactionById($id);
$details = $transactionClass->getTransactionDetails($id);

if (!$order) {
    setFlashMessage('Pesanan tidak ditemukan', 'danger');
    redirect('/kasir/dashboard.php');
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
                    <li class="breadcrumb-item active">Detail Pesanan</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Order Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Pesanan</h5>
                    <?php echo statusBadge($order['status']); ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Kode Pesanan</label>
                            <h6><strong><?php echo $order['transaction_code']; ?></strong></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Customer</label>
                            <h6><?php echo $order['customer_name']; ?></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Waktu Pesanan</label>
                            <h6><?php echo formatDateTime($order['created_at'], 'd F Y, H:i'); ?></h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Metode Pembayaran</label>
                            <h6><span class="badge bg-info"><?php echo ucfirst($order['payment_method']); ?></span></h6>
                        </div>
                    </div>
                    
                    <?php if ($order['notes']): ?>
                    <hr>
                    <div>
                        <label class="text-muted small">Catatan Customer</label>
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
                                    <td class="text-center"><span class="badge bg-secondary"><?php echo $item['quantity']; ?></span></td>
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
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Menunggu Konfirmasi</strong><br>
                        <small>Pesanan ini menunggu persetujuan kasir</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions -->
            <?php if ($order['status'] == 'pending'): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <form action="approve_order.php" method="POST" id="approveForm">
                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Pembayaran</label>
                            <select class="form-select" name="payment_confirmed" required>
                                <option value="">Pilih Status</option>
                                <option value="yes">✅ Pembayaran Sudah Diterima</option>
                                <option value="no">❌ Pembayaran Belum Diterima</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan Kasir (Opsional)</label>
                            <textarea class="form-control" name="kasir_notes" rows="3" 
                                      placeholder="Tambahkan catatan jika diperlukan"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" 
                                    class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Setujui Pesanan
                            </button>
                            
                            <button type="submit" name="action" value="reject" 
                                    class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin menolak pesanan ini?')">
                                <i class="bi bi-x-circle me-2"></i>Tolak Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <?php if ($order['status'] == 'completed'): ?>
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">Pesanan Selesai</h5>
                        <p class="text-muted">Pesanan ini sudah diproses</p>
                        <a href="print_receipt.php?id=<?php echo $order['id']; ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="bi bi-printer me-2"></i>Cetak Struk
                        </a>
                    <?php elseif ($order['status'] == 'cancelled'): ?>
                        <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">Pesanan Dibatalkan</h5>
                        <p class="text-muted">Pesanan ini telah dibatalkan</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
