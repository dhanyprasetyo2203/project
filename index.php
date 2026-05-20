<?php
session_start();
if (!isset($_SESSION['furni_user']) && !isset($_SESSION['furni_admin'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';

// Proses form order
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    $name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
    
    if (!empty($name) && !empty($phone) && !empty($address) && $product_id > 0 && $quantity > 0 && $price > 0) {
        try {
            $total = $price * $quantity;
            
            $stmt = $db->prepare("INSERT INTO orders (product_id, customer_name, customer_phone, customer_address, quantity, total_price, order_date, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
            
            if ($stmt->execute([$product_id, $name, $phone, $address, $quantity, $total])) {
                $success = "✅ Pesanan berhasil! Kami akan menghubungi Anda segera.";
            } else {
                $error = "❌ Gagal menyimpan pesanan. Silakan coba lagi.";
            }
        } catch(PDOException $e) {
            $error = "❌ Error database: " . $e->getMessage();
        }
    } else {
        $error = "❌ Semua field harus diisi dengan benar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>FurniRest | Premium Luxury Furniture</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS (YANG ASLI DARI KAMU) -->
    <link rel="stylesheet" href="style.css">


    
    <style>
        /* Tambahan kecil untuk modal form order */
        .order-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .order-modal.active {
            display: flex;
        }
        .order-container {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 32px;
            padding: 2rem;
            position: relative;
            animation: modalFade 0.3s;
        }
        @keyframes modalFade {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .order-close {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 1.8rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .order-close:hover {
            color: var(--gold);
        }
        .order-container h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        .order-container input,
        .order-container textarea,
        .order-container select {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 1rem;
            border: 1.5px solid #E0DCD5;
            border-radius: 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: var(--transition);
        }
        .order-container input:focus,
        .order-container textarea:focus {
            outline: none;
            border-color: var(--gold);
        }
        .order-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .order-row input {
            flex: 1;
            margin-bottom: 0;
        }
        .total-price {
            background: var(--gray-light);
            padding: 1rem;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin: 1rem 0;
        }
        .order-submit {
            width: 100%;
            padding: 1rem;
            background: var(--dark);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .order-submit:hover {
            background: var(--gold);
            transform: translateY(-2px);
        }
        .success-message {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .error-message {
            background: #f44336;
            color: white;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ========== NAVIGATION BAR ========== -->
<nav class="navbar">
    <div class="logo">
        <img src="assets/images/logo/logo.jpg" alt="FurniRest Logo" class="logo-img" onerror="this.src='https://placehold.co/60x60?text=Logo'">
        Furni<span>Rest</span>
    </div>
    <div class="nav-links">
        <a href="#" class="nav-link active" data-page="home">Beranda</a>
        <a href="#" class="nav-link" data-page="products">Koleksi</a>
        <a href="#" class="nav-link" data-page="profile">Profil</a>
    </div>
    <div class="nav-right">
        <i class="fas fa-search" id="searchIcon"></i>
        <div class="cart-icon" id="cartIconBtn">
            <i class="fas fa-shopping-bag"></i>
            <span class="cart-count" id="cartCount">0</span>
        </div>
        <span style="font-size:0.82rem; color:#6B6B6B; font-weight:600;">
            <i class="fas fa-user-circle"></i>
            <?= htmlspecialchars($_SESSION['furni_nama'] ?? 'Tamu') ?>
        </span>
        <a href="logout.php" title="Keluar" style="color:#C8A86B; font-size:1.1rem; margin-left:0.5rem;">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>

<!-- Bottom Navigation Mobile -->
<div class="bottom-nav">
    <div class="bottom-nav-item active" data-page="home">
        <i class="fas fa-home"></i>
        <span>Beranda</span>
    </div>
    <div class="bottom-nav-item" data-page="products">
        <i class="fas fa-couch"></i>
        <span>Koleksi</span>
    </div>
    <div class="bottom-nav-item" data-page="profile">
        <i class="fas fa-user"></i>
        <span>Profil</span>
    </div>
</div>

<!-- ========== HOME PAGE (hero + kategori + featured) ========== -->
<div id="home-page">
<section class="hero">
    <div class="hero-bg" style="background-image: url('assets/images/hero/bg-hero.jpg');"></div>
    <div class="hero-content">
        <div class="hero-text" data-aos="fade-up" data-aos-duration="1000">
            <span class="hero-badge">✦ Limited Edition ✦</span>
            <h1>Elevate Your<br>Living Space</h1>
            <p style="color:#1A1A1A;background:rgba(253,251,247,0.75);padding:.5rem .8rem;border-radius:12px;display:inline-block;max-width:90%;">Discover luxury furniture that combines timeless elegance with modern comfort. Crafted for those who appreciate the finer things in life.</p>
            <div class="hero-buttons">
                <button class="btn-primary" onclick="showPage('products')">Shop Collection →</button>
                <button class="btn-outline" onclick="scrollToSection('featured')">Explore Now</button>
            </div>
            <div class="hero-stats">
                <div><span>500+</span><br>Premium Products</div>
                <div><span>15k+</span><br>Happy Customers</div>
                <div><span>4.9</span><br>Rating</div>
            </div>
        </div>
        <div class="hero-gallery" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
            <div class="hero-card hero-card-1">
                <img src="assets/images/hero/hero-1.jpg" alt="Luxury Sofa" onerror="this.src='https://placehold.co/400x500?text=Hero+1'">
                <div class="hero-card-label">Nordic Sofa</div>
            </div>
            <div class="hero-card hero-card-2">
                <img src="assets/images/hero/hero-2.jpg" alt="Designer Chair" onerror="this.src='https://placehold.co/400x500?text=Hero+2'">
                <div class="hero-card-label">Eames Chair</div>
            </div>
            <div class="hero-card hero-card-3">
                <img src="assets/images/hero/hero-3.jpg" alt="Modern Cabinet" onerror="this.src='https://placehold.co/400x500?text=Hero+3'">
                <div class="hero-card-label">Statement Piece</div>
            </div>
            <div class="promo-banner">
                <div class="promo-content">
                    <div class="promo-text">
                        <span class="promo-badge">Limited Offer</span>
                        <h2>Luxury</h2>
                        <h3>-40% OFF</h3>
                    </div>
                    <div class="promo-image-wrapper">
                        <div class="promo-image">
                            <img src="assets/images/promo/promo.jpg" alt="Promo" onerror="this.src='https://placehold.co/100x100?text=Promo'">
                        </div>
                        <div class="promo-discount-badge">-40%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== CATEGORIES SECTION ========== -->
<section class="categories" data-aos="fade-up">
    <div class="section-header">
        <span class="section-subtitle">Koleksi</span>
        <h2 class="section-title">Shop by Category</h2>
        <div class="section-line"></div>
    </div>
    <div class="category-grid" id="categoryGrid"></div>
</section>

<!-- ========== FEATURED PRODUCTS (dari DATABASE) ========== -->
<section class="featured-products" id="featured" data-aos="fade-up">
    <div class="section-header">
        <span class="section-subtitle">Best Sellers</span>
        <h2 class="section-title">Most Loved Pieces</h2>
        <div class="section-line"></div>
    </div>
    <div class="products-grid" id="popularProducts">
    </div>
</section>
</div><!-- /home-page -->

<!-- ========== PRODUCTS PAGE (Hidden by default) ========== -->
<div id="products-page" class="hidden-page">
    <div class="products-container">
        <div class="products-header">
            <h1 class="page-title">Our Collection</h1>
            <p class="page-subtitle">Discover furniture that defines elegance and comfort</p>
        </div>
        <div class="products-grid" id="allProductsGrid"></div>
    </div>
</div>

<!-- ========== PROFILE PAGE ========== -->
<div id="profile-page" class="hidden-page">
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-avatar">
                <i class="fas fa-user-astronaut"></i>
            </div>
            <h3>Profile Settings</h3>
            <p class="profile-subtitle">Manage your personal information</p>
            
            <div class="profile-field">
                <label><i class="fas fa-user"></i> Nama Lengkap</label>
                <input type="text" id="profileName" value="Budi Santoso" disabled>
            </div>
            <div class="profile-field">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="profileEmail" value="budi@furirest.com" disabled>
            </div>
            <div class="profile-field">
                <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
                <textarea id="profileAddress" rows="2" disabled>Jl. Furniture No. 123, Jakarta</textarea>
            </div>
            <div class="profile-field">
                <label><i class="fas fa-phone"></i> No. Telepon</label>
                <input type="tel" id="profilePhone" value="0812-3456-7890" disabled>
            </div>
            
            <div class="profile-buttons">
                <button class="edit-toggle" id="editProfileBtn">Edit Profile</button>
                <button class="edit-toggle" id="saveProfileBtn" style="background:#C8A86B; display:none;">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== ORDER MODAL (FORM PESANAN) ========== -->
<div id="orderModal" class="order-modal">
    <div class="order-container">
        <span class="order-close" onclick="closeOrderForm()">&times;</span>
        <h3><i class="fas fa-shopping-cart" style="color: var(--gold);"></i> Form Pemesanan</h3>
        
        <?php if($success): ?>
        <div class="success-message"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" id="orderForm">
            <input type="hidden" name="product_id" id="orderProductId">
            <input type="hidden" name="product_price" id="orderProductPrice">
            <input type="hidden" name="order" value="1">
            
            <input type="text" name="customer_name" id="customerName" placeholder="Nama Lengkap" required>
            <input type="tel" name="customer_phone" id="customerPhone" placeholder="Nomor Telepon" required>
            <textarea name="customer_address" id="customerAddress" rows="2" placeholder="Alamat Lengkap" required></textarea>
            
            <div class="order-row">
                <input type="text" id="orderProductName" readonly style="background:#f5f2ed; flex:2;">
                <input type="number" name="quantity" id="orderQuantity" value="1" min="1" style="flex:1;" onchange="updateTotal()">
            </div>
            
            <div class="total-price">
                <span>Total Harga:</span>
                <span id="orderTotalPrice" style="color: var(--gold); font-size: 1.2rem;">Rp 0</span>
            </div>
            
            <button type="submit" class="order-submit">Pesan Sekarang →</button>
        </form>
    </div>
</div>

<!-- ========== CART SIDEBAR ========== -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3><i class="fas fa-shopping-bag"></i> Your Cart</h3>
        <i class="fas fa-times" id="closeCart"></i>
    </div>
    <div class="cart-items" id="cartItemsList"></div>
    <div class="cart-footer">
        <div class="cart-total" id="cartTotalPrice">Total: Rp 0</div>
        <button class="checkout-btn" id="checkoutBtn">Proceed to Checkout →</button>
    </div>
</div>

<!-- ========== SEARCH MODAL ========== -->
<div id="searchModal" class="search-modal">
    <div class="search-container">
        <div class="search-header">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search for furniture...">
            <i class="fas fa-times" id="closeSearch"></i>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>
</div>

<!-- ========== FOOTER ========== -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <img src="assets/images/logo/logo.jpg" alt="FurniRest Logo" class="logo-img" onerror="this.style.display='none'">
                    Furni<span>Rest</span>
                </div>
                <p class="footer-description">Premium luxury furniture that transforms your living space into a masterpiece of comfort and elegance.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-pinterest-p"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#" onclick="showPage('home')">Home</a></li>
                    <li><a href="#" onclick="showPage('products')">Shop Collection</a></li>
                    <li><a href="#" onclick="showPage('profile')">My Account</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Contact Us</h4>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Jl. Furniture Indah No. 88, Jakarta</li>
                    <li><i class="fas fa-phone"></i> +62 21 1234 5678</li>
                    <li><i class="fas fa-envelope"></i> info@furirest.com</li>
                    <li><i class="fas fa-clock"></i> Mon - Sat: 9am - 6pm</li>
                </ul>
            </div>
            
            <div class="footer-col newsletter-col">
                <h4>Newsletter</h4>
                <p class="newsletter-text">Subscribe to get special offers, free giveaways, and exclusive deals.</p>
                <div class="newsletter-form">
                    <input type="email" id="newsletterEmail" placeholder="Your email address">
                    <button id="subscribeBtn"><i class="fas fa-paper-plane"></i></button>
                </div>
                <p class="newsletter-note">*We never share your email with third parties</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; 2024 FurniRest. All rights reserved. | Premium Luxury Furniture</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ========== TOAST NOTIFICATION ========== -->
<div id="toast" class="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage"></span>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="script.js"></script>
<script>
AOS.init({ once: true, offset: 50, duration: 800 });
</script>
</body>
</html>