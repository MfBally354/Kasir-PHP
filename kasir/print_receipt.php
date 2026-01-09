<?php

// ===================================
// kasir/print_receipt.php
// ===================================
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Struk Pembayaran';

$id = get('id');
if (!$id) {
    redirect('/kasir/dashboard.php');
}

$transactionClass = new Transaction();
$transaction = $transactionClass->getTransactionById($id);
$details = $transactionClass->getTransactionDetails($id);

if (!$transaction) {
    setFlashMessage('Transaksi tidak ditemukan', 'danger');
    redirect('/kasir/dashboard.php');
}

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-end mb-3 no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer me-2"></i>Print Struk
                </button>
                <a href="transaction.php" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Transaksi Baru
                </a>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-2"></i>Dashboard
                </a>
            </div>
            
            <div class="card receipt border-0 shadow-sm">
                <div class="card-body" id="receiptContent">
                    <div class="receipt-header text-center mb-4">
                        <h3 class="mb-0"><?php echo APP_NAME; ?></h3>
                        <p class="mb-0">Jl. Raya No. 123, Kota</p>
                        <p class="mb-0">Telp: 021-12345678</p>
                        <hr>
                    </div>
                    
                    <div class="mb-3">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150">No. Transaksi</td>
                                <td><strong><?php echo $transaction['transaction_code']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td><?php echo formatDateTime($transaction['created_at'], 'd/m/Y H:i'); ?></td>
                            </tr>
                            <tr>
                                <td>Kasir</td>
                                <td><?php echo $transaction['kasir_name']; ?></td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td><?php echo $transaction['customer_name']; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $item): ?>
                            <tr>
                                <td><?php echo $item['product_name']; ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end"><?php echo formatRupiah($item['price']); ?></td>
                                <td class="text-end"><?php echo formatRupiah($item['subtotal']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <hr>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="70%"><strong>TOTAL</strong></td>
                            <td class="text-end"><strong><?php echo formatRupiah($transaction['total_amount']); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Bayar (<?php echo ucfirst($transaction['payment_method']); ?>)</td>
                            <td class="text-end"><?php echo formatRupiah($transaction['payment_amount']); ?></td>
                        </tr>
                        <tr>
                            <td>Kembalian</td>
                            <td class="text-end"><?php echo formatRupiah($transaction['change_amount']); ?></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0"><strong>TERIMA KASIH</strong></p>
                        <p class="mb-0">Atas kunjungan Anda</p>
                        <small class="text-muted">www.sistemkasir.com</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
