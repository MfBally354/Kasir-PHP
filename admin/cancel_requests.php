<?php
// ===================================
// admin/cancel_requests.php - ANTI-FLICKER VERSION
// NO auto-refresh, stable modal handling
// ===================================
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Request Pembatalan Transaksi';

$db = new Database();

// Get pending requests
$sql = "SELECT cr.*, 
               t.transaction_code, t.total_amount, t.created_at as transaction_date,
               u.full_name as kasir_name
        FROM cancellation_requests cr
        JOIN transactions t ON cr.transaction_id = t.id
        JOIN users u ON cr.requested_by = u.id
        WHERE cr.status = 'pending'
        ORDER BY cr.created_at DESC";

$pendingRequests = $db->fetchAll($sql);

// Get history (approved/rejected)
$sqlHistory = "SELECT cr.*, 
                      t.transaction_code, t.total_amount,
                      u.full_name as kasir_name,
                      a.full_name as admin_name
               FROM cancellation_requests cr
               JOIN transactions t ON cr.transaction_id = t.id
               JOIN users u ON cr.requested_by = u.id
               LEFT JOIN users a ON cr.approved_by = a.id
               WHERE cr.status IN ('approved', 'rejected')
               ORDER BY cr.updated_at DESC
               LIMIT 20";

$history = $db->fetchAll($sqlHistory);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Request Pembatalan Transaksi</h2>
            <p class="text-muted">Approve atau reject request pembatalan dari kasir</p>
        </div>
        <div class="col-auto">
            <button onclick="location.reload()" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh Manual
            </button>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <!-- Pending Requests -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Request Menunggu Persetujuan
                </h5>
                <span class="badge bg-warning"><?php echo count($pendingRequests); ?> Request</span>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($pendingRequests)): ?>
            <p class="text-muted text-center py-4">Tidak ada request pembatalan yang menunggu</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Kasir</th>
                            <th class="text-end">Total</th>
                            <th>Tanggal Transaksi</th>
                            <th>Alasan Pembatalan</th>
                            <th>Request Time</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $request): ?>
                        <tr>
                            <td><code><?php echo $request['transaction_code']; ?></code></td>
                            <td><strong><?php echo $request['kasir_name']; ?></strong></td>
                            <td class="text-end"><?php echo formatRupiah($request['total_amount']); ?></td>
                            <td><?php echo formatDateTime($request['transaction_date'], 'd/m/Y H:i'); ?></td>
                            <td>
                                <small><?php echo nl2br(htmlspecialchars($request['reason'])); ?></small>
                            </td>
                            <td><?php echo timeAgo($request['created_at']); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success btn-approve-request" 
                                            data-request-id="<?php echo $request['id']; ?>"
                                            data-transaction-code="<?php echo htmlspecialchars($request['transaction_code']); ?>"
                                            data-total="<?php echo formatRupiah($request['total_amount']); ?>"
                                            data-reason="<?php echo htmlspecialchars($request['reason']); ?>">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-reject-request" 
                                            data-request-id="<?php echo $request['id']; ?>"
                                            data-transaction-code="<?php echo htmlspecialchars($request['transaction_code']); ?>"
                                            data-reason="<?php echo htmlspecialchars($request['reason']); ?>">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Riwayat Request
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($history)): ?>
            <p class="text-muted text-center py-4">Belum ada riwayat</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th>Diproses Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $item): ?>
                        <tr>
                            <td><code><?php echo $item['transaction_code']; ?></code></td>
                            <td><?php echo $item['kasir_name']; ?></td>
                            <td>
                                <?php if ($item['status'] == 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $item['admin_name'] ?? '-'; ?></td>
                            <td><?php echo formatDateTime($item['updated_at'], 'd/m/Y H:i'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- SINGLE Approve Modal (Reusable) -->
<div class="modal fade" id="approveModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Pembatalan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_cancel_request.php" method="POST" id="approveForm">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="approveRequestId">
                    <input type="hidden" name="action" value="approve">
                    
                    <div class="alert alert-info">
                        <strong>Transaksi:</strong> <span id="approveTransactionCode"></span><br>
                        <strong>Total:</strong> <span id="approveTotal"></span><br>
                        <strong>Alasan:</strong> <span id="approveReason"></span>
                    </div>
                    
                    <p class="mb-3">Dengan menyetujui pembatalan ini:</p>
                    <ul>
                        <li>Transaksi akan dibatalkan</li>
                        <li>Stok produk akan dikembalikan</li>
                        <li>Data transaksi tetap tersimpan</li>
                    </ul>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin (Opsional)</label>
                        <textarea class="form-control" name="admin_notes" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Approve Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SINGLE Reject Modal (Reusable) -->
<div class="modal fade" id="rejectModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Pembatalan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_cancel_request.php" method="POST" id="rejectForm">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="rejectRequestId">
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="alert alert-warning">
                        <strong>Transaksi:</strong> <span id="rejectTransactionCode"></span><br>
                        <strong>Alasan Kasir:</strong> <span id="rejectReason"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_notes" rows="3" 
                                  placeholder="Jelaskan mengapa request ditolak" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ========================================
// ANTI-FLICKER MODAL HANDLER
// ========================================
(function() {
    'use strict';
    
    console.log('🔧 Cancel Requests page loaded - Anti-flicker mode');
    
    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModals);
    } else {
        initModals();
    }
    
    function initModals() {
        console.log('🚀 Initializing modals...');
        
        // Get modal instances (singleton)
        const approveModalEl = document.getElementById('approveModal');
        const rejectModalEl = document.getElementById('rejectModal');
        
        if (!approveModalEl || !rejectModalEl) {
            console.error('❌ Modal elements not found!');
            return;
        }
        
        const approveModal = new bootstrap.Modal(approveModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        
        const rejectModal = new bootstrap.Modal(rejectModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        
        // Approve buttons
        const approveButtons = document.querySelectorAll('.btn-approve-request');
        console.log(`Found ${approveButtons.length} approve buttons`);
        
        approveButtons.forEach(btn => {
            // Remove old listeners
            btn.replaceWith(btn.cloneNode(true));
        });
        
        // Re-get buttons after clone
        document.querySelectorAll('.btn-approve-request').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const requestId = this.getAttribute('data-request-id');
                const transactionCode = this.getAttribute('data-transaction-code');
                const total = this.getAttribute('data-total');
                const reason = this.getAttribute('data-reason');
                
                console.log('✅ Approve clicked:', transactionCode);
                
                // Populate modal
                document.getElementById('approveRequestId').value = requestId;
                document.getElementById('approveTransactionCode').textContent = transactionCode;
                document.getElementById('approveTotal').textContent = total;
                document.getElementById('approveReason').textContent = reason;
                document.querySelector('#approveForm textarea[name="admin_notes"]').value = '';
                
                // Show modal
                approveModal.show();
            });
        });
        
        // Reject buttons
        const rejectButtons = document.querySelectorAll('.btn-reject-request');
        console.log(`Found ${rejectButtons.length} reject buttons`);
        
        rejectButtons.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        
        document.querySelectorAll('.btn-reject-request').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const requestId = this.getAttribute('data-request-id');
                const transactionCode = this.getAttribute('data-transaction-code');
                const reason = this.getAttribute('data-reason');
                
                console.log('❌ Reject clicked:', transactionCode);
                
                // Populate modal
                document.getElementById('rejectRequestId').value = requestId;
                document.getElementById('rejectTransactionCode').textContent = transactionCode;
                document.getElementById('rejectReason').textContent = reason;
                document.querySelector('#rejectForm textarea[name="admin_notes"]').value = '';
                
                // Show modal
                rejectModal.show();
            });
        });
        
        // Form submit handlers
        document.getElementById('approveForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        });
        
        document.getElementById('rejectForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        });
        
        // Clean up on modal close
        approveModalEl.addEventListener('hidden.bs.modal', function() {
            document.getElementById('approveForm').reset();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Approve Pembatalan';
        });
        
        rejectModalEl.addEventListener('hidden.bs.modal', function() {
            document.getElementById('rejectForm').reset();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Reject Request';
        });
        
        console.log('✅ All modal handlers initialized');
    }
})();

// CRITICAL: No auto-refresh
console.log('⚠️  Auto-refresh DISABLED - Use manual refresh button');
</script>

<?php include '../includes/footer.php'; ?>
