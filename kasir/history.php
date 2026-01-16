<?php
// ===================================
// kasir/history.php - FIXED VERSION
// Removed all auto-refresh and fixed modal flickering
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Riwayat Transaksi';

$transactionClass = new Transaction();
$db = new Database();
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
        <div class="col-auto">
            <!-- Manual Refresh Button -->
            <button onclick="window.location.reload()" class="btn btn-outline-primary me-2">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
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
                            <th class="text-center">Aksi</th>
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
                            <?php
                            // Cek apakah ada pending request untuk transaksi ini
                            $hasPendingRequest = $db->fetch(
                                "SELECT id FROM cancellation_requests 
                                 WHERE transaction_id = :tid AND status = 'pending' LIMIT 1",
                                [':tid' => $trans['id']]
                            );
                            ?>
                            <tr>
                                <td><code><?php echo $trans['transaction_code']; ?></code></td>
                                <td><?php echo $trans['customer_name']; ?></td>
                                <td class="text-end"><?php echo formatRupiah($trans['total_amount']); ?></td>
                                <td><span class="badge bg-info"><?php echo ucfirst($trans['payment_method']); ?></span></td>
                                <td><?php echo statusBadge($trans['status']); ?></td>
                                <td><?php echo formatDateTime($trans['created_at'], 'd/m/Y H:i'); ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="print_receipt.php?id=<?php echo $trans['id']; ?>" 
                                           class="btn btn-outline-primary" target="_blank" title="Cetak">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        
                                        <?php if ($trans['status'] == 'completed'): ?>
                                            <?php if ($hasPendingRequest): ?>
                                                <button class="btn btn-warning" disabled title="Request pending">
                                                    <i class="bi bi-hourglass-split"></i> Pending
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-danger btn-cancel-request" 
                                                        data-transaction-id="<?php echo $trans['id']; ?>"
                                                        data-transaction-code="<?php echo $trans['transaction_code']; ?>"
                                                        data-total="<?php echo formatRupiah($trans['total_amount']); ?>"
                                                        title="Request Batal">
                                                    <i class="bi bi-x-circle"></i> Batal
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
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

<!-- SINGLE Cancel Request Modal (Reusable) -->
<div class="modal fade" id="cancelModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Request Pembatalan Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="request_cancel.php" method="POST" id="cancelForm">
                <div class="modal-body">
                    <input type="hidden" name="transaction_id" id="modalTransactionId">
                    
                    <div class="alert alert-warning">
                        <strong><i class="bi bi-exclamation-triangle me-2"></i>Perhatian!</strong><br>
                        Request pembatalan akan dikirim ke admin untuk persetujuan.
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Transaksi:</label>
                        <p class="mb-0"><strong id="modalTransactionCode"></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Total:</label>
                        <p class="mb-0"><strong id="modalTotal"></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="cancel_reason" rows="4" 
                                  placeholder="Jelaskan alasan pembatalan (contoh: customer komplain, salah input, dll)" 
                                  required></textarea>
                        <small class="text-muted">Alasan ini akan dilihat oleh admin</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-send me-2"></i>Kirim Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// IMPORTANT: Wrapped in DOMContentLoaded to prevent multiple bindings
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 History page loaded - Modal script initialized');
    
    // Get modal element
    const cancelModal = document.getElementById('cancelModal');
    const bsModal = new bootstrap.Modal(cancelModal, {
        backdrop: 'static',
        keyboard: false
    });
    
    // Get all cancel buttons
    const cancelButtons = document.querySelectorAll('.btn-cancel-request');
    
    console.log(`Found ${cancelButtons.length} cancel buttons`);
    
    // Add click event to each button
    cancelButtons.forEach(function(button) {
        // Remove any existing listeners first
        button.removeEventListener('click', handleCancelClick);
        
        // Add new listener
        button.addEventListener('click', handleCancelClick);
    });
    
    function handleCancelClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('🔴 Cancel button clicked');
        
        const transactionId = this.getAttribute('data-transaction-id');
        const transactionCode = this.getAttribute('data-transaction-code');
        const total = this.getAttribute('data-total');
        
        console.log('Transaction:', transactionCode, 'ID:', transactionId);
        
        // Populate modal
        document.getElementById('modalTransactionId').value = transactionId;
        document.getElementById('modalTransactionCode').textContent = transactionCode;
        document.getElementById('modalTotal').textContent = total;
        
        // Clear textarea
        document.querySelector('textarea[name="cancel_reason"]').value = '';
        
        // Show modal
        bsModal.show();
    }
    
    // Handle modal close - clear form
    cancelModal.addEventListener('hidden.bs.modal', function() {
        console.log('Modal closed');
        document.getElementById('cancelForm').reset();
    });
    
    // Prevent form double submit
    const cancelForm = document.getElementById('cancelForm');
    cancelForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
    });
    
    console.log('✅ Modal events attached successfully');
});

// Prevent any auto-refresh
console.log('⚠️  Auto-refresh DISABLED');
</script>

<?php include '../includes/footer.php'; ?>
