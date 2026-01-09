<?php
// classes/Product.php
// Class untuk manage products

class Product {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all products
    public function getAllProducts($status = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE p.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get product by ID
    public function getProductById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id 
                LIMIT 1";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    // Get products by category
    public function getProductsByCategory($categoryId) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :category_id 
                AND p.status = 'available'
                ORDER BY p.name ASC";
        return $this->db->fetchAll($sql, [':category_id' => $categoryId]);
    }
    
    // Search products
    public function searchProducts($query) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.name LIKE :query OR p.description LIKE :query OR p.sku LIKE :query)
                AND p.status = 'available'
                ORDER BY p.name ASC";
        
        $searchQuery = '%' . $query . '%';
        return $this->db->fetchAll($sql, [':query' => $searchQuery]);
    }
    
    // Create product
    public function createProduct($data) {
        try {
            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSKU();
            }
            
            // Set default image if not provided
            if (empty($data['image'])) {
                $data['image'] = '';
            }
            
            $productId = $this->db->insert('products', $data);
            
            if ($productId) {
                return [
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan',
                    'product_id' => $productId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan produk'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Update product
    public function updateProduct($id, $data) {
        try {
            $result = $this->db->update('products', $data, 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Produk berhasil diupdate'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate produk'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Delete product
    public function deleteProduct($id) {
        try {
            $result = $this->db->delete('products', 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus produk'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Update stock
    public function updateStock($productId, $quantity, $operation = 'subtract') {
        try {
            // Get current stock
            $product = $this->getProductById($productId);
            
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ];
            }
            
            $currentStock = $product['stock'];
            $newStock = 0;
            
            if ($operation === 'subtract') {
                if ($currentStock < $quantity) {
                    return [
                        'success' => false,
                        'message' => 'Stok tidak mencukupi'
                    ];
                }
                $newStock = $currentStock - $quantity;
            } else {
                $newStock = $currentStock + $quantity;
            }
            
            // Update stock
            $result = $this->db->update('products',
                ['stock' => $newStock],
                'id = :id',
                [':id' => $productId]
            );
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Stok berhasil diupdate',
                    'new_stock' => $newStock
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate stok'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Get low stock products
    public function getLowStockProducts($threshold = 10) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.stock <= :threshold 
                AND p.status = 'available'
                ORDER BY p.stock ASC";
        
        return $this->db->fetchAll($sql, [':threshold' => $threshold]);
    }
    
    // Get total products
    public function getTotalProducts() {
        return $this->db->count('products');
    }
    
    // Generate SKU
    private function generateSKU() {
        $prefix = 'PRD';
        $timestamp = time();
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . '-' . $timestamp . '-' . $random;
    }
    
    // === CATEGORY METHODS ===
    
    // Get all categories
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }
    
    // Get category by ID
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    // Create category
    public function createCategory($data) {
        try {
            $categoryId = $this->db->insert('categories', $data);
            
            if ($categoryId) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil ditambahkan',
                    'category_id' => $categoryId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan kategori'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Update category
    public function updateCategory($id, $data) {
        try {
            $result = $this->db->update('categories', $data, 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil diupdate'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate kategori'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Delete category
    public function deleteCategory($id) {
        try {
            // Check if category has products
            $productCount = $this->db->count('products', 'category_id = :id', [':id' => $id]);
            
            if ($productCount > 0) {
                return [
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori yang masih memiliki produk'
                ];
            }
            
            $result = $this->db->delete('categories', 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus kategori'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Get products count by category
    public function getProductsCountByCategory() {
        $sql = "SELECT c.id, c.name, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id
                GROUP BY c.id, c.name
                ORDER BY c.name ASC";
        
        return $this->db->fetchAll($sql);
    }
}
?>
