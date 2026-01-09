<?php
// admin/products.php
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Kelola Produk';

$productClass = new Product();

// Get filter
$categoryFilter = get('category', '');
$searchQuery = get('search', '');

// Get products
if ($searchQuery) {
    $products = $productClass->searchProducts($searchQuery);
} elseif ($categoryFilter) {
    $products = $productClass->getProductsByCategory($categoryFilter);
} else {
    $products = $productClass->getAllProducts();
}

// Get categories
$categories = $productClass->getAllCategories();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Kelola Produk</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Produk</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="add_product.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <!-- Filter & Search -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari produk..." value="<?php echo $searchQuery; ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="GET" action="">
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
                <div class="col-md-5 text-end">
                    <?php if ($searchQuery || $categoryFilter): ?>
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Reset Filter
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="searchTable">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Stok</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada produk ditemukan
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo getImageUrl($product['image']); ?>" 
                                             alt="<?php echo $product['name']; ?>" 
                                             class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo $product['sku']; ?></code></td>
                                <td>
                                    <strong><?php echo $product['name']; ?></strong>
                                    <?php if ($product['description']): ?>
                                    <br><small class="text-muted"><?php echo substr($product['description'], 0, 50); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $product['category_name']; ?></span>
                                </td>
                                <td class="text-end"><?php echo formatRupiah($product['price']); ?></td>
                                <td class="text-center">
                                    <?php if ($product['stock'] < 10): ?>
                                        <span class="badge bg-danger"><?php echo $product['stock']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo statusBadge($product['status']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-outline-danger delete-confirm" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
