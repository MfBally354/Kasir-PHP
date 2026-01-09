<?php
// admin/add_product.php
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Tambah Produk';

$productClass = new Product();
$categories = $productClass->getAllCategories();

// Process form submission
if (isPost()) {
    $name = post('name');
    $categoryId = post('category_id');
    $description = post('description');
    $price = post('price');
    $stock = post('stock');
    $sku = post('sku');
    $status = post('status');
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            $imageName = $upload['filename'];
        } else {
            setFlashMessage($upload['message'], 'danger');
            redirect('/admin/add_product.php');
        }
    }
    
    // Prepare data
    $data = [
        'name' => $name,
        'category_id' => $categoryId,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'sku' => $sku,
        'status' => $status,
        'image' => $imageName
    ];
    
    // Create product
    $result = $productClass->createProduct($data);
    
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
            <h2 class="fw-bold">Tambah Produk</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
                    <li class="breadcrumb-item active">Tambah Produk</li>
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
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="price" required min="0" step="0.01">
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" required min="0">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="sku" placeholder="Kosongkan untuk auto">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control image-upload" name="image" 
                                       accept="image/*" data-preview="imagePreview">
                                <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <img id="imagePreview" src="" alt="" class="img-thumbnail" 
                                 style="max-width: 200px; display: none;">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Produk
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>Informasi
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Isi semua field yang bertanda <span class="text-danger">*</span>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            SKU akan digenerate otomatis jika dikosongkan
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Gambar produk opsional
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview
$('.image-upload').on('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include '../includes/footer.php'; ?>
