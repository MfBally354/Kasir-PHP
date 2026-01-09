<?php
// ===================================
// client/cart.php
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Keranjang Belanja';
$db = new Database();
$productClass = new Product();

// Handle actions
if (isPost()) {
    $action = post('action');
    $productId = post('product_id');
    $userId = $_SESSION['user_id'];
    
    if ($action == 'add') {
        // Check if already in cart
        $existing = $db->fetch("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id", [
            ':user_id' => $userId,
            ':product_id' => $productId
        ]);
        
        if ($existing) {
            // Update quantity
            $db->update('cart', 
                ['quantity' => $existing['quantity'] + 1], 
                'id = :id', 
                [':id' => $existing['id']]
            );
        } else {
            // Add new
            $db->insert('cart', [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
        
        setFlashMessage('Produk ditambahkan ke keranjang', 'success');
    } elseif ($action == 'update') {
        $cartId = post('cart_id');
        $quantity = post('quantity');
        
        if ($quantity > 0) {
            $db->update('cart', 
                ['quantity' => $quantity], 
                'id = :id', 
                [':id' => $cartId]
            );
        }
    } elseif ($action == 'remove') {
        $cartId = post('cart_id');
        $db->delete('cart', 'id = :id', [':id' => $cartId]);
        setFlashMessage('Item dihapus dari keranjang', 'info');
    }
    
    redirect('/client/cart.php');
}

// Get cart items
$sql = "SELECT c.*, p.name, p.price, p.stock, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id";
$cartItems = $db->fetchAll($sql, [':user_id' => $_SESSION['user_id']]);

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Keranjang Belanja</h2>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <?php if (empty($cartItems)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">Keranjang Kosong</h4>
            <p class="text-muted">Belum ada produk di keranjang Anda</p>
            <a href="products.php" class="btn btn-primary mt-3">
                <i class="bi bi-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item row align-items-center py-3">
                        <div class="col-md-2">
                            <?php if ($item['image']): ?>
                            <img src="<?php echo getImageUrl($item['image']); ?>" 
                                 class="img-fluid rounded" alt="<?php echo $item['name']; ?>">
                            <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="height: 80px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                            <p class="text-primary fw-bold mb-0"><?php echo formatRupiah($item['price']); ?></p>
                            <small class="text-muted">Stok: <?php echo $item['stock']; ?></small>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <div class="input-group input-group-sm">
                                    <button type="submit" class="btn btn-outline-secondary" 
                                            onclick="this.form.quantity.value = Math.max(1, parseInt(this.form.quantity.value) - 1)">-</button>
                                    <input type="number" name="quantity" class="form-control text-center" 
                                           value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" 
                                           onchange="this.form.submit()" style="max-width: 60px;">
                                    <button type="submit" class="btn btn-outline-secondary" 
                                            onclick="this.form.quantity.value = Math.min(<?php echo $item['stock']; ?>, parseInt(this.form.quantity.value) + 1)">+</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong><?php echo formatRupiah($item['price'] * $item['quantity']); ?></strong>
                        </div>
                        <div class="col-md-1 text-end">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Belanja</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong><?php echo formatRupiah($total); ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total</h5>
                        <h4 class="text-success fw-bold"><?php echo formatRupiah($total); ?></h4>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="checkout.php" class="btn btn-success btn-lg">
                            <i class="bi bi-credit-card me-2"></i>Checkout
                        </a>
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
