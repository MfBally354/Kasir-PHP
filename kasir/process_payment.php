<?php
// ===================================
// kasir/process_payment.php - FIXED VERSION
// ===================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
requireRole('kasir');

// LOG untuk debugging
error_log("=== PROCESS PAYMENT START ===");
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
$cartData = post('cart_data'); // Masih string JSON

error_log("Raw Cart Data String: " . $cartData);
error_log("Cart Data Length: " . strlen($cartData));

// ========================================
// FIX: Decode JSON dengan error handling
// ========================================
$cart = json_decode($cartData, true);
$jsonError = json_last_error();

error_log("JSON Decode Error Code: " . $jsonError);

if ($jsonError !== JSON_ERROR_NONE) {
    error_log("JSON ERROR: " . json_last_error_msg());
    
    // Try to fix common issues
    // 1. Remove extra slashes
    $cartData = stripslashes($cartData);
    error_log("After stripslashes: " . $cartData);
    
    // 2. Decode again
    $cart = json_decode($cartData, true);
    $jsonError = json_last_error();
    
    if ($jsonError !== JSON_ERROR_NONE) {
        error_log("Still JSON ERROR after stripslashes: " . json_last_error_msg());
        setFlashMessage('Error decoding cart data: ' . json_last_error_msg() . ' | Data: ' . substr($cartData, 0, 100), 'danger');
        redirect('/kasir/transaction.php');
    }
}

error_log("Decoded Cart: " . print_r($cart, true));
error_log("Cart is array: " . (is_array($cart) ? 'YES' : 'NO'));
error_log("Cart count: " . (is_array($cart) ? count($cart) : '0'));

// ========================================
// Validasi cart
// ========================================
if (!is_array($cart) || empty($cart)) {
    error_log("ERROR: Cart is empty or not array!");
    error_log("Cart variable type: " . gettype($cart));
    error_log("Cart variable value: " . var_export($cart, true));
    
    setFlashMessage('Keranjang kosong! Silakan tambah produk terlebih dahulu. Debug: ' . gettype($cart), 'danger');
    redirect('/kasir/transaction.php');
}

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

// Create customer (gunakan kasir sebagai customer untuk walk-in)
$userId = $_SESSION['user_id'];

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
    // Validasi item
    if (!isset($item['id']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
        error_log("ERROR: Invalid item structure: " . print_r($item, true));
        setFlashMessage('Item tidak valid dalam keranjang!', 'danger');
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
