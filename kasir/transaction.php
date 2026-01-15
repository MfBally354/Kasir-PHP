<?php
// kasir/transaction.php - DEBUG VERSION
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
                            <div class="card h-100" style="transition: all 0.3s;">
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
                                    <p class="mb-1 text-primary fw-bold small"><?php echo formatRupiah($product['price']); ?></p>
                                    <small class="text-muted d-block mb-2">Stok: <?php echo $product['stock']; ?></small>

                                    <button type="button"
                                            class="btn btn-success btn-sm w-100 add-to-cart-btn"
                                            onclick="addProductToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, <?php echo $product['stock']; ?>, this)">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah
                                    </button>
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
                        <h5 class="mb-0">Total Belanja:</h5>
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
                                <select class="form-select" name="payment_method" id="paymentMethod" required>
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

                        <!-- Calculator Display -->
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

                        <!-- INFO BOXES - SELALU TAMPIL -->
                        <div class="row g-2 mb-3">
                            <!-- Total Belanja -->
                            <div class="col-md-6">
                                <div class="card bg-info bg-opacity-10 border-info">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <small class="text-muted d-block mb-1">💰 Total Belanja</small>
                                            <h4 class="mb-0 fw-bold text-info total-belanja-box">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kembalian -->
                            <div class="col-md-6">
                                <div class="card bg-success bg-opacity-10 border-success">
                                    <div class="card-body p-3">
                                        <div class="text-center">
                                            <small class="text-muted d-block mb-1">💵 Kembalian</small>
                                            <h4 class="mb-0 fw-bold text-success" id="kembalianBox">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DEBUG INFO -->
                        <div class="alert alert-warning mb-3" id="debugInfo" style="display: none;">
                            <small><strong>🐛 DEBUG INFO:</strong></small><br>
                            <small id="debugText"></small>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg w-100 mb-2" id="applyPayment">
                            <i class="bi bi-calculator me-2"></i>Hitung Kembalian
                        </button>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitPayment" disabled>
                            <i class="bi bi-check-circle me-2"></i>Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calculator-display {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    font-size: 2rem;
    font-weight: bold;
    text-align: right;
    color: #495057;
    min-height: 70px;
}

.calculator-btn {
    height: 60px;
    font-size: 1.2rem;
    font-weight: 600;
}

.product-item .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.add-to-cart-btn:hover {
    transform: scale(1.05);
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.btn-added {
    animation: pulse 0.3s ease;
}

@keyframes highlight {
    0% { background-color: rgba(25, 135, 84, 0.1); }
    50% { background-color: rgba(25, 135, 84, 0.3); }
    100% { background-color: rgba(25, 135, 84, 0.1); }
}

.kembalian-updated {
    animation: highlight 0.6s ease;
}
</style>

<script>
// ========================================
// GLOBAL CART
// ========================================
let cart = [];

console.log('🚀 Transaction page loaded');
console.log('📍 Script version: DEBUG v1.0');

// ========================================
// FORMAT RUPIAH
// ========================================
function formatRupiah(angka) {
    return 'Rp ' + parseFloat(angka).toLocaleString('id-ID');
}

// ========================================
// SHOW DEBUG INFO
// ========================================
function showDebug(message) {
    const debugInfo = document.getElementById('debugInfo');
    const debugText = document.getElementById('debugText');
    if (debugInfo && debugText) {
        debugText.innerHTML = message;
        debugInfo.style.display = 'block';
        console.log('🐛 DEBUG:', message);
    }
}

// ========================================
// ADD PRODUCT TO CART
// ========================================
function addProductToCart(id, name, price, stock, button) {
    console.log('🛒 Adding:', {id, name, price, stock});

    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        if (existingItem.quantity < stock) {
            existingItem.quantity++;
        } else {
            alert('Stok tidak mencukupi! Maksimal: ' + stock);
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

    if (button) {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check-circle me-1"></i>Ditambah!';
        button.classList.add('btn-added');

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-added');
        }, 500);
    }

    renderCart();
}

// ========================================
// CALCULATE TOTAL
// ========================================
function calculateTotal() {
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
    });

    document.querySelectorAll('.total-amount').forEach(el => {
        el.textContent = formatRupiah(total);
    });

    document.querySelectorAll('.total-belanja-box').forEach(el => {
        el.textContent = formatRupiah(total);
    });

    const totalAmountInput = document.getElementById('totalAmount');
    if (totalAmountInput) {
        totalAmountInput.value = total;
    }

    updateCartData();

    console.log('💰 Total calculated:', total);
    return total;
}

// ========================================
// UPDATE KEMBALIAN BOX
// ========================================
function updateKembalianBox(kembalian) {
    console.log('💵 Updating kembalian box:', kembalian);
    
    const kembalianBox = document.getElementById('kembalianBox');
    
    if (!kembalianBox) {
        console.error('❌ kembalianBox element NOT FOUND!');
        showDebug('ERROR: kembalianBox element tidak ditemukan!');
        return;
    }
    
    console.log('✅ kembalianBox element found');
    
    kembalianBox.textContent = formatRupiah(kembalian);
    console.log('✅ Text updated to:', formatRupiah(kembalian));
    
    // Highlight effect
    const card = kembalianBox.closest('.card');
    if (card) {
        card.classList.add('kembalian-updated');
        setTimeout(() => {
            card.classList.remove('kembalian-updated');
        }, 600);
    }
    
    showDebug(`Kembalian berhasil diupdate: ${formatRupiah(kembalian)}`);
}

// ========================================
// UPDATE CART DATA
// ========================================
function updateCartData() {
    const cartDataField = document.getElementById('cartData');
    if (cartDataField) {
        cartDataField.value = JSON.stringify(cart);
    }
}

// ========================================
// RENDER CART
// ========================================
function renderCart() {
    const cartItems = document.getElementById('cartItems');
    if (!cartItems) return;

    cartItems.innerHTML = '';

    if (cart.length === 0) {
        cartItems.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    Keranjang masih kosong
                </td>
            </tr>
        `;
        calculateTotal();
        return;
    }

    cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td class="text-center">
                <div class="input-group input-group-sm" style="width: 120px; margin: 0 auto;">
                    <button class="btn btn-outline-secondary" onclick="decreaseQty(${index})" type="button">-</button>
                    <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                    <button class="btn btn-outline-secondary" onclick="increaseQty(${index})" type="button">+</button>
                </div>
            </td>
            <td class="text-end">${formatRupiah(item.price)}</td>
            <td class="text-end">${formatRupiah(subtotal)}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeItem(${index})" type="button">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        cartItems.appendChild(row);
    });

    calculateTotal();
}

// ========================================
// CART ACTIONS
// ========================================
function decreaseQty(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        renderCart();
    }
}

function increaseQty(index) {
    if (cart[index].quantity < cart[index].stock) {
        cart[index].quantity++;
        renderCart();
    } else {
        alert('Stok maksimal: ' + cart[index].stock);
    }
}

function removeItem(index) {
    if (confirm('Hapus item dari keranjang?')) {
        cart.splice(index, 1);
        renderCart();
    }
}

// ========================================
// DOCUMENT READY
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Ready');
    renderCart();
});

// ========================================
// JQUERY READY
// ========================================
$(document).ready(function() {
    console.log('📄 jQuery Ready');
    console.log('🔍 Checking elements...');
    console.log('  - applyPayment button:', $('#applyPayment').length > 0 ? 'FOUND' : 'NOT FOUND');
    console.log('  - kembalianBox element:', $('#kembalianBox').length > 0 ? 'FOUND' : 'NOT FOUND');
    console.log('  - calculator object:', typeof calculator !== 'undefined' ? 'EXISTS' : 'NOT FOUND');

    // SEARCH PRODUCT
    $('#searchProduct').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.product-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // CATEGORY FILTER
    $('#categoryFilter').on('change', function() {
        const categoryId = $(this).val();
        if (categoryId) {
            $('.product-item').hide();
            $(`.product-item[data-category="${categoryId}"]`).show();
        } else {
            $('.product-item').show();
        }
    });

    // ========================================
    // HITUNG KEMBALIAN - ULTRA DEBUG VERSION
    // ========================================
    $('#applyPayment').on('click', function() {
        console.log('');
        console.log('═══════════════════════════════════════');
        console.log('💳 HITUNG KEMBALIAN CLICKED');
        console.log('═══════════════════════════════════════');

        // Step 1: Validasi cart
        console.log('STEP 1: Validasi cart');
        console.log('  Cart length:', cart.length);
        console.log('  Cart data:', cart);
        
        if (cart.length === 0) {
            console.error('  ❌ Cart kosong!');
            alert('Keranjang masih kosong!');
            return;
        }
        console.log('  ✅ Cart OK');

        // Step 2: Check calculator
        console.log('STEP 2: Check calculator');
        console.log('  typeof calculator:', typeof calculator);
        console.log('  calculator object:', calculator);
        
        if (typeof calculator === 'undefined' || !calculator) {
            console.error('  ❌ Calculator NOT FOUND!');
            alert('Calculator tidak ready! Refresh halaman.');
            showDebug('ERROR: Calculator object tidak ditemukan!');
            return;
        }
        console.log('  ✅ Calculator OK');

        // Step 3: Get values
        console.log('STEP 3: Get values');
        const total = parseFloat($('#totalAmount').val());
        console.log('  Total amount:', total);
        console.log('  Calculator currentValue:', calculator.currentValue);
        
        const payment = calculator.getValue();
        console.log('  Payment (from getValue()):', payment);

        // Step 4: Validasi payment
        console.log('STEP 4: Validasi payment');
        if (!payment || payment <= 0) {
            console.error('  ❌ Payment invalid:', payment);
            alert('Masukkan jumlah pembayaran di kalkulator!');
            showDebug(`ERROR: Payment = ${payment} (invalid)`);
            return;
        }
        console.log('  ✅ Payment valid');

        if (payment < total) {
            const kurang = total - payment;
            console.warn('  ⚠️  Payment kurang!');
            console.log('  Total:', total);
            console.log('  Payment:', payment);
            console.log('  Kurang:', kurang);
            alert(`Pembayaran kurang!\n\nTotal: ${formatRupiah(total)}\nBayar: ${formatRupiah(payment)}\nKurang: ${formatRupiah(kurang)}`);
            showDebug(`Payment kurang! Total: ${formatRupiah(total)}, Bayar: ${formatRupiah(payment)}`);
            return;
        }
        console.log('  ✅ Payment cukup');

        // Step 5: Calculate kembalian
        console.log('STEP 5: Calculate kembalian');
        const kembalian = payment - total;
        console.log('  Kembalian =', payment, '-', total, '=', kembalian);
        console.log('  Kembalian formatted:', formatRupiah(kembalian));

        // Step 6: Set hidden fields
        console.log('STEP 6: Set hidden fields');
        $('#paymentAmount').val(payment);
        $('#changeAmount').val(kembalian);
        console.log('  paymentAmount set to:', payment);
        console.log('  changeAmount set to:', kembalian);

        // Step 7: Update cart data
        console.log('STEP 7: Update cart data');
        updateCartData();
        console.log('  Cart data updated');

        // Step 8: UPDATE KEMBALIAN BOX - DIRECT UPDATE
        console.log('STEP 8: Update kembalian box');
        console.log('  Kembalian value:', kembalian);
        
        // DIRECT UPDATE - Tidak pakai function
        const kembalianBox = document.getElementById('kembalianBox');
        if (kembalianBox) {
            kembalianBox.textContent = formatRupiah(kembalian);
            console.log('  ✅ Kembalian box updated to:', formatRupiah(kembalian));
            
            // Highlight effect
            const card = kembalianBox.closest('.card');
            if (card) {
                card.classList.add('kembalian-updated');
                setTimeout(() => card.classList.remove('kembalian-updated'), 600);
            }
        } else {
            console.error('  ❌ kembalianBox element NOT FOUND!');
        }

        // Step 9: Enable submit
        console.log('STEP 9: Enable submit button');
        $('#submitPayment').prop('disabled', false);
        console.log('  Submit button enabled');

        console.log('═══════════════════════════════════════');
        console.log('✅ HITUNG KEMBALIAN SELESAI');
        console.log('═══════════════════════════════════════');
        console.log('');

        // Alert
        alert(`✅ Kembalian: ${formatRupiah(kembalian)}\n\nCek kotak hijau di bawah kalkulator!`);
    });

    // ========================================
    // FORM SUBMIT
    // ========================================
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        if (!cart || cart.length === 0) {
            alert('Keranjang masih kosong!');
            return false;
        }

        const paymentAmount = parseFloat($('#paymentAmount').val());
        const totalAmount = parseFloat($('#totalAmount').val());

        if (!paymentAmount || paymentAmount <= 0) {
            alert('Silakan hitung kembalian terlebih dahulu!');
            return false;
        }

        if (paymentAmount < totalAmount) {
            alert('Jumlah pembayaran kurang dari total!');
            return false;
        }

        updateCartData();

        const cartDataValue = $('#cartData').val();
        if (!cartDataValue || cartDataValue === '' || cartDataValue === '[]') {
            alert('Error: Data keranjang kosong!');
            return false;
        }

        $('#submitPayment').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

        this.submit();
        return true;
    });

    $('#customerNameInput').on('focus', function() {
        $(document).off('keydown.calculator');
    });

    $('#customerNameInput').on('blur', function() {
        setTimeout(enableKeyboardCalculator, 100);
    });

    console.log('✅ All event listeners attached');
});

function enableKeyboardCalculator() {
    $(document).on('keydown.calculator', function(e) {
        if ($(e.target).is('input, textarea')) return;
        if (typeof calculator === 'undefined') return;

        if (e.key >= '0' && e.key <= '9') calculator.appendNumber(e.key);
        if (e.key === 'Enter' || e.key === '=') { e.preventDefault(); calculator.calculate(); }
        if (e.key === 'Backspace') { e.preventDefault(); calculator.delete(); }
        if (e.key === 'Escape') calculator.clear();
        if (e.key === '.' || e.key === ',') calculator.appendNumber('.');
    });
}

if (typeof $ !== 'undefined') {
    enableKeyboardCalculator();
}

console.log('✅ Script loaded - DEBUG MODE');
</script>

<?php include '../includes/footer.php'; ?>
