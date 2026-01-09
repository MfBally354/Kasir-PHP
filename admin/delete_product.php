<!-- ===================================== -->
<!-- admin/delete_product.php -->
<!-- ===================================== -->

<?php
// admin/delete_product.php
require_once '../config/config.php';
requireRole('admin');

$id = get('id');
if (!$id) {
    redirect('/admin/products.php');
}

$productClass = new Product();
$product = $productClass->getProductById($id);

if (!$product) {
    setFlashMessage('Produk tidak ditemukan', 'danger');
    redirect('/admin/products.php');
}

// Delete image if exists
if ($product['image']) {
    deleteImage($product['image']);
}

// Delete product
$result = $productClass->deleteProduct($id);

if ($result['success']) {
    setFlashMessage($result['message'], 'success');
} else {
    setFlashMessage($result['message'], 'danger');
}

redirect('/admin/products.php');
?>
