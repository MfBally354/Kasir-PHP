<?php
// ===================================
// client/checkout.php
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Checkout';
$db = new Database();

// Get cart items
$sql = "SELECT c.*, p.name, p.price, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id";
$cartItems = $db->fetchAll($sql, [':user_id' => $_SESSION['user_id']]);

if (empty($cartItems)) {
    setFlashMessage('Keranjang kosong!', 'warning');
    redirect('/client/cart.php');
}

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process checkout
if (isPost()) {
    $paymentMethod = post('payment_method');
    $notes = post('notes');
    
    $transactionClass = new Transaction();
    
    $transactionData = [
        'user_id' => $_SESSION['user_id'],
        'kasir_id' => null,
        'total_amount' => $total,
        'payment_amount' => $total,
        'change_amount' => 0,
        'payment_method' => $paymentMethod,
        'transaction_type' => 'client',
        'status' => 'pending',
        'notes' => $notes
    ];
    
    $items = [];
    foreach ($cartItems as $item) {
        $items[] = [
            'product_id' => $item['product_id'],
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'subtotal' => $item['price'] * $item['quantity']
        ];
    }
    
    $result = $transactionClass->createTransaction($transactionData, $items);
    
    if ($result['success']) {
        // Clear cart
        $db->delete('cart', 'user_id = :user_id', [':user_id' => $_SESSION['user_id']]);
        
        setFlashMessage('Pesanan berhasil dibuat! Kode: ' . $result['transaction_code'], 'success');
        redirect('/client/order_detail.php?id=' . $result['transaction_id']);
    } else {
        setFlashMessage($result['message'], 'danger');
    }
}

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Checkout</h2>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> x <?php echo formatRupiah($item['price']); ?></small>
                        </div>
                        <strong><?php echo formatRupiah($item['price'] * $item['quantity']); ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method" required>
                                <option value="">Pilih Metode</option>
                                <option value="cash">Cash</option>
                                <option value="debit">Debit Card</option>
                                <option value="credit">Credit Card</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Tambahkan catatan untuk pesanan Anda"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Buat Pesanan
                            </button>
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Keranjang
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Total Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span><?php echo formatRupiah($total); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Total</h5>
                        <h4 class="text-success fw-bold"><?php echo formatRupiah($total); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
