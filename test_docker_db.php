<?php
echo "<h2>🧪 Test Database Connection in Docker</h2>";
echo "<hr>";

// Detect environment
$isDocker = getenv('APACHE_DOCUMENT_ROOT') !== false || file_exists('/.dockerenv');
echo "<p><strong>Environment:</strong> " . ($isDocker ? 'Docker' : 'PHP Native') . "</p>";

// Include config
require_once 'config/database.php';

echo "<p><strong>DB Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>DB Name:</strong> " . DB_NAME . "</p>";
echo "<p><strong>DB User:</strong> " . DB_USER . "</p>";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3 style='color: green;'>✅ Connection Successful!</h3>";
    
    // Test query
    $stmt = $pdo->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>Users in Database:</h4>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Username</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>{$user['username']}</td><td>{$user['role']}</td></tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Connection Failed!</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
