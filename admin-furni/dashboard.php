<?php
session_start();
if (!isset($_SESSION['furni_admin']) || $_SESSION['furni_admin'] !== true) {
    header('Location: ../login.php'); exit;
}
require_once '../config.php';

$products = $db->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$orders   = $db->query("
    SELECT o.*, p.name AS product_name, p.image AS product_image
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    ORDER BY o.order_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$totalRevenue  = array_sum(array_column($orders, 'total_price'));
$pendingOrders = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — FurniRest</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    /* ===== RESET & VARIABLES (sama persis user store) ===== */
    *{margin:0;padding:0;box-sizing:border-box}
    :root{
        --bg:#FDFBF7;
        --card:#FFFFFF;
        --gold:#C8A86B;
        --gold-dark:#B8964A;
        --dark:#1A1A1A;
        --gray-light:#F5F2ED;
        --border:#E0DCD5;
        --muted:#6B6B6B;
        --shadow-sm:0 10px 30px -8px rgba(0,0,0,.08);
        --shadow-md:0 20px 40px -12px rgba(0,0,0,.12);
        --shadow-gold:0 15px 35px -10px rgba(200,168,107,.25);
        --tr:all .3s cubic-bezier(.2,0,0,1);
        --green:#10b981;--red:#ef4444;--blue:#3b82f6;--orange:#f59e0b;
    }
    body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--dark)}

    /* ===== NAVBAR — sama persis user store ===== */
    .navbar{
        position:fixed;top:0;width:100%;
        background:rgba(253,251,247,.96);
        backdrop-filter:blur(15px);
        padding:1rem 6%;
        display:flex;justify-content:space-between;align-items:center;
        z-index:1000;
        border-bottom:1px solid rgba(200,168,107,.15);
    }
    .nav-logo{display:flex;align-items:center;gap:.5rem;font-size:1.45rem;font-weight:800;color:var(--dark);text-decoration:none}
    .nav-logo span{color:var(--gold)}
    .nav-logo small{
        background:var(--gray-light);color:var(--muted);
        font-size:.6rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
        padding:.2rem .6rem;border-radius:50px;margin-left:.4rem;
    }
    .nav-right{display:flex;align-items:center;gap:1rem}
    .nav-tab{
        background:none;border:none;font-family:inherit;font-size:.9rem;
        font-weight:500;color:var(--muted);cursor:pointer;padding:.4rem .2rem;
        position:relative;transition:var(--tr);
    }
    .nav-tab::after{
        content:'';position:absolute;bottom:-4px;left:0;width:0;height:2px;
        background:var(--gold);transition:width .3s;
    }
    .nav-tab.active,
    .nav-tab:hover{color:var(--dark)}
    .nav-tab.active::after,
    .nav-tab:hover::after{width:100%}
    .btn-add-nav{
        background:var(--dark);color:#fff;border:none;
        padding:.6rem 1.3rem;border-radius:50px;font-family:inherit;
        font-weight:600;font-size:.85rem;cursor:pointer;transition:var(--tr);
        display:flex;align-items:center;gap:.4rem;
    }
    .btn-add-nav:hover{background:var(--gold);color:var(--dark);transform:translateY(-2px)}
    .logout-link{
        color:var(--muted);font-size:.9rem;text-decoration:none;
        display:flex;align-items:center;gap:.4rem;
        padding:.4rem .2rem;transition:var(--tr);
    }
    .logout-link:hover{color:#ef4444}

    /* ===== CONTENT WRAP ===== */
    .wrap{max-width:1300px;margin:0 auto;padding:100px 6% 80px}

    /* ===== STATS GRID ===== */
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.2rem;margin-bottom:2.5rem}
    .stat-card{
        background:var(--card);border-radius:24px;padding:1.4rem;
        box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:1rem;
    }
    .stat-icon{
        width:52px;height:52px;border-radius:16px;
        display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;
    }
    .ic-gold{background:rgba(200,168,107,.12);color:var(--gold)}
    .ic-blue{background:rgba(59,130,246,.1);color:var(--blue)}
    .ic-orange{background:rgba(245,158,11,.1);color:var(--orange)}
    .ic-green{background:rgba(16,185,129,.1);color:var(--green)}
    .stat-val{font-size:1.55rem;font-weight:800;line-height:1}
    .stat-lbl{font-size:.75rem;color:var(--muted);margin-top:.3rem}

    /* ===== SECTION HEADER ===== */
    .section-header{
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:1.8rem;
    }
    .section-left{}
    .section-subtitle-tag{
        color:var(--gold);text-transform:uppercase;font-size:.75rem;
        letter-spacing:3px;font-weight:600;
    }
    .section-title-h{font-size:1.6rem;font-weight:700;margin-top:.15rem}
    .section-line{width:50px;height:3px;background:var(--gold);border-radius:3px;margin-top:.5rem}

    /* ===== FILTER TABS ===== */
    .page-tabs{display:flex;gap:.5rem;background:var(--gray-light);border-radius:50px;padding:5px;width:fit-content;margin-bottom:2rem}
    .page-tab{
        padding:.55rem 1.4rem;border:none;border-radius:50px;
        font-family:inherit;font-size:.85rem;font-weight:600;cursor:pointer;
        background:transparent;color:var(--muted);transition:var(--tr);
    }
    .page-tab.active{background:var(--dark);color:#fff;box-shadow:var(--shadow-sm)}

    .tab-pane{display:none}
    .tab-pane.active{display:block}

    /* ===== PRODUCTS GRID — sama persis user store ===== */
    .products-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(270px,1fr));
        gap:2rem;
    }
    .product-card{
        background:var(--card);border-radius:24px;overflow:hidden;
        box-shadow:var(--shadow-sm);transition:var(--tr);
        display:flex;flex-direction:column;
    }
    .product-card:hover{transform:translateY(-10px);box-shadow:var(--shadow-md)}
    .product-card img{
        width:100%;height:260px;object-fit:cover;
        background:var(--gray-light);transition:transform .5s;display:block;
    }
    .product-card:hover img{transform:scale(1.03)}
    .product-info{padding:1.4rem;flex:1;display:flex;flex-direction:column}
    .product-category{
        color:var(--gold);font-size:.7rem;text-transform:uppercase;
        letter-spacing:1px;margin-bottom:.3rem;font-weight:600;
    }
    .product-title{font-weight:700;font-size:1.05rem;margin-bottom:.3rem}
    .product-price{font-size:1.2rem;font-weight:700;color:var(--dark);margin-bottom:.8rem}
    .product-desc{
        font-size:.8rem;color:var(--muted);line-height:1.5;flex:1;
        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
    }
    .product-actions{
        display:flex;gap:.6rem;padding:1rem 1.4rem;
        border-top:1px solid var(--gray-light);
    }
    .btn-edit{
        flex:1;padding:.65rem;border:1.5px solid var(--border);border-radius:50px;
        background:transparent;font-family:inherit;font-weight:600;font-size:.82rem;
        cursor:pointer;transition:var(--tr);color:var(--dark);
    }
    .btn-edit:hover{background:var(--dark);color:#fff;border-color:var(--dark)}
    .btn-del{
        flex:1;padding:.65rem;border:1.5px solid #fcc;border-radius:50px;
        background:transparent;font-family:inherit;font-weight:600;font-size:.82rem;
        cursor:pointer;transition:var(--tr);color:#ef4444;
    }
    .btn-del:hover{background:#ef4444;color:#fff;border-color:#ef4444}

    .empty-state{
        text-align:center;padding:4rem 2rem;color:var(--muted);
        background:var(--card);border-radius:24px;box-shadow:var(--shadow-sm);
    }
    .empty-state i{font-size:3rem;color:var(--border);margin-bottom:1rem;display:block}
    .empty-state p{font-size:.9rem}

    /* ===== ORDERS TABLE ===== */
    .table-wrap{background:var(--card);border-radius:24px;box-shadow:var(--shadow-sm);overflow:hidden}
    .orders-table{width:100%;border-collapse:collapse}
    .orders-table th{
        background:var(--gray-light);padding:.9rem 1.2rem;text-align:left;
        font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;
        color:var(--muted);border-bottom:1px solid var(--border);
    }
    .orders-table td{
        padding:.9rem 1.2rem;border-bottom:1px solid var(--gray-light);
        font-size:.85rem;vertical-align:middle;
    }
    .orders-table tr:last-child td{border-bottom:none}
    .orders-table tr:hover td{background:#FDFBF7}
    .order-id{font-weight:700;color:var(--gold)}
    .cust-name{font-weight:600;margin-bottom:.1rem}
    .cust-phone{font-size:.75rem;color:var(--muted)}
    .prod-cell{display:flex;align-items:center;gap:.7rem}
    .prod-cell img{width:40px;height:40px;object-fit:cover;border-radius:10px;flex-shrink:0}
    .prod-cell span{font-weight:500;font-size:.83rem}
    .badge{
        display:inline-flex;align-items:center;gap:.3rem;
        padding:.3rem .8rem;border-radius:50px;font-size:.72rem;font-weight:700;
    }
    .b-pending{background:rgba(245,158,11,.1);color:var(--orange)}
    .b-processing{background:rgba(59,130,246,.1);color:var(--blue)}
    .b-completed{background:rgba(16,185,129,.1);color:var(--green)}
    .b-cancelled{background:rgba(239,68,68,.08);color:var(--red)}
    .status-sel{
        padding:.4rem .8rem;border:1.5px solid var(--border);border-radius:50px;
        font-family:inherit;font-size:.8rem;background:#fff;cursor:pointer;
    }
    .status-sel:focus{outline:none;border-color:var(--gold)}
    .btn-trash{
        width:34px;height:34px;border:1.5px solid #fcc;background:transparent;
        border-radius:10px;cursor:pointer;color:#ef4444;transition:var(--tr);
        display:flex;align-items:center;justify-content:center;
    }
    .btn-trash:hover{background:#ef4444;color:#fff;border-color:#ef4444}

    /* ===== MODAL ===== */
    .modal-overlay{
        display:none;position:fixed;inset:0;
        background:rgba(0,0,0,.6);backdrop-filter:blur(6px);
        z-index:2000;align-items:center;justify-content:center;padding:1rem;
    }
    .modal-overlay.open{display:flex}
    .modal{
        background:#fff;border-radius:32px;width:100%;max-width:540px;
        max-height:90vh;overflow-y:auto;
        box-shadow:0 30px 50px -15px rgba(0,0,0,.2);
        animation:mIn .25s ease;
    }
    @keyframes mIn{from{opacity:0;transform:scale(.96) translateY(8px)}to{opacity:1;transform:scale(1) translateY(0)}}
    .modal-head{
        display:flex;justify-content:space-between;align-items:center;
        padding:1.6rem 2rem;border-bottom:1px solid var(--border);
        position:sticky;top:0;background:#fff;z-index:1;border-radius:32px 32px 0 0;
    }
    .modal-head h3{font-size:1.1rem;font-weight:700}
    .modal-close{
        width:36px;height:36px;background:var(--gray-light);border:none;
        border-radius:12px;cursor:pointer;font-size:1rem;color:var(--muted);
        display:flex;align-items:center;justify-content:center;transition:var(--tr);
    }
    .modal-close:hover{background:#ef4444;color:#fff}
    .modal-body{padding:1.6rem 2rem}
    .field{margin-bottom:1.1rem}
    .field label{
        display:block;font-weight:600;margin-bottom:.45rem;
        font-size:.83rem;color:var(--dark);
    }
    .field label i{margin-right:.35rem;color:var(--gold)}
    .field input,.field textarea,.field select{
        width:100%;padding:.82rem 1rem;
        border:1.5px solid var(--border);border-radius:16px;
        font-family:inherit;font-size:.9rem;color:var(--dark);
        background:#FAFAF8;transition:border-color .2s;
    }
    .field input:focus,.field textarea:focus{
        outline:none;border-color:var(--gold);background:#fff;
    }
    .field-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}

    /* Upload area */
    .upload-area{
        border:2px dashed var(--border);border-radius:20px;
        padding:1.5rem;text-align:center;cursor:pointer;
        transition:var(--tr);background:#FAFAF8;
    }
    .upload-area:hover,.upload-area.drag{
        border-color:var(--gold);background:rgba(200,168,107,.04);
    }
    .upload-area i{font-size:2rem;color:var(--border);margin-bottom:.5rem;display:block}
    .upload-area.drag i{color:var(--gold)}
    .upload-area p{font-size:.83rem;color:var(--muted)}
    .upload-area strong{color:var(--gold);cursor:pointer}
    .upload-area input[type=file]{display:none}
    .img-preview{
        width:100%;height:160px;object-fit:cover;border-radius:16px;
        margin-top:.8rem;display:none;border:1.5px solid var(--border);
    }
    .img-preview.show{display:block}
    .url-or{
        display:flex;align-items:center;gap:.6rem;
        margin:.75rem 0;font-size:.75rem;color:#C0BAB0;
    }
    .url-or::before,.url-or::after{content:'';flex:1;height:1px;background:var(--border)}

    .modal-foot{
        padding:1.2rem 2rem 2rem;display:flex;gap:.8rem;
        position:sticky;bottom:0;background:#fff;border-top:1px solid var(--border);
    }
    .btn-save{
        flex:1;padding:.88rem;border:none;border-radius:50px;
        background:var(--dark);color:#fff;font-family:inherit;
        font-weight:700;font-size:.95rem;cursor:pointer;transition:var(--tr);
    }
    .btn-save:hover{background:var(--gold);color:var(--dark)}
    .btn-cancel{
        padding:.88rem 1.5rem;border:1.5px solid var(--border);border-radius:50px;
        background:#fff;color:var(--muted);font-family:inherit;
        font-weight:600;cursor:pointer;transition:var(--tr);
    }
    .btn-cancel:hover{border-color:var(--dark);color:var(--dark)}

    /* ===== TOAST — sama seperti user store ===== */
    .toast{
        position:fixed;bottom:30px;left:50%;
        transform:translateX(-50%) translateY(100px);
        background:var(--dark);color:#fff;
        padding:1rem 2rem;border-radius:50px;
        display:flex;align-items:center;gap:.8rem;
        z-index:3000;transition:transform .3s;
        box-shadow:var(--shadow-md);font-size:.88rem;font-weight:600;
        white-space:nowrap;
    }
    .toast.show{transform:translateX(-50%) translateY(0)}
    .toast .ti{color:#4CAF50;font-size:1.1rem}
    .toast.err .ti{color:#ef4444}

    /* ===== RESPONSIVE ===== */
    @media(max-width:900px){
        .stats-grid{grid-template-columns:repeat(2,1fr)}
        .wrap{padding:90px 4% 80px}
    }
    @media(max-width:600px){
        .stats-grid{grid-template-columns:1fr 1fr}
        .wrap{padding:85px 1rem 80px}
        .navbar{padding:1rem 1rem}
        .products-grid{grid-template-columns:1fr}
        .field-row{grid-template-columns:1fr}
        .nav-right .nav-tab:not(.active){display:none}
        .orders-table{min-width:600px}
        .table-wrap{overflow-x:auto}
        .modal-body,.modal-head,.modal-foot{padding-left:1.2rem;padding-right:1.2rem}
    }
    </style>
</head>
<body>

<!-- ===== NAVBAR (gaya user store) ===== -->
<nav class="navbar">
    <a class="nav-logo" href="#">
        Furni<span>Rest</span><small>Admin</small>
    </a>
    <div style="display:flex;gap:2rem">
        <button class="nav-tab active" data-tab="products">Produk</button>
        <button class="nav-tab" data-tab="orders">Pesanan</button>
    </div>
    <div class="nav-right">
        <a href="../index.php" target="_blank" class="logout-link" title="Lihat Toko">
            <i class="fas fa-store"></i>
        </a>
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</nav>

<div class="wrap">

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon ic-gold"><i class="fas fa-box-open"></i></div>
            <div><div class="stat-val"><?= count($products) ?></div><div class="stat-lbl">Total Produk</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-blue"><i class="fas fa-shopping-bag"></i></div>
            <div><div class="stat-val"><?= count($orders) ?></div><div class="stat-lbl">Total Pesanan</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-orange"><i class="fas fa-clock"></i></div>
            <div><div class="stat-val"><?= $pendingOrders ?></div><div class="stat-lbl">Pending</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-green"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-val">Rp&thinsp;<?= $totalRevenue >= 1000000 ? number_format($totalRevenue/1000000,1).'jt' : number_format($totalRevenue,0,',','.') ?></div>
                <div class="stat-lbl">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- ===== TAB PRODUK ===== -->
    <div class="tab-pane active" id="pane-products">
        <div class="section-header">
            <div class="section-left">
                <div class="section-subtitle-tag">Koleksi</div>
                <h2 class="section-title-h">Daftar Produk</h2>
                <div class="section-line"></div>
            </div>
            <button class="btn-add-nav" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Produk
            </button>
        </div>

        <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>Belum ada produk. Klik <strong>Tambah Produk</strong> untuk mulai.</p>
        </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card" id="card-<?= $p['id'] ?>">
                <img src="../<?= htmlspecialchars($p['image'] ?? '') ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     onerror="this.src='https://placehold.co/400x260?text=No+Image'">
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars($p['category'] ?? '—') ?></div>
                    <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                    <?php if (!empty($p['description'])): ?>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="product-actions">
                    <button class="btn-edit" onclick='openModal(<?= json_encode($p) ?>)'>
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button class="btn-del" onclick="deleteProduct(<?= $p['id'] ?>)">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ===== TAB PESANAN ===== -->
    <div class="tab-pane" id="pane-orders">
        <div class="section-header">
            <div class="section-left">
                <div class="section-subtitle-tag">Manajemen</div>
                <h2 class="section-title-h">Daftar Pesanan</h2>
                <div class="section-line"></div>
            </div>
        </div>

        <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <p>Belum ada pesanan masuk.</p>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order</th><th>Customer</th><th>Produk</th>
                        <th>Qty</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o):
                    $bClass = match($o['status']) {
                        'processing' => 'b-processing',
                        'completed'  => 'b-completed',
                        'cancelled'  => 'b-cancelled',
                        default      => 'b-pending'
                    };
                ?>
                <tr id="orow-<?= $o['id'] ?>">
                    <td><span class="order-id">#<?= $o['id'] ?></span></td>
                    <td>
                        <div class="cust-name"><?= htmlspecialchars($o['customer_name']) ?></div>
                        <div class="cust-phone"><?= htmlspecialchars($o['customer_phone']) ?></div>
                    </td>
                    <td>
                        <div class="prod-cell">
                            <?php if (!empty($o['product_image'])): ?>
                            <img src="../<?= htmlspecialchars($o['product_image']) ?>"
                                 onerror="this.style.display='none'">
                            <?php endif; ?>
                            <span><?= htmlspecialchars($o['product_name'] ?? 'Produk dihapus') ?></span>
                        </div>
                    </td>
                    <td><?= $o['quantity'] ?>×</td>
                    <td><strong>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></strong></td>
                    <td>
                        <select class="status-sel" data-id="<?= $o['id'] ?>" onchange="updateStatus(this)">
                            <option value="pending"    <?= $o['status']==='pending'    ?'selected':'' ?>>⏳ Pending</option>
                            <option value="processing" <?= $o['status']==='processing' ?'selected':'' ?>>🔄 Processing</option>
                            <option value="completed"  <?= $o['status']==='completed'  ?'selected':'' ?>>✅ Completed</option>
                            <option value="cancelled"  <?= $o['status']==='cancelled'  ?'selected':'' ?>>❌ Cancelled</option>
                        </select>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></td>
                    <td>
                        <button class="btn-trash" onclick="deleteOrder(<?= $o['id'] ?>)" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /wrap -->

<!-- ===== MODAL TAMBAH / EDIT PRODUK ===== -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-head">
            <h3 id="modalTitle">Tambah Produk</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="pid">

            <div class="field">
                <label><i class="fas fa-tag"></i>Nama Produk</label>
                <input type="text" id="pname" placeholder="cth: Nordic Velvet Sofa" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label><i class="fas fa-th-list"></i>Kategori</label>
                    <input type="text" id="pcat" placeholder="Sofa, Kursi, Meja…">
                </div>
                <div class="field">
                    <label><i class="fas fa-coins"></i>Harga (Rp)</label>
                    <input type="number" id="pprice" placeholder="4500000" required>
                </div>
            </div>
            <div class="field">
                <label><i class="fas fa-boxes"></i>Stok</label>
                <input type="number" id="pstock" placeholder="50" min="0" value="50">
            </div>

            <!-- Upload gambar dari file laptop -->
            <div class="field">
                <label><i class="fas fa-image"></i>Gambar Produk</label>
                <div class="upload-area" id="uploadArea">
                    <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp,image/gif">
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#E0DCD5;margin-bottom:.5rem;display:block"></i>
                        <p style="font-size:.85rem;color:#6B6B6B">Klik di sini atau seret foto dari laptop</p>
                        <p style="font-size:.72rem;color:#C0BAB0;margin-top:.25rem">JPG, PNG, WebP — maks 5 MB</p>
                    </div>
                    <div id="previewWrap" style="display:none;position:relative">
                        <img id="imgPreview" src="" alt="Preview" style="width:100%;height:160px;object-fit:cover;border-radius:12px;display:block">
                        <button type="button" id="changeImgBtn" style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.55);color:#fff;border:none;border-radius:8px;padding:.35rem .75rem;font-size:.75rem;font-family:inherit;cursor:pointer;font-weight:600">
                            <i class="fas fa-camera"></i> Ganti
                        </button>
                    </div>
                </div>
                <div class="url-or">atau masukkan URL gambar</div>
                <input type="text" id="pimg" placeholder="assets/images/products/..." oninput="onUrlChange(this.value)">
            </div>

            <div class="field">
                <label><i class="fas fa-align-left"></i>Deskripsi</label>
                <textarea id="pdesc" rows="3" placeholder="Deskripsi singkat produk…"></textarea>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <button class="btn-save" id="btnSave" onclick="saveProduct()">
                <i class="fas fa-save"></i> Simpan Produk
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle ti"></i>
    <span id="toastMsg"></span>
</div>

<script>
// ===== TAB SWITCH =====
document.querySelectorAll('[data-tab]').forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.dataset.tab;
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('[data-tab]').forEach(b => b.classList.remove('active'));
        document.getElementById('pane-' + tab).classList.add('active');
        document.querySelectorAll('[data-tab="' + tab + '"]').forEach(b => b.classList.add('active'));
    });
});

// ===== TOAST =====
function showToast(msg, type = 'ok') {
    const t = document.getElementById('toast');
    t.className = 'toast' + (type === 'err' ? ' err' : '');
    t.querySelector('.ti').className = 'ti fas ' + (type === 'err' ? 'fa-times-circle' : 'fa-check-circle');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ===== MODAL =====
function setPreview(src) {
    const wrap   = document.getElementById('previewWrap');
    const prompt = document.getElementById('uploadPrompt');
    const img    = document.getElementById('imgPreview');
    if (src) {
        img.src = src;
        wrap.style.display   = 'block';
        prompt.style.display = 'none';
    } else {
        wrap.style.display   = 'none';
        prompt.style.display = 'block';
    }
}

function openModal(p) {
    document.getElementById('pid').value     = p ? p.id               : '';
    document.getElementById('pname').value   = p ? p.name             : '';
    document.getElementById('pcat').value    = p ? (p.category  || '') : '';
    document.getElementById('pprice').value  = p ? p.price            : '';
    document.getElementById('pstock').value  = p ? (p.stock ?? 50)    : 50;
    document.getElementById('pimg').value    = p ? (p.image     || '') : '';
    document.getElementById('pdesc').value   = p ? (p.description||'') : '';
    document.getElementById('fileInput').value = '';
    document.getElementById('modalTitle').textContent = p ? 'Edit Produk' : 'Tambah Produk';

    setPreview(p && p.image ? '../' + p.image : '');
    document.getElementById('modalOverlay').classList.add('open');
}
function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}
document.getElementById('modalOverlay').addEventListener('click', e => {
    if (e.target === document.getElementById('modalOverlay')) closeModal();
});

// URL input → preview
function onUrlChange(val) {
    if (val.trim()) {
        setPreview('../' + val.trim());
    } else {
        setPreview('');
    }
}

// ===== FILE UPLOAD =====
const fileInput  = document.getElementById('fileInput');
const uploadArea = document.getElementById('uploadArea');

// Klik di mana saja dalam kotak upload → buka file dialog
uploadArea.addEventListener('click', e => {
    if (e.target === fileInput) return; // jangan double-trigger
    fileInput.click();
});

// Tombol "Ganti" di atas preview
document.getElementById('changeImgBtn').addEventListener('click', e => {
    e.stopPropagation();
    fileInput.click();
});

// Drag & drop
uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.borderColor = '#C8A86B'; });
uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor = ''; });
uploadArea.addEventListener('drop', e => {
    e.preventDefault();
    uploadArea.style.borderColor = '';
    const f = e.dataTransfer.files[0];
    if (f) handleFile(f);
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length) handleFile(fileInput.files[0]);
});

function handleFile(file) {
    const allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!allowed.includes(file.type)) {
        showToast('Format harus JPG, PNG, WebP, atau GIF', 'err'); return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showToast('Ukuran file maksimal 5 MB', 'err'); return;
    }

    // Preview lokal dulu (langsung muncul)
    const reader = new FileReader();
    reader.onload = ev => setPreview(ev.target.result);
    reader.readAsDataURL(file);

    // Upload ke server
    const fd = new FormData();
    fd.append('image', file);
    setBtnLoading(true, 'Mengunggah…');

    fetch('../admin-api-furni/upload_image.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            setBtnLoading(false);
            if (data.success) {
                document.getElementById('pimg').value = data.path;
                showToast('Gambar berhasil diunggah ✓');
            } else {
                showToast(data.message || 'Gagal upload', 'err');
                // Kembalikan ke preview sebelumnya jika gagal
                const oldVal = document.getElementById('pimg').value;
                setPreview(oldVal ? '../' + oldVal : '');
            }
        })
        .catch(() => {
            setBtnLoading(false);
            showToast('Koneksi error saat upload', 'err');
        });
}

// ===== SAVE PRODUCT =====
function setBtnLoading(loading, txt = 'Menyimpan…') {
    const btn = document.getElementById('btnSave');
    btn.disabled = loading;
    btn.innerHTML = loading
        ? '<i class="fas fa-spinner fa-spin"></i> ' + txt
        : '<i class="fas fa-save"></i> Simpan Produk';
}

function saveProduct() {
    const name  = document.getElementById('pname').value.trim();
    const price = parseFloat(document.getElementById('pprice').value);
    if (!name || !price) { showToast('Nama dan harga wajib diisi', 'err'); return; }

    const payload = {
        id:          document.getElementById('pid').value || null,
        name,
        category:    document.getElementById('pcat').value.trim(),
        price,
        stock:       parseInt(document.getElementById('pstock').value) || 0,
        image:       document.getElementById('pimg').value.trim(),
        description: document.getElementById('pdesc').value.trim()
    };

    setBtnLoading(true);
    fetch('../admin-api-furni/save_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        setBtnLoading(false);
        if (data.success) {
            closeModal();
            showToast(payload.id ? 'Produk diperbarui!' : 'Produk ditambahkan!');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('Gagal: ' + data.message, 'err');
        }
    })
    .catch(() => { setBtnLoading(false); showToast('Koneksi error', 'err'); });
}

// ===== DELETE PRODUCT =====
function deleteProduct(id) {
    if (!confirm('Yakin ingin menghapus produk ini?')) return;
    fetch('../admin-api-furni/delete_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('card-' + id)?.remove(); showToast('Produk dihapus'); }
        else showToast('Gagal: ' + data.message, 'err');
    });
}

// ===== UPDATE ORDER STATUS =====
function updateStatus(sel) {
    fetch('../admin-api-furni/update_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: sel.dataset.id, status: sel.value })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showToast('Status diperbarui');
        else showToast('Gagal update status', 'err');
    });
}

// ===== DELETE ORDER =====
function deleteOrder(id) {
    if (!confirm('Yakin hapus pesanan #' + id + '?')) return;
    fetch('../admin-api-furni/delete-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('orow-' + id)?.remove(); showToast('Pesanan dihapus'); }
        else showToast('Gagal: ' + data.message, 'err');
    });
}
</script>
</body>
</html>
