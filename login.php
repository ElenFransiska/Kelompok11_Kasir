<?php
session_start();

$message = '';
if (isset($_SESSION['login_message'])) {
    $message = $_SESSION['login_message'];
    unset($_SESSION['login_message']); // Clear the message after displaying
}

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- SIMULASI OTENTIKASI ADMIN (TANPA DATABASE) ---
    $valid_username = "admin";
    $valid_password = "password123"; // TIDAK AMAN untuk produksi!

    if ($username === $valid_username && $password === $valid_password) {
        // Otentikasi berhasil
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = 'Admin'; // Pastikan role adalah Admin
        $_SESSION['login_message'] = "Login berhasil! Selamat datang, " . htmlspecialchars($username) . ".";
        
        header("Location: admin_dashboard.php"); // Arahkan ke dashboard admin
        exit();
    } else {
        // Otentikasi gagal
        $_SESSION['login_message'] = "Username atau password salah. Silakan coba lagi.";
        header("Location: admin_login.php"); // Kembali ke halaman login admin
        exit();
    }
}

// Handle logout action (if logout is initiated from admin_dashboard, it will redirect here)
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    $_SESSION['login_message'] = "Anda telah berhasil logout.";
    header("Location: admin_login.php"); // Redirect back to admin login
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - Smart Laundry</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/css_style.css">
</head>
<body>
    <div class="container login-form-container">
        <h2>Masuk Sebagai Admin</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'berhasil') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="admin_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="login-button">
                <span class="arrow-icon">&rarr;</span> Masuk
            </button>
            <p class="back-link"><a href="index.php">&larr; Kembali ke Pilihan</a></p>
        </form>
    </div>
</body>
</html>