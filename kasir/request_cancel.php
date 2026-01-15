<?php
// ===================================
// kasir/request_cancel.php
// Kasir request pembatalan ke admin
// ===================================
require_once '../config/config.php';
requireRole('kasir');

if (!isPost()) {
    redirect('/kasir/history.php');
}

$transactionId = post('transaction_id');
$cancelReason = post('cancel_reason');

if (!$transactionId || empty($cancelReason)) {
    setFlashMessage('Data tidak lengkap!', 'danger');
    redirect('/kasir/history.php');
}

$transactionClass = new Transaction();
$transaction = $transactionClass->getTransactionById($transactionId);

if (!$transaction) {
    setFlashMessage('Transaksi tidak ditemukan', 'danger');
    redirect('/kasir/history.php');
}

// Cek apakah transaksi ini dibuat oleh kasir yang login
if ($transaction['kasir_id'] != $_SESSION['user_id']) {
    setFlashMessage('Anda tidak berhak membatalkan transaksi ini!', 'danger');
    redirect('/kasir/history.php');
}

// Cek status - hanya bisa request cancel untuk completed
if ($transaction['status'] != 'completed') {
    setFlashMessage('Hanya transaksi completed yang bisa direquest pembatalan', 'warning');
    redirect('/kasir/history.php');
}

try {
    $db = new Database();
    
    // Insert ke tabel cancellation_requests
    $requestData = [
        'transaction_id' => $transactionId,
        'requested_by' => $_SESSION['user_id'],
        'reason' => $cancelReason,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $requestId = $db->insert('cancellation_requests', $requestData);
    
    if ($requestId) {
        setFlashMessage(
            'Request pembatalan berhasil dikirim ke admin. Kode: ' . $transaction['transaction_code'],
            'success'
        );
    } else {
        throw new Exception('Gagal mengirim request pembatalan');
    }
    
} catch (Exception $e) {
    error_log("Request Cancel Error: " . $e->getMessage());
    setFlashMessage('Error: ' . $e->getMessage(), 'danger');
}

redirect('/kasir/history.php');
?>
