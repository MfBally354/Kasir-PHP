<?php
require_once 'config/config.php';

echo "<h2>🔐 Test Login System</h2>";
echo "<hr>";

// Test data
$test_username = 'admin';
$test_password = 'admin123';

echo "<h3>Test Credentials:</h3>";
echo "<ul>";
echo "<li>Username: <strong>$test_username</strong></li>";
echo "<li>Password: <strong>$test_password</strong></li>";
echo "</ul>";

// Instance Auth class
$auth = new Auth();

echo "<h3>Test 1: Cek User Exists</h3>";
$sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
$db = new Database();
$user = $db->fetch($sql, [':username' => $test_username]);

if ($user) {
    echo "<p style='color: green;'>✅ User ditemukan</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
    echo "<tr><td>Username</td><td>" . $user['username'] . "</td></tr>";
    echo "<tr><td>Full Name</td><td>" . $user['full_name'] . "</td></tr>";
    echo "<tr><td>Role</td><td>" . $user['role'] . "</td></tr>";
    echo "<tr><td>Status</td><td>" . $user['status'] . "</td></tr>";
    echo "<tr><td>Password Hash</td><td>" . substr($user['password'], 0, 30) . "...</td></tr>";
    echo "</table>";
    
    echo "<h3>Test 2: Verify Password</h3>";
    $password_check = password_verify($test_password, $user['password']);
    
    if ($password_check) {
        echo "<p style='color: green;'>✅ Password COCOK!</p>";
        
        echo "<h3>Test 3: Login via Auth Class</h3>";
        $result = $auth->login($test_username, $test_password);
        
        if ($result['success']) {
            echo "<p style='color: green;'>✅ LOGIN BERHASIL!</p>";
            echo "<p>Role: " . $result['role'] . "</p>";
            echo "<p>Session ID: " . session_id() . "</p>";
            echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
            
            echo "<hr>";
            echo "<h2 style='color: green;'>🎉 SEMUA TEST PASSED!</h2>";
            echo "<p><a href='admin/dashboard.php'>➡️ Ke Admin Dashboard</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Login gagal: " . $result['message'] . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Password TIDAK COCOK!</p>";
        echo "<p>Hash di database mungkin salah.</p>";
        
        echo "<h4>Generate Hash Baru:</h4>";
        $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
        echo "<p>Hash untuk password '$test_password':</p>";
        echo "<textarea style='width: 100%; height: 60px;'>$new_hash</textarea>";
        echo "<p>Copy hash di atas, lalu jalankan SQL:</p>";
        echo "<code>UPDATE users SET password = '$new_hash' WHERE username = '$test_username';</code>";
    }
    
} else {
    echo "<p style='color: red;'>❌ User tidak ditemukan!</p>";
}
?>
