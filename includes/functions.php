<?php
// includes/functions.php
// Helper functions untuk aplikasi

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validasi email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Format tanggal Indonesia
function formatDate($date, $format = 'd-m-Y') {
    return date($format, strtotime($date));
}

// Format tanggal dan waktu Indonesia
function formatDateTime($datetime, $format = 'd-m-Y H:i:s') {
    return date($format, strtotime($datetime));
}

// Upload gambar
function uploadImage($file, $targetDir = UPLOAD_PATH) {
    // Validasi file
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return [
            'success' => false,
            'message' => 'Tidak ada file yang diupload'
        ];
    }
    
    // Cek error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'message' => 'Error saat upload file'
        ];
    }
    
    // Validasi tipe file
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF'
        ];
    }
    
    // Validasi ukuran file (max 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        return [
            'success' => false,
            'message' => 'Ukuran file terlalu besar. Maksimal 2MB'
        ];
    }
    
    // Generate nama file unik
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '_' . time() . '.' . $extension;
    $targetFile = $targetDir . $fileName;
    
    // Pastikan direktori ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return [
            'success' => true,
            'message' => 'File berhasil diupload',
            'filename' => $fileName,
            'filepath' => $targetFile
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Gagal mengupload file'
        ];
    }
}

// Delete gambar
function deleteImage($filename, $targetDir = UPLOAD_PATH) {
    $filepath = $targetDir . $filename;
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

// Get image URL
function getImageUrl($filename) {
    if (empty($filename)) {
        return UPLOAD_URL . 'default.jpg';
    }
    
    return UPLOAD_URL . $filename;
}

// Alert message (untuk tampilan Bootstrap)
function showAlert($message, $type = 'info') {
    $alertClass = 'alert-' . $type;
    echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
            $message
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

// Set flash message
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Get dan hapus flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Display flash message
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        showAlert($flash['message'], $flash['type']);
    }
}

// Pagination helper
function paginate($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => ($currentPage - 1) * $itemsPerPage
    ];
}

// Generate pagination HTML
function generatePagination($totalPages, $currentPage, $url) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav><ul class="pagination">';
    
    // Previous button
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= "<li class='page-item'><a class='page-link' href='$url?page=$prevPage'>Previous</a></li>";
    } else {
        $html .= "<li class='page-item disabled'><span class='page-link'>Previous</span></li>";
    }
    
    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $html .= "<li class='page-item $active'><a class='page-link' href='$url?page=$i'>$i</a></li>";
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= "<li class='page-item'><a class='page-link' href='$url?page=$nextPage'>Next</a></li>";
    } else {
        $html .= "<li class='page-item disabled'><span class='page-link'>Next</span></li>";
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

// Check if request is POST
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Check if request is GET
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// Get POST data
function post($key, $default = null) {
    return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
}

// Get GET data
function get($key, $default = null) {
    return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
}

// Debug helper
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

// Print r helper
function pr($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

// Time ago helper
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return $diff . ' detik yang lalu';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' menit yang lalu';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' jam yang lalu';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' hari yang lalu';
    } else {
        return date('d M Y', $time);
    }
}

// Status badge helper
function statusBadge($status) {
    $badges = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'completed' => 'success',
        'cancelled' => 'danger',
        'available' => 'success',
        'unavailable' => 'secondary'
    ];
    
    $badgeClass = isset($badges[$status]) ? $badges[$status] : 'secondary';
    $statusText = ucfirst($status);
    
    return "<span class='badge bg-$badgeClass'>$statusText</span>";
}

// Role badge helper
function roleBadge($role) {
    $badges = [
        'admin' => 'danger',
        'kasir' => 'primary',
        'client' => 'info'
    ];
    
    $badgeClass = isset($badges[$role]) ? $badges[$role] : 'secondary';
    $roleText = ucfirst($role);
    
    return "<span class='badge bg-$badgeClass'>$roleText</span>";
}



?>
