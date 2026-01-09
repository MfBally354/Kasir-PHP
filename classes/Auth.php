<?php
// ========================================
// classes/Auth.php - LENGKAP
// ========================================
?>
<?php
// classes/Auth.php
// Class untuk handle authentication (login, register, logout)

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Register user baru (untuk client/pembeli)
    public function register($username, $password, $full_name, $email, $phone = '', $address = '') {
        // Cek apakah username sudah ada
        if ($this->userExists($username, $email)) {
            return [
                'success' => false,
                'message' => 'Username atau email sudah terdaftar'
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Data untuk insert
        $data = [
            'username' => $username,
            'password' => $hashedPassword,
            'full_name' => $full_name,
            'email' => $email,
            'role' => 'client',
            'phone' => $phone,
            'address' => $address,
            'status' => 'active'
        ];
        
        // Insert ke database
        $userId = $this->db->insert('users', $data);
        
        if ($userId) {
            return [
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan login.',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Registrasi gagal. Silakan coba lagi.'
            ];
        }
    }
    
    // Login user
    public function login($username, $password) {
        // Cari user berdasarkan username
        $sql = "SELECT * FROM users WHERE username = :username AND status = 'active' LIMIT 1";
        $user = $this->db->fetch($sql, [':username' => $username]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan atau akun tidak aktif'
            ];
        }
        
        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Password salah'
            ];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        return [
            'success' => true,
            'message' => 'Login berhasil',
            'role' => $user['role']
        ];
    }
    
    // Logout user
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    // Cek apakah user sudah login
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Get user yang sedang login
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, [':id' => $_SESSION['user_id']]);
    }
    
    // Cek apakah username atau email sudah ada
    private function userExists($username, $email) {
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1";
        $result = $this->db->fetch($sql, [
            ':username' => $username,
            ':email' => $email
        ]);
        
        return $result ? true : false;
    }
    
    // Update profile
    public function updateProfile($userId, $data) {
        // Jangan update password dan role lewat method ini
        unset($data['password']);
        unset($data['role']);
        
        $result = $this->db->update('users', $data, 'id = :id', [':id' => $userId]);
        
        if ($result) {
            // Update session jika ada perubahan
            if (isset($data['full_name'])) {
                $_SESSION['full_name'] = $data['full_name'];
            }
            if (isset($data['email'])) {
                $_SESSION['email'] = $data['email'];
            }
            
            return [
                'success' => true,
                'message' => 'Profile berhasil diupdate'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Update profile gagal'
            ];
        }
    }
    
    // Change password
    public function changePassword($userId, $oldPassword, $newPassword) {
        // Get user data
        $user = $this->db->fetch("SELECT * FROM users WHERE id = :id", [':id' => $userId]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan'
            ];
        }
        
        // Verify old password
        if (!password_verify($oldPassword, $user['password'])) {
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
    }
}
?>
