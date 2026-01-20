<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Test Koneksi Database</h2>";
echo "<hr>";

// Config
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'kasir_db';

echo "<h3>📝 Konfigurasi:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>User:</strong> $user</li>";
echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($pass)) . "</li>";
echo "<li><strong>Database:</strong> $db</li>";
echo "</ul>";

echo "<hr>";

// Test 1: Koneksi dasar
echo "<h3>Test 1: Koneksi PDO</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ <strong>KONEKSI BERHASIL!</strong></p>";
    
    // Test 2: Query tables
    echo "<h3>Test 2: Daftar Tabel</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Test 3: Count users
    echo "<h3>Test 3: Data Users</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Jumlah users: <strong>" . $result['total'] . "</strong></p>";
    
    // Show users
    $stmt = $pdo->query("SELECT id, username, full_name, role FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th></tr>";
    foreach ($users as $user_data) {
        echo "<tr>";
        echo "<td>" . $user_data['id'] . "</td>";
        echo "<td>" . $user_data['username'] . "</td>";
        echo "<td>" . $user_data['full_name'] . "</td>";
        echo "<td>" . $user_data['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>🎉 SEMUA TEST PASSED!</h2>";
    echo "<p><a href='index.php'>➡️ Coba Akses Website Utama</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>KONEKSI GAGAL!</strong></p>";
    echo "<p><strong>Error Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    echo "<hr>";
    echo "<h3>💡 Solusi:</h3>";
    echo "<ol>";
    echo "<li>Cek MySQL berjalan: <code>sudo systemctl status mysql</code></li>";
    echo "<li>Cek user access: <code>mysql -u iqbal -p -h 127.0.0.1</code></li>";
    echo "<li>Cek password benar: <code>password123</code></li>";
    echo "</ol>";
}
?>
