<?php
// Redirect ke login terpadu
header('Location: ../login.php');
exit;

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($username === 'admin' && $password === 'furni123') {
        $_SESSION['furni_admin'] = true;
        $_SESSION['furni_username'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!-- LANJUTKAN DENGAN HTML YANG SAMA (tidak berubah) -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FurniRest</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1A1A1A 0%, #2C2C2C 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container { width: 100%; max-width: 420px; padding: 2rem; }
        .login-card {
            background: white;
            border-radius: 32px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo h1 { font-size: 1.8rem; color: #1A1A1A; }
        .login-logo span { color: #C8A86B; }
        .login-logo p { color: #6B6B6B; font-size: 0.85rem; margin-top: 0.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.85rem; color: #1A1A1A; }
        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1.5px solid #E0DCD5;
            border-radius: 16px;
            font-family: inherit;
        }
        .form-group input:focus { outline: none; border-color: #C8A86B; }
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: #1A1A1A;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-login:hover { background: #C8A86B; transform: translateY(-2px); }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.85rem;
        }
        .login-info {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #E0DCD5;
            text-align: center;
            font-size: 0.8rem;
            color: #6B6B6B;
        }
        .login-info strong { color: #C8A86B; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <h1>Furni<span>Rest</span></h1>
                <p>Admin Panel</p>
            </div>
            
            <?php if($error): ?>
                <div class="error-message">❌ <?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-info">
                <p>Login: <strong>admin</strong> / <strong>furni123</strong></p>
            </div>
        </div>
    </div>
</body>
</html>