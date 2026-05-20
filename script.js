/**
 * ============================================================
 * 📁 STRUKTUR FOLDER GAMBAR
 * ============================================================
 * 
 * assets/
 * ├── images/
 * │   ├── logo/
 * │   │   └── logo.jpg
 * │   ├── hero/
 * │   │   ├── bg-hero.jpg
 * │   │   ├── hero-1.jpg
 * │   │   ├── hero-2.jpg
 * │   │   └── hero-3.jpg
 * │   ├── promo/
 * │   │   └── promo.jpg
 * │   └── products/
 * │       ├── produk-1.jpg  s/d produk-15.jpg
 * 
 * ============================================================
 */

// ========== DATA PRODUK — diambil dari database via API ==========
let products = [];

// ========== VARIABLES ==========
let cart = [];
let currentFilter = "all";
let currentSort = "default";
let minPriceFilter = 0;
let maxPriceFilter = 10000000;
let currentProduct = null;
let selectedColor = null;
let selectedSize = null;
let quantity = 1;
let selectedShipping = "standard";
let selectedPayment = "bca";
let searchTimeout = null;

const shippingCost = { standard: 20000, express: 50000 };
const shippingNames = { standard: "Standard (3-5 days)", express: "Express (1-2 days)" };
const paymentNames = { bca: "Credit Card", mandiri: "Bank Transfer", other: "E-Wallet" };

// ========== UTILITY FUNCTIONS ==========
function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const icon = toast.querySelector('i');
    
    toastMessage.textContent = message;
    if (isError) {
        icon.style.color = '#E07C6C';
        icon.className = 'fas fa-exclamation-circle';
    } else {
        icon.style.color = '#4CAF50';
        icon.className = 'fas fa-check-circle';
    }
    
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

// ========== RENDER CATEGORIES ==========
function renderCategories() {
    const categories = ["Sofa", "Meja", "Kursi", "Lemari", "Tempat Tidur"];
    const grid = document.getElementById('categoryGrid');
    if(grid) {
        grid.innerHTML = categories.map(cat => `
            <div class="category-item" onclick="filterByCategory('${cat}')">
                <div class="category-icon"><i class="fas fa-${cat === 'Sofa' ? 'couch' : cat === 'Meja' ? 'table' : cat === 'Kursi' ? 'chair' : cat === 'Lemari' ? 'archive' : 'bed'}"></i></div>
                <h3>${cat}</h3>
            </div>
        `).join('');
    }
}

// ========== RENDER PRODUCTS ==========
function renderProducts(containerId, productList, limit = null) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const items = limit ? productList.slice(0, limit) : productList;
    if (items.length === 0) {
        container.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--text-muted);"><i class="fas fa-box-open" style="font-size:3rem;margin-bottom:1rem;display:block;"></i>Produk tidak ditemukan</div>';
        return;
    }
    container.innerHTML = items.map(p => {
        const stok = p.stock ?? 0;
        const stokLabel = stok === 0
            ? `<span class="stok-badge stok-habis">Stok Habis</span>`
            : stok <= 20
            ? `<span class="stok-badge stok-sedikit">Stok: ${stok}</span>`
            : `<span class="stok-badge stok-ada">Stok: ${stok}</span>`;
        return `
        <div class="product-card" onclick="openProductDetail(${p.id})">
            <img class="product-img" src="${p.image}" alt="${p.name}" loading="lazy" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
            <div class="product-info">
                <div class="product-category">${p.category}</div>
                <div class="product-title">${p.name}</div>
                <div class="product-price">Rp ${p.price.toLocaleString('id-ID')}</div>
                ${stokLabel}
                <button class="add-cart-btn" onclick="event.stopPropagation(); addToCartQuick(${p.id})" ${stok===0?'disabled':''}>
                    <i class="fas fa-shopping-bag"></i> ${stok===0?'Stok Habis':'Beli Sekarang'}
                </button>
            </div>
        </div>`;
    }).join('');
}

// ========== FILTER & SORT ==========
function filterAndSortProducts() {
    let filtered = products.filter(p => {
        if(currentFilter !== "all" && p.category !== currentFilter) return false;
        if(p.price < minPriceFilter || p.price > maxPriceFilter) return false;
        return true;
    });
    if(currentSort === "priceLow") filtered.sort((a,b) => a.price - b.price);
    else if(currentSort === "priceHigh") filtered.sort((a,b) => b.price - a.price);
    else if(currentSort === "popular") filtered.sort((a,b) => b.sold - a.sold);
    renderProducts('allProductsGrid', filtered);
}

window.filterByCategory = (cat) => {
    currentFilter = cat;
    filterAndSortProducts();
    showPage('products');
    
    document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
        if(btn.getAttribute('data-filter') === cat) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
};

// ========== SEARCH FUNCTION ==========
function setupSearch() {
    const searchIcon = document.getElementById('searchIcon');
    const searchModal = document.getElementById('searchModal');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    
    if(searchIcon) {
        searchIcon.addEventListener('click', () => {
            searchModal.style.display = 'flex';
            searchInput.focus();
        });
    }
    
    if(closeSearch) {
        closeSearch.addEventListener('click', () => {
            searchModal.style.display = 'none';
            searchResults.innerHTML = '';
            searchInput.value = '';
        });
    }
    
    if(searchModal) {
        searchModal.addEventListener('click', (e) => {
            if(e.target === searchModal) {
                searchModal.style.display = 'none';
                searchResults.innerHTML = '';
                searchInput.value = '';
            }
        });
    }
    
    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = e.target.value.toLowerCase().trim();
                if (query.length < 2) {
                    searchResults.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);">Ketik minimal 2 karakter...</div>';
                    return;
                }
                if (products.length === 0) {
                    searchResults.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);">Memuat produk...</div>';
                    return;
                }
                const filtered = products.filter(p =>
                    p.name.toLowerCase().includes(query) ||
                    p.category.toLowerCase().includes(query) ||
                    (p.desc && p.desc.toLowerCase().includes(query))
                );
                if (filtered.length === 0) {
                    searchResults.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);">Produk tidak ditemukan</div>';
                } else {
                    searchResults.innerHTML = filtered.map(p => `
                        <div class="search-result-item" onclick="selectSearchResult(${p.id})">
                            <img src="${p.image}" alt="${p.name}" onerror="this.src='https://placehold.co/60x60?text=No+Image'">
                            <div>
                                <div style="font-weight:600;font-size:.9rem;">${p.name}</div>
                                <div style="color:var(--gold);font-weight:600;font-size:.85rem;">Rp ${p.price.toLocaleString('id-ID')}</div>
                                <div style="font-size:.75rem;color:var(--text-muted);">${p.category} &bull; Stok: ${p.stock ?? '-'}</div>
                            </div>
                        </div>
                    `).join('');
                }
            }, 250);
        });
    }
}

window.selectSearchResult = (id) => {
    document.getElementById('searchModal').style.display = 'none';
    document.getElementById('searchInput').value = '';
    openProductDetail(id);
};

// ========== NEWSLETTER FUNCTION ==========
function setupNewsletter() {
    const subscribeBtn = document.getElementById('subscribeBtn');
    const newsletterEmail = document.getElementById('newsletterEmail');
    
    if(subscribeBtn) {
        subscribeBtn.addEventListener('click', () => {
            const email = newsletterEmail.value.trim();
            if(email && email.includes('@') && email.includes('.')) {
                showToast(`Thanks for subscribing! We'll send updates to ${email}`);
                newsletterEmail.value = '';
            } else {
                showToast('Please enter a valid email address!', true);
            }
        });
    }
}

// ========== CART FUNCTIONS ==========
function addToCart(id, qty, color, size) {
    const existing = cart.find(item => item.id === id && item.selectedColor === color && item.selectedSize === size);
    const product = products.find(p => p.id === id);
    
    if(existing) {
        existing.quantity += qty;
        showToast(`${product.name} quantity updated!`);
    } else {
        cart.push({ ...product, quantity: qty, selectedColor: color, selectedSize: size });
        showToast(`${product.name} added to cart!`);
    }
    updateCartUI();
}

window.addToCartQuick = (id) => {
    const product = products.find(p => p.id === id);
    addToCart(id, 1, product.colors[0], product.sizes[0]);
};

function updateCartUI() {
    const cartCount = document.getElementById('cartCount');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    if(cartCount) cartCount.innerText = totalItems;
    
    const cartItemsDiv = document.getElementById('cartItemsList');
    if(cartItemsDiv) {
        if(cart.length === 0) {
            cartItemsDiv.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--text-muted);"><i class="fas fa-shopping-bag" style="font-size:3rem; margin-bottom:1rem; display:block;"></i>Your cart is empty</div>';
        } else {
            cartItemsDiv.innerHTML = cart.map((item, idx) => `
                <div class="cart-item">
                    <img class="cart-item-img" src="${item.image}" alt="${item.name}" onerror="this.src='https://placehold.co/70x70?text=No+Image'">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">Rp ${item.price.toLocaleString()}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">${item.selectedColor} | ${item.selectedSize}</div>
                        <div style="font-size:0.85rem;">Qty: ${item.quantity}</div>
                        <div class="remove-item" onclick="removeCartItem(${idx})"><i class="fas fa-trash-alt"></i> Remove</div>
                    </div>
                </div>
            `).join('');
        }
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const cartTotal = document.getElementById('cartTotalPrice');
    if(cartTotal) cartTotal.innerHTML = `Total: Rp ${total.toLocaleString()}`;
}

window.removeCartItem = (index) => {
    const removedItem = cart[index];
    cart.splice(index, 1);
    updateCartUI();
    showToast(`${removedItem.name} removed from cart`);
};

// ========== CHECKOUT FUNCTIONS ==========
function openCheckoutModal() {
    if(cart.length === 0) {
        showToast('Your cart is empty!', true);
        return;
    }
    document.getElementById('checkoutModal').style.display = 'flex';
    updateReceiptPreview();
}

function updateReceiptPreview() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = shippingCost[selectedShipping];
    const total = subtotal + shipping;
    
    const receiptItemsDiv = document.getElementById('receiptItems');
    receiptItemsDiv.innerHTML = cart.map(item => `
        <div class="summary-row">
            <span>${item.name} x${item.quantity}</span>
            <span>Rp ${(item.price * item.quantity).toLocaleString()}</span>
        </div>
    `).join('');
    
    document.getElementById('receiptSubtotal').innerHTML = `Rp ${subtotal.toLocaleString()}`;
    document.getElementById('receiptShipping').innerHTML = `Rp ${shipping.toLocaleString()}`;
    document.getElementById('receiptTotal').innerHTML = `Rp ${total.toLocaleString()}`;
}

function generateInvoiceNumber() {
    return 'INV/' + new Date().getFullYear() + '/' + 
           String(Math.floor(Math.random() * 10000)).padStart(4, '0');
}

function printReceipt(orderData) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head><title>FurniRest - Order Receipt</title>
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px; }
            .receipt { max-width: 450px; margin: 0 auto; border: 1px solid #E0DCD5; padding: 25px; border-radius: 24px; }
            .header { text-align: center; border-bottom: 2px dashed #C8A86B; padding-bottom: 15px; margin-bottom: 20px; }
            .header h2 { color: #C8A86B; margin-bottom: 5px; }
            .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
            .total { border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px; font-weight: bold; font-size: 1.1rem; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
        </style>
        </head>
        <body>
        <div class="receipt">
            <div class="header">
                <h2>FurniRest</h2>
                <p>Premium Luxury Furniture</p>
                <p>No. Invoice: ${orderData.invoiceNo}</p>
                <p>${new Date().toLocaleString('id-ID')}</p>
            </div>
            <div><strong>Customer:</strong> ${orderData.customerName}</div>
            <div><strong>Address:</strong> ${orderData.customerAddress}</div>
            <div><strong>Phone:</strong> ${orderData.customerPhone}</div>
            <div style="margin: 15px 0;"><strong>Order Details:</strong></div>
            ${orderData.items.map(item => `<div class="row"><span>${item.name} x ${item.quantity}</span><span>Rp ${(item.price * item.quantity).toLocaleString()}</span></div>`).join('')}
            <div class="row"><span>Subtotal:</span><span>Rp ${orderData.subtotal.toLocaleString()}</span></div>
            <div class="row"><span>Shipping:</span><span>Rp ${orderData.shippingCost.toLocaleString()}</span></div>
            <div class="row total"><span>TOTAL:</span><span>Rp ${orderData.total.toLocaleString()}</span></div>
            <div class="row"><span>Payment:</span><span>${orderData.paymentMethod}</span></div>
            <div class="footer"><p>Thank you for shopping at FurniRest!</p><p>Your order will be processed soon</p></div>
        </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// ========== PRODUCT DETAIL MODAL ==========
window.openProductDetail = (id) => {
    currentProduct = products.find(p => p.id === id);
    selectedColor = currentProduct.colors[0];
    selectedSize = currentProduct.sizes[0];
    quantity = 1;
    
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="modal-gallery">
            <img src="${currentProduct.image}" alt="${currentProduct.name}" onerror="this.src='https://placehold.co/600x400?text=No+Image'">
        </div>
        <div class="modal-details">
            <h2 class="modal-title">${currentProduct.name}</h2>
            <div class="modal-category" style="color:var(--gold); font-size:0.8rem;">${currentProduct.category} | ⭐ ${currentProduct.rating} | Sold ${currentProduct.sold}</div>
            <div class="modal-price">Rp ${currentProduct.price.toLocaleString()}</div>
            <div class="modal-desc" style="color:var(--text-muted); margin:1rem 0;">${currentProduct.desc}</div>
            <div class="variant-group">
                <div class="variant-label">Color:</div>
                <div class="variant-options" id="colorOptions"></div>
            </div>
            <div class="variant-group">
                <div class="variant-label">Size:</div>
                <div class="variant-options" id="sizeOptions"></div>
            </div>
            <div class="variant-group">
                <div class="variant-label">Quantity:</div>
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="updateQuantityModal(-1)">-</button>
                    <input id="quantityInput" type="number" value="1" min="1" readonly style="width:70px; text-align:center; border:1px solid #ddd; border-radius:12px; padding:0.5rem;">
                    <button class="quantity-btn" onclick="updateQuantityModal(1)">+</button>
                </div>
            </div>
            <button class="modal-add-cart" onclick="addToCartFromModal()"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
        </div>
    `;
    
    const colorDiv = document.getElementById('colorOptions');
    colorDiv.innerHTML = currentProduct.colors.map(c => `<div class="variant-option ${c === selectedColor ? 'selected' : ''}" onclick="selectColorModal('${c}')">${c}</div>`).join('');
    
    const sizeDiv = document.getElementById('sizeOptions');
    sizeDiv.innerHTML = currentProduct.sizes.map(s => `<div class="variant-option ${s === selectedSize ? 'selected' : ''}" onclick="selectSizeModal('${s}')">${s}</div>`).join('');
    
    document.getElementById('productModal').style.display = 'flex';
};

window.selectColorModal = (color) => { 
    selectedColor = color; 
    document.querySelectorAll('#colorOptions .variant-option').forEach(opt => opt.classList.remove('selected')); 
    event.target.classList.add('selected'); 
};

window.selectSizeModal = (size) => { 
    selectedSize = size; 
    document.querySelectorAll('#sizeOptions .variant-option').forEach(opt => opt.classList.remove('selected')); 
    event.target.classList.add('selected'); 
};

window.updateQuantityModal = (delta) => { 
    quantity = Math.max(1, quantity + delta); 
    document.getElementById('quantityInput').value = quantity; 
};

window.addToCartFromModal = () => {
    addToCart(currentProduct.id, quantity, selectedColor, selectedSize);
    document.getElementById('productModal').style.display = 'none';
};

// ========== PAGE NAVIGATION ==========
function showPage(pageId) {
    document.querySelectorAll('[id$="-page"]').forEach(page => {
        page.classList.add('hidden-page');
        page.classList.remove('active-page');
    });
    document.getElementById(`${pageId}-page`).classList.remove('hidden-page');
    document.getElementById(`${pageId}-page`).classList.add('active-page');
    
    document.querySelectorAll('.nav-link, .bottom-nav-item').forEach(link => {
        link.classList.remove('active');
        if(link.getAttribute('data-page') === pageId) link.classList.add('active');
    });
    
    if(pageId === 'products') filterAndSortProducts();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if(element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ========== PROFILE EDIT ==========
let isEditing = false;
const editProfileBtn = document.getElementById('editProfileBtn');
const saveProfileBtn = document.getElementById('saveProfileBtn');

if(editProfileBtn) {
    editProfileBtn.addEventListener('click', () => {
        isEditing = true;
        document.getElementById('profileName').disabled = false;
        document.getElementById('profileEmail').disabled = false;
        document.getElementById('profileAddress').disabled = false;
        document.getElementById('profilePhone').disabled = false;
        editProfileBtn.style.display = 'none';
        if(saveProfileBtn) saveProfileBtn.style.display = 'inline-block';
    });
}

if(saveProfileBtn) {
    saveProfileBtn.addEventListener('click', () => {
        isEditing = false;
        document.getElementById('profileName').disabled = true;
        document.getElementById('profileEmail').disabled = true;
        document.getElementById('profileAddress').disabled = true;
        document.getElementById('profilePhone').disabled = true;
        saveProfileBtn.style.display = 'none';
        if(editProfileBtn) editProfileBtn.style.display = 'inline-block';
        showToast('Profile saved successfully!');
    });
}

// ========== EVENT LISTENERS ==========
const checkoutBtn = document.getElementById('checkoutBtn');
const closeCart = document.getElementById('closeCart');
const cartIconBtn = document.getElementById('cartIconBtn');
const closeCheckout = document.getElementById('closeCheckout');

if(checkoutBtn) checkoutBtn.addEventListener('click', openCheckoutModal);
if(closeCart) closeCart.addEventListener('click', () => {
    document.getElementById('cartSidebar').classList.remove('open');
});
if(cartIconBtn) cartIconBtn.addEventListener('click', () => {
    document.getElementById('cartSidebar').classList.add('open');
});
if(closeCheckout) closeCheckout.addEventListener('click', () => {
    document.getElementById('checkoutModal').style.display = 'none';
});

// Shipping & Payment Options
document.querySelectorAll('.shipping-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.shipping-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        selectedShipping = this.getAttribute('data-shipping');
        updateReceiptPreview();
    });
});

document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        selectedPayment = this.getAttribute('data-bank');
        updateReceiptPreview();
    });
});

// Checkout Form Submit
const checkoutForm = document.getElementById('checkoutForm');
if(checkoutForm) {
    checkoutForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const customerName = document.getElementById('customerName').value;
        const customerAddress = document.getElementById('customerAddress').value;
        const customerPhone = document.getElementById('customerPhone').value;
        
        if(!customerName || !customerAddress || !customerPhone) {
            showToast('Please fill in all fields!', true);
            return;
        }
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const orderData = {
            invoiceNo: generateInvoiceNumber(),
            customerName,
            customerAddress,
            customerPhone,
            items: cart.map(item => ({ name: item.name, quantity: item.quantity, price: item.price })),
            subtotal: subtotal,
            shippingMethod: shippingNames[selectedShipping],
            shippingCost: shippingCost[selectedShipping],
            total: subtotal + shippingCost[selectedShipping],
            paymentMethod: paymentNames[selectedPayment]
        };
        
        printReceipt(orderData);
        showToast('Order placed successfully! Receipt is printing...');
        cart = [];
        updateCartUI();
        document.getElementById('checkoutModal').style.display = 'none';
        document.getElementById('cartSidebar').classList.remove('open');
        checkoutForm.reset();
    });
}

// Modal Close
const productModal = document.getElementById('productModal');
if(productModal) {
    productModal.addEventListener('click', (e) => {
        if(e.target === productModal) {
            productModal.style.display = 'none';
        }
    });
}

document.querySelectorAll('.modal-close').forEach(closeBtn => {
    closeBtn.addEventListener('click', () => {
        document.getElementById('productModal').style.display = 'none';
    });
});

// Navigation Links
document.querySelectorAll('.nav-link, .bottom-nav-item').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const page = link.getAttribute('data-page');
        if(page) showPage(page);
    });
});

// Filter Buttons
const sortPopular = document.getElementById('sortPopular');
const sortPriceLow = document.getElementById('sortPriceLow');
const sortPriceHigh = document.getElementById('sortPriceHigh');
const applyPriceFilter = document.getElementById('applyPriceFilter');

if(sortPopular) sortPopular.addEventListener('click', () => { currentSort = "popular"; filterAndSortProducts(); });
if(sortPriceLow) sortPriceLow.addEventListener('click', () => { currentSort = "priceLow"; filterAndSortProducts(); });
if(sortPriceHigh) sortPriceHigh.addEventListener('click', () => { currentSort = "priceHigh"; filterAndSortProducts(); });
if(applyPriceFilter) applyPriceFilter.addEventListener('click', () => {
    const minInput = document.getElementById('minPrice');
    const maxInput = document.getElementById('maxPrice');
    minPriceFilter = parseInt(minInput?.value) || 0;
    maxPriceFilter = parseInt(maxInput?.value) || 10000000;
    filterAndSortProducts();
});

document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
        currentFilter = btn.getAttribute('data-filter');
        filterAndSortProducts();
        document.querySelectorAll('.filter-btn[data-filter]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

// ========== ORDER FORM FUNCTIONS ==========
function openOrderForm(productId, productName, price) {
    document.getElementById('orderProductId').value = productId;
    document.getElementById('orderProductName').value = productName;
    document.getElementById('orderProductPrice').value = price;
    document.getElementById('orderQuantity').value = 1;
    updateTotal();
    document.getElementById('orderModal').classList.add('active');
}

function closeOrderForm() {
    document.getElementById('orderModal').classList.remove('active');
}

function updateTotal() {
    const quantity = parseInt(document.getElementById('orderQuantity').value) || 1;
    const price = parseInt(document.getElementById('orderProductPrice').value) || 0;
    const total = price * quantity;
    document.getElementById('orderTotalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ========== INITIALIZE APP ==========
document.addEventListener('DOMContentLoaded', () => {
    setupSearch();
    setupNewsletter();
    updateCartUI();
    renderCategories();
    loadProductsFromDB();
});

function loadProductsFromDB() {
    fetch('api/products.php')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.products.length) {
                products = data.products;
            }
            renderProducts('popularProducts', products.slice(0, 8));
            renderProducts('allProductsGrid', products);
            renderCategories(); // re-render setelah data ada
        })
        .catch(() => {
            renderProducts('popularProducts', products.slice(0, 8));
            renderProducts('allProductsGrid', products);
        });
}