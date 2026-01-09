-- Database: kasir_db.sql
-- Sistem Kasir - Database Structure

-- Buat database
CREATE DATABASE IF NOT EXISTS kasir_db;
USE kasir_db;

-- Tabel users (untuk Admin, Kasir, dan Client/Pembeli)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'kasir', 'client') NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel categories (kategori produk)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel products (produk yang dijual)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    sku VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_sku (sku),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel transactions (transaksi utama)
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    kasir_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) DEFAULT 0,
    payment_method ENUM('cash', 'debit', 'credit', 'ewallet') NOT NULL,
    transaction_type ENUM('kasir', 'client') NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kasir_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_transaction_code (transaction_code),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel transaction_details (detail item transaksi)
CREATE TABLE transaction_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel cart (keranjang belanja untuk client)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data admin default (password: admin123)
INSERT INTO users (username, password, full_name, email, role, phone, address) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@kasir.com', 'admin', '081234567890', 'Jalan Admin No. 1');

-- Insert data kasir contoh (password: kasir123)
INSERT INTO users (username, password, full_name, email, role, phone, address) VALUES
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Satu', 'kasir1@kasir.com', 'kasir', '081234567891', 'Jalan Kasir No. 1');

-- Insert kategori contoh
INSERT INTO categories (name, description) VALUES
('Makanan', 'Kategori produk makanan'),
('Minuman', 'Kategori produk minuman'),
('Snack', 'Kategori produk snack'),
('Elektronik', 'Kategori produk elektronik'),
('ATK', 'Kategori alat tulis kantor');

-- Insert produk contoh
INSERT INTO products (category_id, name, description, price, stock, sku, status) VALUES
(1, 'Nasi Goreng', 'Nasi goreng spesial dengan telur', 15000.00, 100, 'FD001', 'available'),
(1, 'Mie Ayam', 'Mie ayam dengan bakso', 12000.00, 100, 'FD002', 'available'),
(2, 'Es Teh Manis', 'Es teh manis segar', 5000.00, 200, 'DR001', 'available'),
(2, 'Kopi Hitam', 'Kopi hitam original', 8000.00, 150, 'DR002', 'available'),
(3, 'Chitato', 'Keripik kentang rasa sapi panggang', 10000.00, 50, 'SN001', 'available'),
(3, 'Oreo', 'Biskuit coklat dengan krim vanilla', 12000.00, 50, 'SN002', 'available'),
(4, 'Power Bank', 'Power bank 10000mAh', 150000.00, 20, 'EL001', 'available'),
(5, 'Pulpen Pilot', 'Pulpen hitam pilot', 3000.00, 100, 'AT001', 'available');
