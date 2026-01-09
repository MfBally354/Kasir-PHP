<?php
// ===================================
// client/products.php
// ===================================
require_once '../config/config.php';
requireRole('client');

$pageTitle = 'Produk';
$productClass = new Product();

$categoryFilter = get('category', '');
$searchQuery = get('search', '');

if ($searchQuery) {
    $products = $productClass->searchProducts($searchQuery);
} elseif ($categoryFilter) {
    $products = $productClass->getProductsByCategory($categoryFilter);
} else {
    $products = $productClass->getAllProducts('available');
}

$categories = $productClass->getAllCategories();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Katalog Produk</h2>
            <p class="text-muted">Temukan produk yang Anda butuhkan</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Cari produk..." value="<?php echo $searchQuery; ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-3">
            <form method="GET">
                <select class="form-select" name="category" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" 
                            <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    
    <div class="row g-3">
        <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                Tidak ada produk ditemukan
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
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
                        <span class="badge bg-secondary mb-2"><?php echo $product['category_name']; ?></span>
                        <h6 class="card-title"><?php echo $product['name']; ?></h6>
                        <p class="card-text small text-muted"><?php echo substr($product['description'], 0, 60); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-primary fw-bold"><?php echo formatRupiah($product['price']); ?></span>
                            <span class="badge bg-success">Stok: <?php echo $product['stock']; ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <form action="cart.php" method="POST" class="d-grid">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
