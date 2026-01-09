require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Edit Produk';
$productClass = new Product();

$id = get('id');
if (!$id) {
    redirect('/admin/products.php');
}

$product = $productClass->getProductById($id);
if (!$product) {
    setFlashMessage('Produk tidak ditemukan', 'danger');
    redirect('/admin/products.php');
}

$categories = $productClass->getAllCategories();

// Process form
if (isPost()) {
    $data = [
        'name' => post('name'),
        'category_id' => post('category_id'),
        'description' => post('description'),
        'price' => post('price'),
        'stock' => post('stock'),
        'sku' => post('sku'),
        'status' => post('status')
    ];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            // Delete old image
            if ($product['image']) {
                deleteImage($product['image']);
            }
            $data['image'] = $upload['filename'];
        }
    }
    
    $result = $productClass->updateProduct($id, $data);
    
    if ($result['success']) {
        setFlashMessage($result['message'], 'success');
        redirect('/admin/products.php');
    } else {
        setFlashMessage($result['message'], 'danger');
    }
}

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Edit Produk</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
                    <li class="breadcrumb-item active">Edit Produk</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo $product['name']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo $product['description']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="price" 
                                           value="<?php echo $product['price']; ?>" required min="0" step="0.01">
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" 
                                       value="<?php echo $product['stock']; ?>" required min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="sku" 
                                       value="<?php echo $product['sku']; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="available" <?php echo $product['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                    <option value="unavailable" <?php echo $product['status'] == 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                            </div>
                        </div>
                        
                        <?php if ($product['image']): ?>
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini:</label><br>
                            <img src="<?php echo getImageUrl($product['image']); ?>" 
                                 alt="<?php echo $product['name']; ?>" 
                                 class="img-thumbnail" style="max-width: 200px;">
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update Produk
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
