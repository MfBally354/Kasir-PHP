<?php
// kasir/transaction.php - FIXED VERSION
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Transaksi Baru';
$includeCalculator = true;

$productClass = new Product();
$products = $productClass->getAllProducts('available');
$categories = $productClass->getAllCategories();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Transaksi Baru</h2>
            <p class="text-muted">Scan atau pilih produk untuk memulai transaksi</p>
        </div>
        <div class="col-auto">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row g-3">
        <!-- Products List -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm" style="max-height: 80vh; overflow-y: auto;">
                <div class="card-header bg-white sticky-top">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk...">
                        <button class="btn btn-primary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <select class="form-select form-select-sm" id="categoryFilter">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2" id="productList">
                        <?php foreach ($products as $product): ?>
                        <div class="col-6 product-item" data-category="<?php echo $product['category_id']; ?>">
                            <div class="card h-100 cursor-pointer product-card-select" 
                                 data-id="<?php echo $product['id']; ?>"
                                 data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                 data-price="<?php echo $product['price']; ?>"
                                 data-stock="<?php echo $product['stock']; ?>">
                                <div class="card-body p-2 text-center">
                                    <?php if ($product['image']): ?>
                                    <img src="<?php echo getImageUrl($product['image']); ?>" 
                                         class="img-fluid rounded mb-2" 
                                         style="height: 80px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-light rounded mb-2 d-flex align-items-center justify-content-center" 
                                         style="height: 80px;">
                                        <i class="bi bi-image fs-3 text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <h6 class="mb-1 small"><?php echo $product['name']; ?></h6>
                                    <p class="mb-0 text-primary fw-bold small"><?php echo formatRupiah($product['price']); ?></p>
                                    <small class="text-muted">Stok: <?php echo $product['stock']; ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cart & Payment -->
        <div class="col-lg-7">
            <!-- Cart Items -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
                    </h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <table class="table" id="cartTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <tr id="emptyCart">
                                <td colspan="5" class="text-center text-muted py-4">
                                    Keranjang masih kosong. Pilih produk untuk memulai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total:</h5>
                        <h3 class="mb-0 fw-bold text-success total-amount">Rp 0</h3>
                    </div>
                </div>
            </div>
            
            <!-- Payment Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cash-coin me-2"></i>Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" action="process_payment.php" method="POST">
                        <input type="hidden" id="totalAmount" name="total_amount" value="0">
                        <input type="hidden" id="paymentAmount" name="payment_amount" value="0">
                        <input type="hidden" id="changeAmount" name="change_amount" value="0">
                        <input type="hidden" id="cartData" name="cart_data" value="">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="debit">Debit Card</option>
                                    <option value="credit">Credit Card</option>
                                    <option value="ewallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Customer (Opsional)</label>
                                <input type="text" class="form-control" name="customer_name" 
                                       id="customerNameInput" placeholder="Nama customer">
                            </div>
                        </div>
                        
                        <!-- Calculator -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Bayar</label>
                            <div id="calculatorDisplay" class="calculator-display">Rp 0</div>
                        </div>
                        
                        <!-- Calculator Buttons -->
                        <div class="row g-2 mb-3">
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="7">7</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="8">8</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="9">9</button></div>
                            <div class="col-3"><button type="button" class="btn btn-warning calculator-btn w-100" id="calcClear">C</button></div>
                            
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="4">4</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="5">5</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="6">6</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="0">0</button></div>
                            
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="1">1</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="2">2</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="3">3</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary calculator-btn w-100 calc-number" data-value="000">000</button></div>
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div class="row g-2 mb-3">
                            <div class="col-3"><button type="button" class="btn btn-outline-info btn-sm w-100 quick-amount" data-amount="10000">10k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info btn-sm w-100 quick-amount" data-amount="20000">20k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info btn-sm w-100 quick-amount" data-amount="50000">50k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info btn-sm w-100 quick-amount" data-amount="100000">100k</button></div>
                        </div>
                        
                        <button type="button" class="btn btn-primary w-100 mb-2" id="applyPayment">
                            <i class="bi bi-calculator me-2"></i>Hitung Kembalian
                        </button>
                        
                        <div id="changeDisplay" class="alert alert-info" style="display: none;">
                            <strong>Kembalian:</strong> <span id="changeDisplayAmount">Rp 0</span>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitPayment" disabled>
                            <i class="bi bi-check-circle me-2"></i>Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

// Add product to cart
$('.product-card-select').on('click', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const price = parseFloat($(this).data('price'));
    const stock = parseInt($(this).data('stock'));
    
    // Check if already in cart
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        if (existingItem.quantity < stock) {
            existingItem.quantity++;
        } else {
            alert('Stok tidak mencukupi!');
            return;
        }
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1,
            stock: stock
        });
    }
    
    renderCart();
});

// Render cart
function renderCart() {
    const cartItems = $('#cartItems');
    cartItems.empty();
    
    if (cart.length === 0) {
        cartItems.append(`
            <tr id="emptyCart">
                <td colspan="5" class="text-center text-muted py-4">
                    Keranjang masih kosong
                </td>
            </tr>
        `);
        calculateTotal();
        return;
    }
    
    cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        cartItems.append(`
            <tr>
                <td>${item.name}</td>
                <td class="text-center">
                    <div class="input-group input-group-sm" style="width: 120px; margin: 0 auto;">
                        <button class="btn btn-outline-secondary qty-minus" data-index="${index}" type="button">-</button>
                        <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                        <button class="btn btn-outline-secondary qty-plus" data-index="${index}" type="button">+</button>
                    </div>
                </td>
                <td class="text-end">${formatRupiah(item.price)}</td>
                <td class="text-end subtotal">${formatRupiah(subtotal)}</td>
                <td>
                    <button class="btn btn-sm btn-danger remove-item" data-index="${index}" type="button">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    
    calculateTotal();
    bindCartEvents();
}

// Bind cart events
function bindCartEvents() {
    $('.qty-minus').off('click').on('click', function() {
        const index = $(this).data('index');
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
            renderCart();
        }
    });
    
    $('.qty-plus').off('click').on('click', function() {
        const index = $(this).data('index');
        if (cart[index].quantity < cart[index].stock) {
            cart[index].quantity++;
            renderCart();
        } else {
            alert('Stok maksimal: ' + cart[index].stock);
        }
    });
    
    $('.remove-item').off('click').on('click', function() {
        const index = $(this).data('index');
        cart.splice(index, 1);
        renderCart();
    });
}

// Calculate total
function calculateTotal() {
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
    });
    
    $('.total-amount').text(formatRupiah(total));
    $('#totalAmount').val(total);
    return total;
}

// Apply payment
$('#applyPayment').on('click', function() {
    const total = parseFloat($('#totalAmount').val());
    const payment = calculator.getValue();
    
    if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    
    if (payment < total) {
        alert('Jumlah pembayaran kurang dari total!');
        return;
    }
    
    const change = payment - total;
    
    $('#paymentAmount').val(payment);
    $('#changeAmount').val(change);
    $('#changeDisplayAmount').text(formatRupiah(change));
    $('#changeDisplay').show();
    $('#submitPayment').prop('disabled', false);
});

// Submit payment
$('#paymentForm').on('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong!');
        return false;
    }
    
    $('#cartData').val(JSON.stringify(cart));
});

// Format Rupiah
function formatRupiah(angka) {
    return 'Rp ' + parseFloat(angka).toLocaleString('id-ID');
}

// Search and filter
$('#searchProduct').on('keyup', function() {
    const value = $(this).val().toLowerCase();
    $('.product-item').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

$('#categoryFilter').on('change', function() {
    const categoryId = $(this).val();
    if (categoryId) {
        $('.product-item').hide();
        $(`.product-item[data-category="${categoryId}"]`).show();
    } else {
        $('.product-item').show();
    }
});

// FIX: Prevent backspace from triggering calculator when typing in input fields
$(document).on('keydown', '#customerNameInput', function(e) {
    // Stop event propagation to prevent calculator from catching it
    e.stopPropagation();
});

// FIX: Disable keyboard calculator when focus on input
$('#customerNameInput').on('focus', function() {
    $(document).off('keydown.calculator');
});

$('#customerNameInput').on('blur', function() {
    // Re-enable keyboard calculator after blur
    setTimeout(function() {
        enableKeyboardCalculator();
    }, 100);
});

// Enable keyboard calculator
function enableKeyboardCalculator() {
    $(document).on('keydown.calculator', function(e) {
        // Only if not focused on input
        if ($(e.target).is('input, textarea')) {
            return;
        }
        
        if (!calculator) return;
        
        if (e.key >= '0' && e.key <= '9') {
            calculator.appendNumber(e.key);
        }
        
        if (e.key === 'Enter' || e.key === '=') {
            e.preventDefault();
            calculator.calculate();
        }
        
        if (e.key === 'Backspace') {
            e.preventDefault();
            calculator.delete();
        }
        
        if (e.key === 'Escape') {
            calculator.clear();
        }
        
        if (e.key === '.' || e.key === ',') {
            calculator.appendNumber('.');
        }
    });
}

// Initialize keyboard calculator
enableKeyboardCalculator();
</script>

<?php include '../includes/footer.php'; ?>
