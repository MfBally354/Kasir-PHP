<?php
// ===================================
// admin/process_cancel_request.php
// Proses approve/reject dari admin
// ===================================
require_once '../config/config.php';
requireRole('admin');

if (!isPost()) {
    redirect('/admin/cancel_requests.php');
}

$requestId = post('request_id');
$action = post('action');
$adminNotes = post('admin_notes', '');

if (!$requestId || !in_array($action, ['approve', 'reject'])) {
    setFlashMessage('Data tidak valid!', 'danger');
    redirect('/admin/cancel_requests.php');
}

$db = new Database();
$transactionClass = new Transaction();

try {
    // Get request data
    $sql = "SELECT cr.*, t.transaction_code 
            FROM cancellation_requests cr
            JOIN transactions t ON cr.transaction_id = t.id
            WHERE cr.id = :id AND cr.status = 'pending'
            LIMIT 1";
    
    $request = $db->fetch($sql, [':id' => $requestId]);
    
    if (!$request) {
        throw new Exception('Request tidak ditemukan atau sudah diproses');
    }
    
    if ($action == 'approve') {
        // APPROVE - Cancel transaction
        $cancelResult = $transactionClass->cancelTransaction($request['transaction_id']);
        
        if (!$cancelResult['success']) {
            throw new Exception('Gagal membatalkan transaksi: ' . $cancelResult['message']);
        }
        
        // Update request status
        $updateData = [
            'status' => 'approved',
            'approved_by' => $_SESSION['user_id'],
            'admin_notes' => $adminNotes,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $db->update('cancellation_requests', $updateData, 'id = :id', [':id' => $requestId]);
        
        setFlashMessage(
            'Request pembatalan APPROVED! Transaksi ' . $request['transaction_code'] . ' telah dibatalkan dan stok dikembalikan.',
            'success'
        );
        
    } elseif ($action == 'reject') {
        // REJECT - hanya update status request
        if (empty($adminNotes)) {
            throw new Exception('Alasan penolakan harus diisi!');
        }
        
        $updateData = [
            'status' => 'rejected',
            'approved_by' => $_SESSION['user_id'],
            'admin_notes' => $adminNotes,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $db->update('cancellation_requests', $updateData, 'id = :id', [':id' => $requestId]);
        
        setFlashMessage(
            'Request pembatalan REJECTED. Transaksi ' . $request['transaction_code'] . ' tetap completed.',
            'info'
        );
    }
    
} catch (Exception $e) {
    error_log("Process Cancel Request Error: " . $e->getMessage());
    setFlashMessage('Error: ' . $e->getMessage(), 'danger');
}

redirect('/admin/cancel_requests.php');
?>
