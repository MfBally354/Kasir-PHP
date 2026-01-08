<?php
// classes/Transaction.php
// Class untuk manage transactions

class Transaction {
    private $db;
    private $productClass;
    
    public function __construct() {
        $this->db = new Database();
        $this->productClass = new Product();
    }
    
    // Create transaction
    public function createTransaction($data, $items) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
            
            // Generate transaction code
            $data['transaction_code'] = generateTransactionCode();
            
            // Insert transaction
            $transactionId = $this->db->insert('transactions', $data);
            
            if (!$transactionId) {
                throw new Exception('Gagal membuat transaksi');
            }
            
            // Insert transaction details dan update stock
            foreach ($items as $item) {
                $detailData = [
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ];
                
                $detailId = $this->db->insert('transaction_details', $detailData);
                
                if (!$detailId) {
                    throw new Exception('Gagal menyimpan detail transaksi');
                }
                
                // Update stock produk
                $updateStock = $this->productClass->updateStock(
                    $item['product_id'], 
                    $item['quantity'], 
                    'subtract'
                );
                
                if (!$updateStock['success']) {
                    throw new Exception($updateStock['message']);
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Transaksi berhasil',
                'transaction_id' => $transactionId,
                'transaction_code' => $data['transaction_code']
            ];
            
        } catch (Exception $e) {
            // Rollback jika ada error
            $this->db->rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get transaction by ID
    public function getTransactionById($id) {
        $sql = "SELECT t.*, u.full_name as customer_name, k.full_name as kasir_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users k ON t.kasir_id = k.id
                WHERE t.id = :id
                LIMIT 1";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    // Get transaction details
    public function getTransactionDetails($transactionId) {
        $sql = "SELECT td.*, p.image
                FROM transaction_details td
                LEFT JOIN products p ON td.product_id = p.id
                WHERE td.transaction_id = :transaction_id";
        return $this->db->fetchAll($sql, [':transaction_id' => $transactionId]);
    }
    
    // Get all transactions
    public function getAllTransactions($filters = []) {
        $sql = "SELECT t.*, u.full_name as customer_name, k.full_name as kasir_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN users k ON t.kasir_id = k.id
                WHERE 1=1";
        
        $params = [];
        
        // Filter by status
        if (isset($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filter by transaction type
        if (isset($filters['transaction_type'])) {
            $sql .= " AND t.transaction_type = :transaction_type";
            $params[':transaction_type'] = $filters['transaction_type'];
        }
        
        // Filter by user
        if (isset($filters['user_id'])) {
            $sql .= " AND t.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        
        // Filter by kasir
        if (isset($filters['kasir_id'])) {
            $sql .= " AND t.kasir_id = :kasir_id";
            $params[':kasir_id'] = $filters['kasir_id'];
        }
        
        // Filter by date
        if (isset($filters['date_from'])) {
            $sql .= " AND DATE(t.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $sql .= " AND DATE(t.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        // Limit
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Update transaction status
    public function updateStatus($id, $status) {
        $result = $this->db->update('transactions', 
            ['status' => $status], 
            'id = :id', 
            [':id' => $id]
        );
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Status transaksi berhasil diupdate'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal mengupdate status transaksi'
            ];
        }
    }
    
    // Cancel transaction
    public function cancelTransaction($id) {
        try {
            $this->db->beginTransaction();
            
            // Get transaction
            $transaction = $this->getTransactionById($id);
            
            if (!$transaction) {
                throw new Exception('Transaksi tidak ditemukan');
            }
            
            if ($transaction['status'] == 'cancelled') {
                throw new Exception('Transaksi sudah dibatalkan');
            }
            
            // Get transaction details
            $details = $this->getTransactionDetails($id);
            
            // Restore stock
            foreach ($details as $detail) {
                $updateStock = $this->productClass->updateStock(
                    $detail['product_id'], 
                    $detail['quantity'], 
                    'add'
                );
                
                if (!$updateStock['success']) {
                    throw new Exception('Gagal mengembalikan stok: ' . $updateStock['message']);
                }
            }
            
            // Update status
            $result = $this->db->update('transactions', 
                ['status' => 'cancelled'], 
                'id = :id', 
                [':id' => $id]
            );
            
            if (!$result) {
                throw new Exception('Gagal mengupdate status transaksi');
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get transaction statistics
    public function getStatistics($filters = []) {
        $where = "WHERE status = 'completed'";
        $params = [];
        
        if (isset($filters['date_from'])) {
            $where .= " AND DATE(created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $where .= " AND DATE(created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        // Total transaksi
        $totalTransactions = $this->db->count('transactions', $where, $params);
        
        // Total pendapatan
        $sql = "SELECT SUM(total_amount) as total_revenue FROM transactions $where";
        $revenue = $this->db->fetch($sql, $params);
        
        return [
            'total_transactions' => $totalTransactions,
            'total_revenue' => $revenue['total_revenue'] ?? 0
        ];
    }
    
    // Get daily sales report
    public function getDailySalesReport($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $sql = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_transaction,
                    payment_method,
                    COUNT(*) as method_count
                FROM transactions
                WHERE DATE(created_at) = :date AND status = 'completed'
                GROUP BY payment_method";
        
        return $this->db->fetchAll($sql, [':date' => $date]);
    }
    
    // Get best selling products
    public function getBestSellingProducts($limit = 10, $filters = []) {
        $where = "WHERE t.status = 'completed'";
        $params = [];
        
        if (isset($filters['date_from'])) {
            $where .= " AND DATE(t.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $where .= " AND DATE(t.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        $sql = "SELECT 
                    td.product_name,
                    p.image,
                    SUM(td.quantity) as total_sold,
                    SUM(td.subtotal) as total_revenue
                FROM transaction_details td
                JOIN transactions t ON td.transaction_id = t.id
                LEFT JOIN products p ON td.product_id = p.id
                $where
                GROUP BY td.product_id, td.product_name, p.image
                ORDER BY total_sold DESC
                LIMIT $limit";
        
        return $this->db->fetchAll($sql, $params);
    }
}
?>
