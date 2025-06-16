<?php
// home.php - Halaman pembuka dengan link ke menu, order dan history
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kasir - Beranda</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7fafc;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            gap: 32px;
            color: #111827;
        }
        h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #8b5cf6 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        nav {
            display: flex;
            gap: 24px;
        }
        a.button {
            background: #06b6d4;
            color: white;
            padding: 16px 28px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(6,182,212,0.4);
            transition: background-color 0.3s ease;
        }
        a.button:hover {
            background: #0ea5e9;
        }
    </style>
</head>
<body>
    <h1>Selamat Datang di Sistem Kasir</h1>
    <nav>
        <a class="button" href="menu.php">Lihat Menu</a>
        <a class="button" href="order.php">Pesanan Saya</a>
        <a class="button" href="history.php">Riwayat Pesanan</a>
    </nav>
</body>
</html>

