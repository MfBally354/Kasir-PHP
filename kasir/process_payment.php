<?php
// ===================================
// kasir/process_payment.php
// ===================================
require_once '../config/config.php';
requireRole('kasir');

if (!isPost()) {
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
$cartData = post('cart_data');

// Decode cart data
$cart = json_decode($cartData, true);

if (empty($cart)) {
    setFlashMessage('Keranjang kosong!', 'danger');
    redirect('/kasir/transaction.php');
}

// Create temporary customer if needed
$userId = $_SESSION['user_id']; // Default to kasir as customer

// If customer name provided, check if exists or create
if ($customerName && $customerName != 'Walk-in Customer') {
    // For simplicity, use kasir ID. In real app, you might want to create temp customer
    $userId = $_SESSION['user_id'];
}

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

// Prepare items
$items = [];
foreach ($cart as $item) {
    $items[] = [
        'product_id' => $item['id'],
        'product_name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'subtotal' => $item['price'] * $item['quantity']
    ];
}

// Create transaction
$result = $transactionClass->createTransaction($transactionData, $items);

if ($result['success']) {
    setFlashMessage('Transaksi berhasil! Kode: ' . $result['transaction_code'], 'success');
    redirect('/kasir/print_receipt.php?id=' . $result['transaction_id']);
} else {
    setFlashMessage($result['message'], 'danger');
    redirect('/kasir/transaction.php');
}
