<?php
// classes/User.php
// Class untuk manage users (Admin, Kasir, Client)

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all users
    public function getAllUsers($role = null) {
        $sql = "SELECT * FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = :role";
            $params[':role'] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get user by ID
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    // Get user by username
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, [':username' => $username]);
    }
    
    // Get user by email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, [':email' => $email]);
    }
    
    // Create user
    public function createUser($data) {
        try {
            // Validasi username sudah ada
            $existingUser = $this->getUserByUsername($data['username']);
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Username sudah digunakan'
                ];
            }
            
            // Validasi email sudah ada
            $existingEmail = $this->getUserByEmail($data['email']);
            if ($existingEmail) {
                return [
                    'success' => false,
                    'message' => 'Email sudah terdaftar'
                ];
            }
            
            // Hash password
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            
            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }
            
            $userId = $this->db->insert('users', $data);
            
            if ($userId) {
                return [
                    'success' => true,
                    'message' => 'User berhasil ditambahkan',
                    'user_id' => $userId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan user'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Update user
    public function updateUser($id, $data) {
        try {
            // Cek apakah user ada
            $user = $this->getUserById($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }
            
            // Validasi username jika diubah
            if (isset($data['username']) && $data['username'] != $user['username']) {
                $existingUser = $this->getUserByUsername($data['username']);
                if ($existingUser) {
                    return [
                        'success' => false,
                        'message' => 'Username sudah digunakan'
                    ];
                }
            }
            
            // Validasi email jika diubah
            if (isset($data['email']) && $data['email'] != $user['email']) {
                $existingEmail = $this->getUserByEmail($data['email']);
                if ($existingEmail) {
                    return [
                        'success' => false,
                        'message' => 'Email sudah terdaftar'
                    ];
                }
            }
            
            // Hash password jika ada
            if (isset($data['password'])) {
                if (empty($data['password'])) {
                    unset($data['password']);
                } else {
                    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                }
            }
            
            $result = $this->db->update('users', $data, 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'User berhasil diupdate'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate user'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Delete user
    public function deleteUser($id) {
        try {
            // Cek apakah user ada
            $user = $this->getUserById($id);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }
            
            // Cek apakah user adalah admin terakhir
            if ($user['role'] === 'admin') {
                $adminCount = $this->db->count('users', 'role = :role AND status = :status', [
                    ':role' => 'admin',
                    ':status' => 'active'
                ]);
                
                if ($adminCount <= 1) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menghapus admin terakhir'
                    ];
                }
            }
            
            $result = $this->db->delete('users', 'id = :id', [':id' => $id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus user'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Update user status
    public function updateStatus($id, $status) {
        try {
            $result = $this->db->update('users',
                ['status' => $status],
                'id = :id',
                [':id' => $id]
            );
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Status user berhasil diupdate'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate status user'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Get total users by role
    public function getTotalUsersByRole($role) {
        return $this->db->count('users', 'role = :role AND status = :status', [
            ':role' => $role,
            ':status' => 'active'
        ]);
    }
    
    // Get total users
    public function getTotalUsers() {
        return $this->db->count('users', 'status = :status', [':status' => 'active']);
    }
    
    // Get users statistics
    public function getUsersStatistics() {
        $stats = [
            'total' => $this->getTotalUsers(),
            'admin' => $this->getTotalUsersByRole('admin'),
            'kasir' => $this->getTotalUsersByRole('kasir'),
            'client' => $this->getTotalUsersByRole('client'),
            'inactive' => $this->db->count('users', 'status = :status', [':status' => 'inactive'])
        ];
        
        return $stats;
    }
    
    // Search users
    public function searchUsers($query) {
        $sql = "SELECT * FROM users 
                WHERE username LIKE :query 
                OR full_name LIKE :query 
                OR email LIKE :query 
                ORDER BY created_at DESC";
        
        $searchQuery = '%' . $query . '%';
        return $this->db->fetchAll($sql, [':query' => $searchQuery]);
    }
    
    // Get active kasir
    public function getActiveKasir() {
        $sql = "SELECT * FROM users 
                WHERE role = 'kasir' 
                AND status = 'active' 
                ORDER BY full_name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    // Get recent registered users
    public function getRecentUsers($limit = 10) {
        $sql = "SELECT * FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [':limit' => $limit]);
    }
    
    // Verify password
    public function verifyPassword($userId, $password) {
        $user = $this->getUserById($userId);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
    
    // Change password
    public function changePassword($userId, $oldPassword, $newPassword) {
        try {
            // Verify old password
            if (!$this->verifyPassword($userId, $oldPassword)) {
                return [
                    'success' => false,
                    'message' => 'Password lama salah'
                ];
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            // Update password
            $result = $this->db->update('users',
                ['password' => $hashedPassword],
                'id = :id',
                [':id' => $userId]
            );
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Password berhasil diubah'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengubah password'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Reset password (untuk admin)
    public function resetPassword($userId, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            $result = $this->db->update('users',
                ['password' => $hashedPassword],
                'id = :id',
                [':id' => $userId]
            );
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Password berhasil direset'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mereset password'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
}
?>
