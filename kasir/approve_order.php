<?php
// ===================================
// kasir/approve_order.php - BARU
// Proses approval atau reject pesanan
// ===================================
require_once '../config/config.php';
requireRole('kasir');

if (!isPost()) {
    redirect('/kasir/dashboard.php');
}

$transactionClass = new Transaction();
$orderId = post('id');
$action = post('action');
$paymentConfirmed = post('payment_confirmed');
$kasirNotes = post('kasir_notes', '');

if (!$orderId) {
    setFlashMessage('Order ID tidak valid', 'danger');
    redirect('/kasir/dashboard.php');
}

// Get order
$order = $transactionClass->getTransactionById($orderId);

if (!$order) {
    setFlashMessage('Pesanan tidak ditemukan', 'danger');
    redirect('/kasir/dashboard.php');
}

// Cek status
if ($order['status'] != 'pending') {
    setFlashMessage('Pesanan ini sudah diproses sebelumnya', 'warning');
    redirect('/kasir/dashboard.php');
}

try {
    if ($action == 'approve') {
        // Validasi pembayaran
        if ($paymentConfirmed != 'yes') {
            setFlashMessage('Harap konfirmasi pembayaran sudah diterima!', 'warning');
            redirect('/kasir/view_order.php?id=' . $orderId);
        }
        
        // Approve pesanan
        $db = new Database();
        
        // Update transaction
        $updateData = [
            'status' => 'completed',
            'kasir_id' => $_SESSION['user_id'],
            'notes' => $order['notes'] . "\n\nKasir Notes: " . $kasirNotes
        ];
        
        $result = $db->update('transactions', $updateData, 'id = :id', [':id' => $orderId]);
        
        if ($result) {
            setFlashMessage('Pesanan berhasil disetujui! Kode: ' . $order['transaction_code'], 'success');
            redirect('/kasir/print_receipt.php?id=' . $orderId);
        } else {
            throw new Exception('Gagal mengupdate status pesanan');
        }
        
    } elseif ($action == 'reject') {
        // Reject pesanan - kembalikan stok
        $result = $transactionClass->cancelTransaction($orderId);
        
        if ($result['success']) {
            // Update notes
            $db = new Database();
            $updateData = [
                'notes' => $order['notes'] . "\n\nRejected by Kasir: " . $kasirNotes
            ];
            $db->update('transactions', $updateData, 'id = :id', [':id' => $orderId]);
            
            setFlashMessage('Pesanan ditolak dan stok sudah dikembalikan', 'info');
            redirect('/kasir/dashboard.php');
        } else {
            throw new Exception($result['message']);
        }
    }
    
} catch (Exception $e) {
    error_log("Approve Order Error: " . $e->getMessage());
    setFlashMessage('Error: ' . $e->getMessage(), 'danger');
    redirect('/kasir/view_order.php?id=' . $orderId);
}
?>
