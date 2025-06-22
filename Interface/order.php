<?php
// menu_page.php - Halaman untuk menampilkan daftar menu
session_start();
// Memastikan koneksi database berada di direktori yang benar
// Asumsi db_connection.php berada satu tingkat di atas direktori ini.
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
    <!-- Menggunakan CSS global dari direktori css/ -->
    <link rel="stylesheet" href="../css/css_order.css"> 
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            <span class="logo-placeholder">C/R</span> <!-- Placeholder logo -->
            [Nama Cafe/Restoran Anda]
        </div>
        <nav class="navbar-nav">
            <a href="home.php" class="nav-link">Home</a>
            <a href="menu_page.php" class="nav-link active">Menu</a> 
            <a href="pesan.php" class="nav-link">Pesan Sekarang</a>
            <a href="contact.php" class="nav-link">Hubungi Kami</a>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="menu-page-title">Daftar Menu Kami</h1>
        <p class="menu-page-description">Jelajahi berbagai pilihan makanan dan minuman lezat yang kami tawarkan. Setiap hidangan dibuat dengan bahan-bahan segar pilihan untuk memastikan cita rasa terbaik.</p>

        <?php if (isset($error_message)): ?>
            <div class="message-box error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (isset($info_message)): ?>
            <div class="message-box success">
                <i class="fas fa-info-circle"></i>
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
                                <img src="<?php echo htmlspecialchars($menu_item['gambar_url']); ?>" alt="Gambar <?php echo htmlspecialchars($menu_item['nama_menu']); ?>">
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
                                    <!-- Tombol Pesan baru per item menu -->
                                    <a href="pesan_makanan.php?menu_id=<?php echo $menu_item['id']; ?>" 
                                       class="menu-item-order-btn" 
                                       <?php echo ($menu_item['stok'] <= 0) ? 'aria-disabled="true" style="pointer-events: none; opacity: 0.6; cursor: not-allowed;"' : ''; ?>>
                                        <i class="fas fa-cart-plus"></i> Pesan
                                    </a>
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
