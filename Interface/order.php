<?php
session_start();
require_once '../db_connection.php'; 

$menu_items = [];
$error_message = '';
$info_message = '';

if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    $error_message = "Tidak dapat terhubung ke database. Mohon cek konfigurasi koneksi database Anda. ";
    if (isset($conn) && $conn->connect_error) {
    }
    $menu_items = []; 
} else {
    $sql_get_menu = "SELECT kategori, nama, image, keterangan, harga FROM view_menu ORDER BY kategori, nama;"; 

    $result_get_menu = $conn->query($sql_get_menu);

    if ($result_get_menu && $result_get_menu->num_rows > 0) {
        while ($row = $result_get_menu->fetch_assoc()) {
            $menu_items[] = $row;
        }
    } else if (!$result_get_menu) {
        error_log("Failed to fetch menu items on menu_page: " . $conn->error);
        $error_message = "Terjadi kesalahan saat mengambil daftar menu dari database. Mohon coba lagi nanti. (SQL Error: " . $conn->error . ")";
    } else {
        $info_message = "Saat ini belum ada item menu yang tersedia.";
    }

    $conn->close(); 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/css_order.css"> 
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            <span class="logo-placeholder">E/R</span> 
            System Kasir
        </div>
        <nav class="navbar-nav">
            <a href="home.php" class="nav-link">Home</a>
            <a href="menu_page.php" class="nav-link active">Menu</a> 
            <a href="pesan.php" class="nav-link">Pesan Sekarang</a>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="menu-page-title">Daftar Menu Kami</h1>
        <p class="menu-page-description">Jelajahi berbagai pilihan makanan dan minuman lezat yang kami tawarkan. Setiap hidangan dibuat dengan bahan-bahan segar pilihan untuk memastikan cita rasa terbaik.</p>

        <?php if (!empty($error_message)): ?>
            <div class="message-box error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (!empty($info_message)): ?>
            <div class="message-box success">
                <i class="fas fa-info-circle"></i>
                <?php echo htmlspecialchars($info_message); ?>
            </div>
        <?php endif; ?>

        <?php
        $categorized_menu = [];
        if (!empty($menu_items)) {
            foreach ($menu_items as $item) {
                $categorized_menu[$item['kategori']][] = $item;
            }
        }

        if (!empty($categorized_menu)) {
            foreach ($categorized_menu as $category => $items_in_category):
            ?>
                <section class="menu-category-section">
                    <h3><?php echo htmlspecialchars($category); ?></h3>
                    <div class="menu-grid">
                        <?php foreach ($items_in_category as $menu_item): ?>
                            <div class="menu-item-card">
                         <img src="../<?= $menu_item['image'] ?>" alt="<?=$menu_item['nama']?>">
                                <div class="card-content">
                                    <div>
                                        <h4><?php echo htmlspecialchars($menu_item['nama']); ?></h4>
                                        <p class="description"><?php echo htmlspecialchars($menu_item['keterangan']); ?></p>
                                        <p class="price">Rp <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach;
        } 
        ?>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> [Nama Cafe/Restoran Anda]. Semua Hak Dilindungi.</p>
        <p>Powered by Program Kasir.</p>
    </footer>
</body>
</html>
