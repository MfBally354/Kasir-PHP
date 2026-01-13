<?php
// kasir/transaction.php - SIMPLIFIED VERSION (NO EXTERNAL CALCULATOR.JS)
require_once '../config/config.php';
requireRole('kasir');

$pageTitle = 'Transaksi Baru';

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
                        <h3 class="mb-0 fw-bold text-success" id="totalDisplay">Rp 0</h3>
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

                        <!-- Total Info -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">💰 Total Belanja:</span>
                                <span class="fs-5 fw-bold" id="totalInfoDisplay">Rp 0</span>
                            </div>
                        </div>

                        <!-- Calculator Display -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Bayar</label>
                            <input type="text" 
                                   id="paymentInput" 
                                   class="form-control form-control-lg text-end fw-bold" 
                                   style="font-size: 1.5rem;"
                                   placeholder="Rp 0"
                                   readonly>
                        </div>

                        <!-- Calculator Buttons -->
                        <div class="row g-2 mb-3">
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="7">7</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="8">8</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="9">9</button></div>
                            <div class="col-3"><button type="button" class="btn btn-warning w-100 py-3" id="clearBtn">C</button></div>

                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="4">4</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="5">5</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="6">6</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="0">0</button></div>

                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="1">1</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="2">2</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="3">3</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 py-3 calc-btn" data-value="000">000</button></div>
                        </div>

                        <!-- Quick Amount -->
                        <div class="row g-2 mb-3">
                            <div class="col-3"><button type="button" class="btn btn-outline-info w-100 quick-btn" data-amount="10000">10k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info w-100 quick-btn" data-amount="20000">20k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info w-100 quick-btn" data-amount="50000">50k</button></div>
                            <div class="col-3"><button type="button" class="btn btn-outline-info w-100 quick-btn" data-amount="100000">100k</button></div>
                        </div>

                        <button type="button" class="btn btn-primary btn-lg w-100 mb-3" id="calculateBtn">
                            <i class="bi bi-calculator me-2"></i>Hitung Kembalian
                        </button>

                        <!-- Change Display -->
                        <div id="changeBox" class="alert alert-success border-2 border-success" style="display: none;">
                            <div class="text-center py-3">
                                <div class="mb-2">
                                    <i class="bi bi-cash-stack" style="font-size: 3rem; color: #198754;"></i>
                                </div>
                                <h5 class="fw-bold text-success mb-2">KEMBALIAN</h5>
                                <h1 class="fw-bold text-success mb-0" id="changeText">Rp 0</h1>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn" disabled>
                            <i class="bi bi-check-circle me-2"></i>Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
</style>

<script>
console.log('=== SCRIPT START ===');

// GLOBAL CART
let cart = [];
let paymentValue = 0;

// FORMAT RUPIAH
function formatRupiah(number) {
    return 'Rp ' + parseInt(number).toLocaleString('id-ID');
}

// ADD TO CART
function addProductToCart(id, name, price, stock, button) {
    console.log('ADD TO CART:', {id, name, price, stock});
    
    let existingItem = cart.find(item => item.id === id);
    
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
    
    // Button feedback
    if (button) {
        let originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check-circle"></i> Ditambah!';
        button.classList.add('btn-added');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-added');
        }, 500);
    }
    
    renderCart();
}

// DECREASE QTY
function decreaseQty(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        renderCart();
    }
}

// INCREASE QTY
function increaseQty(index) {
    if (cart[index].quantity < cart[index].stock) {
        cart[index].quantity++;
        renderCart();
    } else {
        alert('Stok maksimal: ' + cart[index].stock);
    }
}

// REMOVE ITEM
function removeItem(index) {
    if (confirm('Hapus item dari keranjang?')) {
        cart.splice(index, 1);
        renderCart();
    }
}

// RENDER CART
function renderCart() {
    console.log('RENDER CART, items:', cart.length);
    
    let tbody = document.getElementById('cartItems');
    tbody.innerHTML = '';
    
    if (cart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Keranjang masih kosong</td></tr>';
        updateTotal(0);
        return;
    }
    
    let total = 0;
    
    cart.forEach((item, index) => {
        let subtotal = item.price * item.quantity;
        total += subtotal;
        
        let row = `
            <tr>
                <td>${item.name}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="decreaseQty(${index})" type="button">-</button>
                        <span class="btn btn-outline-secondary disabled">${item.quantity}</span>
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
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    updateTotal(total);
    updateCartData();
}

// UPDATE TOTAL
function updateTotal(total) {
    console.log('UPDATE TOTAL:', total);
    
    document.getElementById('totalDisplay').textContent = formatRupiah(total);
    document.getElementById('totalInfoDisplay').textContent = formatRupiah(total);
    document.getElementById('totalAmount').value = total;
}

// UPDATE CART DATA
function updateCartData() {
    document.getElementById('cartData').value = JSON.stringify(cart);
}

// CALCULATOR FUNCTIONS
function updatePaymentDisplay() {
    document.getElementById('paymentInput').value = formatRupiah(paymentValue);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM LOADED');
    
    // Calculator number buttons
    document.querySelectorAll('.calc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            
            if (paymentValue === 0) {
                paymentValue = parseInt(value);
            } else {
                paymentValue = parseInt(paymentValue.toString() + value);
            }
            
            updatePaymentDisplay();
        });
    });
    
    // Clear button
    document.getElementById('clearBtn').addEventListener('click', function() {
        paymentValue = 0;
        updatePaymentDisplay();
        document.getElementById('changeBox').style.display = 'none';
        document.getElementById('submitBtn').disabled = true;
    });
    
    // Quick amount buttons
    document.querySelectorAll('.quick-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            paymentValue = parseInt(this.getAttribute('data-amount'));
            updatePaymentDisplay();
        });
    });
    
    // Calculate button
    document.getElementById('calculateBtn').addEventListener('click', function() {
        console.log('CALCULATE CLICKED');
        
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        
        let total = parseInt(document.getElementById('totalAmount').value);
        
        if (paymentValue === 0) {
            alert('Masukkan jumlah pembayaran!');
            return;
        }
        
        if (paymentValue < total) {
            alert(`Pembayaran kurang!\n\nTotal: ${formatRupiah(total)}\nBayar: ${formatRupiah(paymentValue)}\nKurang: ${formatRupiah(total - paymentValue)}`);
            return;
        }
        
        let change = paymentValue - total;
        
        // Set hidden fields
        document.getElementById('paymentAmount').value = paymentValue;
        document.getElementById('changeAmount').value = change;
        
        // Show change
        document.getElementById('changeText').textContent = formatRupiah(change);
        document.getElementById('changeBox').style.display = 'block';
        
        // Enable submit
        document.getElementById('submitBtn').disabled = false;
        
        // Scroll to change
        document.getElementById('changeBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        console.log('CHANGE CALCULATED:', change);
    });
    
    // Form submit
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('FORM SUBMIT');
        
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return false;
        }
        
        let payment = parseInt(document.getElementById('paymentAmount').value);
        let total = parseInt(document.getElementById('totalAmount').value);
        
        if (!payment || payment < total) {
            alert('Silakan hitung kembalian terlebih dahulu!');
            return false;
        }
        
        updateCartData();
        
        let cartData = document.getElementById('cartData').value;
        if (!cartData || cartData === '[]') {
            alert('Error: Cart data kosong!');
            return false;
        }
        
        // Disable submit button
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        
        console.log('SUBMITTING...');
        this.submit();
    });
    
    // Search
    if (document.getElementById('searchProduct')) {
        document.getElementById('searchProduct').addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(value) ? '' : 'none';
            });
        });
    }
    
    // Category filter
    if (document.getElementById('categoryFilter')) {
        document.getElementById('categoryFilter').addEventListener('change', function() {
            let categoryId = this.value;
            document.querySelectorAll('.product-item').forEach(item => {
                if (!categoryId) {
                    item.style.display = '';
                } else {
                    item.style.display = item.getAttribute('data-category') === categoryId ? '' : 'none';
                }
            });
        });
    }
    
    console.log('ALL EVENTS ATTACHED');
});

console.log('=== SCRIPT END ===');
</script>

<?php include '../includes/footer.php'; ?>
