<?php
session_start();

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Data simulasi (dalam aplikasi nyata ambil dari database)
$todaySales = 2850000;
$monthlySales = 42500000;
$popularItems = [
    ['name' => 'Kopi Latte', 'sold' => 128],
    ['name' => 'Croissant', 'sold' => 95],
    ['name' => 'Sandwich', 'sold' => 87]
];
$weeklyData = [
    'Sen' => 3500000,
    'Sel' => 4200000,
    'Rab' => 3850000,
    'Kam' => 4500000,
    'Jum' => 5200000,
    'Sab' => 6100000,
    'Min' => 5800000
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #06b6d4;
            --primary-dark: #0e7490;
            --secondary: #8b5cf6;
            --light: #f8fafc;
            --dark: #1e293b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --info: #3b82f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: bold;
        }
        
        .admin-name {
            font-weight: 500;
        }
        
        .admin-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .nav-menu {
            padding: 1.5rem;
        }
        
        .nav-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            text-decoration: none;
            color: white;
        }
        
        .nav-item.active, .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        /* Main Content Styles */
        .main-content {
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }
        
        .user-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-notification {
            background-color: white;
            color: var(--dark);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .btn-notification:hover {
            background-color: #f8fafc;
        }
        
        .btn-logout {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-logout:hover {
            background-color: #dc2626;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .sales .card-icon {
            background-color: rgba(6, 182, 212, 0.1);
            color: var(--primary);
        }
        
        .revenue .card-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .orders .card-icon {
            background-color: rgba(249, 115, 22, 0.1);
            color: #f97316;
        }
        
        .customers .card-icon {
            background-color: rgba(139, 92, 246, 0.1);
            color: var(--secondary);
        }
        
        .card-value {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .card-change {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .positive {
            color: var(--success);
        }
        
        .negative {
            color: var(--danger);
        }
        
        /* Charts & Tables */
        .grid-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .chart-container, .popular-items {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .chart-placeholder {
            height: 300px;
            background-color: #f8fafc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
        }
        
        .item-list {
            list-style: none;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        .item-count {
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="admin-info">
                    <div class="admin-avatar"><?php echo substr($_SESSION['name'], 0, 1); ?></div>
                    <div>
                        <div class="admin-name"><?php echo $_SESSION['name']; ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                </div>
            </div>
            
            <div class="nav-menu">
                <div class="nav-title">Menu Utama</div>
                <a href="admin_dashboard.php" class="nav-item active">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="admin_products.php" class="nav-item">
                    <i class="fas fa-box-open"></i>
                    <span>Produk</span>
                </a>
                <a href="admin_orders.php" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pesanan</span>
                </a>
                <a href="admin_users.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Pengguna</span>
                </a>
                
                <div class="nav-title">Laporan</div>
                <a href="admin_reports.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Penjualan</span>
                </a>
                <a href="admin_analytics.php" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analitik</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="page-title">
                    <h1>Dashboard</h1>
                </div>
                <div class="user-actions">
                    <button class="btn btn-notification">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="btn btn-logout" onclick="window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stats-card sales">
                    <div class="card-header">
                        <div class="card-title">Penjualan Hari Ini</div>
                        <div class="card-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                    <div class="card-value">Rp <?php echo number_format($todaySales, 0, ',', '.'); ?></div>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up"></i> 12% dari kemarin
                    </div>
                </div>
                
                <div class="stats-card revenue">
                    <div class="card-header">
                        <div class="card-title">Total Penjualan Bulan Ini</div>
                        <div class="card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="card-value">Rp <?php echo number_format($monthlySales, 0, ',', '.'); ?></div>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up"></i> 8% dari bulan lalu
                    </div>
                </div>
                
                <div class="stats-card orders">
                    <div class="card-header">
                        <div class="card-title">Total Pesanan Hari Ini</div>
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="card-value">48</div>
                    <div class="card-change negative">
                        <i class="fas fa-arrow-down"></i> 3% dari kemarin
                    </div>
                </div>
                
                <div class="stats-card customers">
                    <div class="card-header">
                        <div class="card-title">Pengguna Baru</div>
                        <div class="card-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <div class="card-value">6</div>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up"></i> 20% dari kemarin
                    </div>
                </div>
            </div>
            
            <!-- Charts & Popular Items -->
            <div class="grid-container">
                <div class="chart-container">
                    <h3 class="section-title">Penjualan Mingguan</h3>
                    <div class="chart-placeholder">
                        [Grafik Penjualan Mingguan]
                        <script>
                            // JavaScript untuk menampilkan chart akan ditambahkan di sini
                            // Contoh menggunakan Chart.js
                        </script>
                    </div>
                </div>
                
                <div class="popular-items">
                    <h3 class="section-title">Produk Terpopuler</h3>
                    <ul class="item-list">
                        <?php foreach ($popularItems as $item): ?>
                            <li class="item">
                                <span class="item-name"><?php echo $item['name']; ?></span>
                                <span class="item-count"><?php echo $item['sold']; ?> terjual</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Recent Orders Table -->
            <div class="chart-container">
                <h3 class="section-title">Pesanan Terbaru</h3>
                <div class="chart-placeholder">
                    [Tabel Pesanan Terbaru]
                </div>
            </div>
        </div>
    </div>
</body>
</html>
