<?php
// menu_page.php - Halaman untuk menampilkan daftar menu
session_start();
// PERBAIKAN: Mengganti 'kasir_db' dengan 'db_connection.php'
require_once '../db_connection.php'; 

$menu_items = [];
// Ambil semua menu dari database untuk ditampilkan
$sql_get_menu = "SELECT id, nama_menu, deskripsi, harga, stok, kategori, gambar_url FROM menu ORDER BY kategori, nama_menu";
$result_get_menu = $conn->query($sql_get_menu);

if ($result_get_menu && $result_get_menu->num_rows > 0) {
    while ($row = $result_get_menu->fetch_assoc()) {
        $menu_items[] = $row;
    }
} else if (!$result_get_menu) {
    error_log("Failed to fetch menu items on menu_page: " . $conn->error);
    // Tampilkan pesan error jika gagal mengambil menu dari database
    $error_message = "Terjadi kesalahan saat mengambil daftar menu dari database. Mohon coba lagi nanti.";
} else {
    // Jika tidak ada menu di database
    $info_message = "Saat ini belum ada item menu yang tersedia.";
}

$conn->close(); // Tutup koneksi setelah semua operasi selesai pada satu request
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu - [Nama Cafe/Restoran Anda]</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/css_order.css"> <!-- Menggunakan CSS global -->
    <style>
        /* Gaya spesifik untuk halaman menu_page.php */
        .menu-page-title {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-dark);
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .menu-page-description {
            text-align: center;
            font-size: 1.05rem;
            color: var(--text-medium);
            margin-bottom: 3.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        .menu-category-section {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .menu-category-section:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .menu-category-section h3 {
            font-size: 2.2rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            padding-bottom: 15px;
            font-weight: 700;
        }

        .menu-category-section h3::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background-color: var(--primary-dark);
            border-radius: 5px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
            justify-content: center;
            align-items: stretch;
        }

        .menu-item-card {
            background-color: white;
            border-radius: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--light);
        }

        .menu-item-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .menu-item-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 18px 18px 0 0;
            transition: transform 0.4s ease;
        }

        .menu-item-card:hover img {
            transform: scale(1.1);
        }

        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-content h4 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 0.4rem;
            font-weight: 600;
        }

        .card-content p.description {
            font-size: 0.9rem;
            color: var(--text-medium);
            margin-bottom: 12px;
            flex-grow: 1;
            min-height: 40px; /* Consistent height */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-content p.price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 15px;
            letter-spacing: -0.2px;
        }

        .stok-info {
            font-size: 0.85rem;
            color: var(--text-medium);
            margin-top: -8px;
            margin-bottom: 0; /* No margin bottom as no buttons below */
            font-weight: 500;
        }

        .stok-info.low-stock {
            color: var(--warning);
            font-weight: 700;
        }
        .stok-info.out-of-stock {
            color: var(--danger);
            font-weight: 700;
            text-transform: uppercase;
        }

        .message-box {
            padding: 18px 30px;
            margin-bottom: 3.5rem;
            border-radius: 15px;
            font-size: 1.15rem;
            text-align: center;
            font-weight: 600;
            border: 1px solid transparent;
            animation: fadeInScale 0.6s forwards;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: translateY(-30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Responsive Adjustments (Copy from pesan_makanan.php for consistency) */
        @media (max-width: 768px) {
            .navbar { padding: 1rem 1rem; }
            .navbar-brand { font-size: 1.5rem; }
            .navbar-nav { gap: 1rem; }
            .nav-link { padding: 0.4rem 0.6rem; font-size: 0.9rem; }
            .main-content { padding: 2.5rem 1.5rem; }
            .menu-page-title { font-size: 2.5rem; }
            .menu-page-description { font-size: 1rem; margin-bottom: 2.5rem; }
            .menu-category-section { padding: 2rem 1.5rem; margin-bottom: 2.5rem; }
            .menu-category-section h3 { font-size: 1.8rem; margin-bottom: 2rem; }
            .menu-category-section h3::after { width: 80px; height: 4px; }
            .menu-grid { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
            .menu-item-card img { height: 160px; }
            .card-content { padding: 15px; }
            .card-content h4 { font-size: 1.3rem; }
            .card-content p.description { font-size: 0.85rem; min-height: 35px; }
            .card-content p.price { font-size: 1.3rem; }
            .stok-info { font-size: 0.8rem; }
            .message-box { font-size: 1rem; padding: 12px 20px; margin-bottom: 2.5rem; }
            .footer { padding: 1.2rem; font-size: 0.85rem; }
        }

        @media (max-width: 500px) {
            .navbar { flex-direction: column; align-items: flex-start; gap: 0.8rem; }
            .navbar-nav { width: 100%; justify-content: space-around; gap: 0.5rem; margin-top: 10px; }
            .nav-link { flex-grow: 1; text-align: center; padding: 0.6rem 0.5rem; font-size: 0.85rem; }
            .main-content { padding: 1.5rem 1rem; }
            .menu-page-title { font-size: 2rem; }
            .menu-page-description { font-size: 0.9rem; margin-bottom: 2rem; }
            .menu-category-section h3 { font-size: 1.6rem; margin-bottom: 1.5rem; }
            .menu-grid { grid-template-columns: 1fr; gap: 1.2rem; }
            .menu-item-card img { height: 140px; }
            .card-content h4 { font-size: 1.2rem; }
            .card-content p.description { font-size: 0.8rem; }
            .card-content p.price { font-size: 1.1rem; }
            .footer { padding: 1rem; }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            [Nama Cafe/Restoran Anda]
        </div>
        <nav class="navbar-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="pesan_makanan.php" class="nav-link">Pesan Sekarang</a>
            <a href="menu_page.php" class="nav-link active">Menu</a> <!-- Link aktif untuk halaman ini -->
            <a href="contact.php" class="nav-link">Hubungi Kami</a>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="menu-page-title">Daftar Menu Kami</h1>
        <p class="menu-page-description">Jelajahi berbagai pilihan makanan dan minuman lezat yang kami tawarkan. Setiap hidangan dibuat dengan bahan-bahan segar pilihan untuk memastikan cita rasa terbaik.</p>

        <?php if (isset($error_message)): ?>
            <div class="message-box error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (isset($info_message)): ?>
            <div class="message-box success">
                <?php echo htmlspecialchars($info_message); ?>
            </div>
        <?php endif; ?>

        <?php
        // Kelompokkan menu berdasarkan kategori
        $categorized_menu = [];
        foreach ($menu_items as $item) {
            $categorized_menu[$item['kategori']][] = $item;
        }

        // Tampilkan kategori dan item menu
        if (!empty($categorized_menu)) {
            foreach ($categorized_menu as $category => $items_in_category):
            ?>
                <section class="menu-category-section">
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                    <div class="menu-grid">
                        <?php foreach ($items_in_category as $menu_item): ?>
                            <div class="menu-item-card">
                                <img src="<?php echo htmlspecialchars($menu_item['gambar_url']); ?>" alt="); ?>]">
                                <div class="card-content">
                                    <div>
                                        <h4><?php echo htmlspecialchars($menu_item['nama_menu']); ?></h4>
                                        <p class="description"><?php echo htmlspecialchars($menu_item['deskripsi']); ?></p>
                                        <p class="price">Rp <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                                        <p class="stok-info <?php 
                                            if ($menu_item['stok'] <= 5 && $menu_item['stok'] > 0) { echo 'low-stock'; }
                                            else if ($menu_item['stok'] <= 0) { echo 'out-of-stock'; }
                                        ?>">
                                            Stok: <?php 
                                            if ($menu_item['stok'] <= 0) { echo 'Habis'; }
                                            else { echo htmlspecialchars($menu_item['stok']); }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach;
        } // End if (!empty($categorized_menu))
        ?>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> [Nama Cafe/Restoran Anda]. Semua Hak Dilindungi.</p>
        <p>Powered by Program Kasir.</p>
    </footer>
</body>
</html>
