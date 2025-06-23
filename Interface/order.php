<?php
// menu_page.php - Halaman untuk menampilkan daftar menu dari tabel 'produk'
session_start();
// Memastikan koneksi database berada di direktori yang benar
// Asumsi db_connection.php berada satu tingkat di atas direktori ini.
require_once '../db_connection.php'; 

// Inisialisasi $menu_items dan $error_message/info_message
$menu_items = [];
$error_message = '';
$info_message = '';

// Cek apakah $conn berhasil terdefinisi dan merupakan objek MySQLi yang valid setelah require_once
if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    // Jika koneksi gagal atau $conn tidak terdefinisi dengan benar, tampilkan pesan error
    // Ini membantu debug jika db_connection.php gagal tanpa fatal error langsung
    $error_message = "Tidak dapat terhubung ke database. Mohon cek konfigurasi koneksi database Anda. ";
    if (isset($conn) && $conn->connect_error) {
        $error_message .= "Error: " . $conn->connect_error; // Tambahkan detail error koneksi jika ada
    }
    // Pastikan array kosong agar tidak ada loop yang mencoba query
    $menu_items = []; 
} else {
    // Koneksi database berhasil, lanjutkan dengan query
    // Ambil semua menu dari database untuk ditampilkan dari tabel 'produk'
    // PERBAIKAN PENTING: Menyesuaikan nama kolom sesuai tabel 'produk' dan menambahkan 'harga'
    $sql_get_menu = "SELECT id_produk, kategori, nama, image, keterangan, stok, harga FROM produk ORDER BY kategori, nama;"; // UBAH: nama kolom dan penambahan 'harga'
    $result_get_menu = $conn->query($sql_get_menu);

    if ($result_get_menu && $result_get_menu->num_rows > 0) {
        while ($row = $result_get_menu->fetch_assoc()) {
            $menu_items[] = $row;
        }
    } else if (!$result_get_menu) {
        // Query gagal dieksekusi (contoh: tabel tidak ada, sintaks salah)
        error_log("Failed to fetch menu items on menu_page: " . $conn->error);
        $error_message = "Terjadi kesalahan saat mengambil daftar menu dari database. Mohon coba lagi nanti. (SQL Error: " . $conn->error . ")";
    } else {
        // Query berhasil dieksekusi, tetapi tidak ada baris data (tabel kosong)
        $info_message = "Saat ini belum ada item menu yang tersedia.";
    }

    $conn->close(); // Tutup koneksi setelah semua operasi selesai pada satu request
}
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
        // Kelompokkan menu berdasarkan kategori
        $categorized_menu = [];
        // Pastikan $menu_items tidak kosong sebelum di-loop
        if (!empty($menu_items)) {
            foreach ($menu_items as $item) {
                // Menggunakan 'kategori' dari tabel produk
                $categorized_menu[$item['kategori']][] = $item;
            }
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
                                <!-- PERBAIKAN: Menggunakan 'image' dari tabel produk -->
                                <img src="../<?= htmlspecialchars($menu_item['image']) ?>" alt="Gambar <?= htmlspecialchars($menu_item['nama']) ?>">
                                <div class="card-content">
                                    <div>
                                        <!-- PERBAIKAN: Menggunakan 'nama' dari tabel produk -->
                                        <h4><?php echo htmlspecialchars($menu_item['nama']); ?></h4>
                                        <!-- PERBAIKAN: Menggunakan 'keterangan' dari tabel produk -->
                                        <p class="description"><?php echo htmlspecialchars($menu_item['keterangan']); ?></p>
                                        <!-- Menggunakan 'harga' dari tabel produk -->
                                        <p class="price">Rp <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                                        <!-- Menggunakan 'stok' dari tabel produk -->
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
                                    <!-- PERBAIKAN: Menggunakan 'id_produk' dari tabel produk dan menghapus atribut `aria-disabled` serta `style` -->
                                    <a href="pesan_makanan.php?menu_id=<?php echo $menu_item['id_produk']; ?>" 
                                       class="menu-item-order-btn">
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
