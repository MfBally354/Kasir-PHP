<?php
// ===================================
// admin/cancel_requests.php - FIXED VERSION
// REMOVED auto-refresh to prevent flickering
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
            <!-- Manual Refresh Button -->
            <button onclick="location.reload()" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise me-2"></i>Refresh Data
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
                                    <button type="button" class="btn btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#approveModal<?php echo $request['id']; ?>">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal<?php echo $request['id']; ?>">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </div>
                                
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal<?php echo $request['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Approve Pembatalan</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_cancel_request.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    
                                                    <div class="alert alert-info">
                                                        <strong>Transaksi:</strong> <?php echo $request['transaction_code']; ?><br>
                                                        <strong>Total:</strong> <?php echo formatRupiah($request['total_amount']); ?><br>
                                                        <strong>Alasan:</strong> <?php echo htmlspecialchars($request['reason']); ?>
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
                                
                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal<?php echo $request['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Reject Pembatalan</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_cancel_request.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    
                                                    <div class="alert alert-warning">
                                                        <strong>Transaksi:</strong> <?php echo $request['transaction_code']; ?><br>
                                                        <strong>Alasan Kasir:</strong> <?php echo htmlspecialchars($request['reason']); ?>
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

<!-- REMOVED: Auto-refresh script yang menyebabkan flickering -->
<!-- User sekarang bisa manual refresh dengan tombol -->

<?php include '../includes/footer.php'; ?>
