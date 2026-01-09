<?php
// ===================================
// client/dashboard.php
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Dashboard';
$productClass = new Product();
$transactionClass = new Transaction();

$newProducts = $productClass->getAllProducts('available');
$newProducts = array_slice($newProducts, 0, 8);

$myOrders = $transactionClass->getAllTransactions([
    'user_id' => $_SESSION['user_id'],
    'limit' => 5
]);

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-muted">Selamat datang, <?php echo $_SESSION['full_name']; ?>!</p>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="products.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center p-4 hover-scale">
                    <i class="bi bi-bag fs-1 text-primary mb-3"></i>
                    <h5>Lihat Produk</h5>
                    <p class="text-muted mb-0">Jelajahi katalog produk kami</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="cart.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center p-4 hover-scale">
                    <i class="bi bi-cart3 fs-1 text-success mb-3"></i>
                    <h5>Keranjang Saya</h5>
                    <p class="text-muted mb-0">Lihat item di keranjang</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="my_orders.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center p-4 hover-scale">
                    <i class="bi bi-receipt fs-1 text-info mb-3"></i>
                    <h5>Pesanan Saya</h5>
                    <p class="text-muted mb-0">Riwayat pembelian</p>
                </div>
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Produk Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($newProducts as $product): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="card product-card h-100">
                                <?php if ($product['image']): ?>
                                <img src="<?php echo getImageUrl($product['image']); ?>" 
                                     class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                                <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $product['name']; ?></h6>
                                    <p class="card-text text-primary fw-bold"><?php echo formatRupiah($product['price']); ?></p>
                                    <a href="products.php" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="products.php" class="btn btn-outline-primary">
                            Lihat Semua Produk <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Pesanan Terakhir</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($myOrders)): ?>
                        <p class="text-muted text-center py-4">Belum ada pesanan</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($myOrders as $order): ?>
                            <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo $order['transaction_code']; ?></strong>
                                    <?php echo statusBadge($order['status']); ?>
                                </div>
                                <small class="text-muted"><?php echo formatRupiah($order['total_amount']); ?></small><br>
                                <small class="text-muted"><?php echo timeAgo($order['created_at']); ?></small>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

