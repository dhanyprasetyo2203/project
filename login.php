<?php
session_start();
if (isset($_SESSION['furni_admin']) && $_SESSION['furni_admin'] === true) {
    header('Location: admin-furni/dashboard.php'); exit;
}
if (isset($_SESSION['furni_user']) && $_SESSION['furni_user'] === true) {
    header('Location: index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '') {
        $error = 'Nama tidak boleh kosong.';
    } elseif ($username === 'admin' && $password === 'furni123') {
        $_SESSION['furni_admin']    = true;
        $_SESSION['furni_username'] = 'admin';
        header('Location: admin-furni/dashboard.php'); exit;
    } elseif ($username === 'admin' && $password !== 'furni123') {
        $error = 'Password admin salah!';
    } else {
        $_SESSION['furni_user'] = true;
        $_SESSION['furni_nama'] = htmlspecialchars($username);
        header('Location: index.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniRest — Masuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            background:#FDFBF7;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:2rem 1rem;
        }
        .wrap{width:100%;max-width:420px}

        /* Logo — sama persis dengan navbar user */
        .logo-area{text-align:center;margin-bottom:2rem}
        .logo-area h1{font-size:1.9rem;font-weight:800;color:#1A1A1A;letter-spacing:-.02em}
        .logo-area h1 span{color:#C8A86B}
        .logo-area p{color:#6B6B6B;font-size:.85rem;margin-top:.3rem}

        /* Card — sama seperti profile-card user */
        .card{
            background:#fff;
            border-radius:32px;
            padding:2.5rem;
            box-shadow:0 20px 40px -12px rgba(0,0,0,.12);
        }

        .card-title{font-size:1.1rem;font-weight:700;color:#1A1A1A;margin-bottom:.25rem}
        .card-sub{font-size:.82rem;color:#6B6B6B;margin-bottom:1.8rem;line-height:1.5}

        /* Error */
        .error-box{
            background:#fff0f0;color:#c0392b;
            border:1px solid #fcc;
            padding:.7rem 1rem;border-radius:16px;
            font-size:.83rem;margin-bottom:1.2rem;
            display:flex;align-items:center;gap:.5rem;
        }

        /* Form — sama seperti form user store */
        .form-group{margin-bottom:1.1rem}
        .form-group label{
            display:block;font-weight:600;
            margin-bottom:.5rem;font-size:.85rem;color:#1A1A1A;
        }
        .form-group label i{margin-right:.4rem;color:#C8A86B}
        .form-group input{
            width:100%;padding:.85rem 1rem;
            border:1.5px solid #E0DCD5;border-radius:16px;
            font-family:inherit;font-size:.93rem;
            background:#FAFAFA;color:#1A1A1A;
            transition:border-color .2s,box-shadow .2s;
        }
        .form-group input:focus{
            outline:none;border-color:#C8A86B;
            box-shadow:0 0 0 3px rgba(200,168,107,.1);
            background:#fff;
        }
        .hint{font-size:.75rem;color:#A0A0A0;margin-top:.35rem}
        .hint i{margin-right:.3rem}

        /* Button — sama seperti btn-primary user */
        .btn{
            width:100%;padding:.9rem;
            background:#1A1A1A;color:#fff;
            border:none;border-radius:50px;
            font-family:inherit;font-weight:600;font-size:.95rem;
            cursor:pointer;transition:all .3s;
            box-shadow:0 10px 30px -8px rgba(0,0,0,.2);
            margin-top:.3rem;
        }
        .btn:hover{background:#C8A86B;transform:translateY(-3px);box-shadow:0 15px 35px -10px rgba(200,168,107,.35)}

        /* Divider */
        .divider{
            display:flex;align-items:center;gap:.8rem;
            margin:1.5rem 0;font-size:.75rem;color:#C0BAB0;
        }
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#E0DCD5}

        .bottom-note{text-align:center;font-size:.78rem;color:#A0A0A0;margin-top:1rem}
        .bottom-note strong{color:#C8A86B}
    </style>
</head>
<body>
<div class="wrap">
    <div class="logo-area">
        <h1>Furni<span>Rest</span></h1>
        <p>Premium Luxury Furniture</p>
    </div>

    <div class="card">
        <p class="card-title">Selamat Datang</p>
        <p class="card-sub">Masukkan nama untuk berbelanja, atau ketuk <strong>admin</strong> dengan password khusus untuk masuk ke panel pengelola.</p>

        <?php if ($error): ?>
        <div class="error-box"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i>Nama / Username</label>
                <input type="text" name="username"
                       placeholder="Nama kamu atau 'admin'"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autofocus required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i>Password</label>
                <input type="password" name="password" placeholder="Kosongkan jika bukan admin">
                <p class="hint"><i class="fas fa-info-circle"></i>Pelanggan tidak perlu password</p>
            </div>
            <button type="submit" class="btn">Masuk &rarr;</button>
        </form>

        <div class="divider">atau</div>
        <div class="bottom-note">
            Admin: <strong>admin</strong> / <strong>furni123</strong>
        </div>
    </div>
</div>
</body>
</html>
