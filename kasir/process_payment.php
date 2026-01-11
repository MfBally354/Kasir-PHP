<?php
// ===================================
// kasir/process_payment.php - FIXED
// ===================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
requireRole('kasir');

// LOG untuk debugging
error_log("=== PROCESS PAYMENT DEBUG ===");
error_log("POST data: " . print_r($_POST, true));

if (!isPost()) {
    error_log("ERROR: Not a POST request");
    redirect('/kasir/transaction.php');
}

$transactionClass = new Transaction();
$db = new Database();

// Get form data
$totalAmount = post('total_amount');
$paymentAmount = post('payment_amount');
$changeAmount = post('change_amount');
$paymentMethod = post('payment_method');
$customerName = post('customer_name', 'Walk-in Customer');

// PENTING: Gunakan $_POST langsung untuk cart_data (jangan di-sanitize)
$cartData = isset($_POST['cart_data']) ? $_POST['cart_data'] : '';

error_log("Total Amount: $totalAmount");
error_log("Payment Amount: $paymentAmount");
error_log("Change Amount: $changeAmount");
error_log("Payment Method: $paymentMethod");
error_log("Customer Name: $customerName");
error_log("Cart Data RAW: $cartData");
error_log("Cart Data Type: " . gettype($cartData));
error_log("Cart Data Length: " . strlen($cartData));

// FIX: Cek apakah cart data ada dan tidak kosong
if (empty($cartData) || trim($cartData) === '' || $cartData === '[]') {
    error_log("ERROR: Cart data is empty or invalid");
    setFlashMessage('Keranjang kosong! Silakan tambah produk terlebih dahulu.', 'danger');
    redirect('/kasir/transaction.php');
}

// Decode cart data dengan error handling
$cart = json_decode($cartData, true);
$jsonError = json_last_error();

error_log("JSON Decode Error Code: $jsonError");
error_log("JSON Decode Error Message: " . json_last_error_msg());
error_log("Decoded Cart Type: " . gettype($cart));
error_log("Decoded Cart: " . print_r($cart, true));

// FIX: Cek json_decode error
if ($jsonError !== JSON_ERROR_NONE) {
    error_log("ERROR: JSON decode failed - " . json_last_error_msg());
    setFlashMessage('Error decode data keranjang: ' . json_last_error_msg(), 'danger');
    redirect('/kasir/transaction.php');
}

// FIX: Cek apakah $cart adalah array dan tidak kosong
if (!is_array($cart) || empty($cart)) {
    error_log("ERROR: Cart is not an array or is empty after decode");
    error_log("Cart value: " . var_export($cart, true));
    setFlashMessage('Keranjang kosong atau format data tidak valid!', 'danger');
    redirect('/kasir/transaction.php');
}

error_log("SUCCESS: Cart decoded successfully with " . count($cart) . " items");

// Validasi total amount
if (empty($totalAmount) || $totalAmount <= 0) {
    error_log("ERROR: Invalid total amount: $totalAmount");
    setFlashMessage('Total amount tidak valid!', 'danger');
    redirect('/kasir/transaction.php');
}

// Validasi payment amount
if (empty($paymentAmount) || $paymentAmount < $totalAmount) {
    error_log("ERROR: Payment less than total");
    setFlashMessage('Jumlah pembayaran kurang dari total!', 'danger');
    redirect('/kasir/transaction.php');
}

// Create temporary customer if needed
$userId = $_SESSION['user_id']; // Default to kasir as customer

// Prepare transaction data
$transactionData = [
    'user_id' => $userId,
    'kasir_id' => $_SESSION['user_id'],
    'total_amount' => $totalAmount,
    'payment_amount' => $paymentAmount,
    'change_amount' => $changeAmount,
    'payment_method' => $paymentMethod,
    'transaction_type' => 'kasir',
    'status' => 'completed',
    'notes' => 'Customer: ' . $customerName
];

error_log("Transaction Data: " . print_r($transactionData, true));

// Prepare items
$items = [];
foreach ($cart as $item) {
    // Validasi setiap item
    if (!isset($item['id']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
        error_log("ERROR: Invalid item structure: " . print_r($item, true));
        setFlashMessage('Data produk tidak lengkap!', 'danger');
        redirect('/kasir/transaction.php');
    }
    
    $items[] = [
        'product_id' => $item['id'],
        'product_name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'subtotal' => $item['price'] * $item['quantity']
    ];
}

error_log("Transaction Items Count: " . count($items));
error_log("Transaction Items: " . print_r($items, true));

// Create transaction
try {
    $result = $transactionClass->createTransaction($transactionData, $items);
    
    error_log("Transaction Result: " . print_r($result, true));
    
    if ($result['success']) {
        error_log("SUCCESS: Transaction created with ID: " . $result['transaction_id']);
        setFlashMessage('Transaksi berhasil! Kode: ' . $result['transaction_code'], 'success');
        redirect('/kasir/print_receipt.php?id=' . $result['transaction_id']);
    } else {
        error_log("ERROR: " . $result['message']);
        setFlashMessage($result['message'], 'danger');
        redirect('/kasir/transaction.php');
    }
} catch (Exception $e) {
    error_log("EXCEPTION: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    setFlashMessage('Error: ' . $e->getMessage(), 'danger');
    redirect('/kasir/transaction.php');
}
?>
