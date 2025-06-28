<?php
// menu_page.php - Halaman untuk menampilkan daftar menu dari tabel 'produk' atau VIEW 'daftar_produk'
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
    // PENTING: Perintah 'CREATE VIEW' digunakan untuk membuat VIEW di database (SATU KALI SAJA),
    // BUKAN untuk mengambil data di setiap request halaman.
    // Untuk mengambil data, gunakan perintah 'SELECT'.
    // Pastikan VIEW 'daftar_produk' sudah dibuat di database Anda dengan perintah SQL di atas.

    // Mengambil semua kolom yang diperlukan dari VIEW 'daftar_produk'
    // Kolom 'stok' DIHILANGKAN dari SELECT query ini sesuai permintaan Anda
    $sql_get_menu = "SELECT kategori, nama, image, keterangan, harga FROM view_menu ORDER BY kategori, nama;"; 
    
    // Opsi: Jika Anda TIDAK ingin menggunakan VIEW dan ingin langsung dari tabel 'produk',
    // gunakan baris di bawah ini sebagai ganti $sql_get_menu di atas:
    // $sql_get_menu = "SELECT id_produk, kategori, nama, image, keterangan, stok, harga FROM produk ORDER BY kategori, nama;"; 

    $result_get_menu = $conn->query($sql_get_menu);

    if ($result_get_menu && $result_get_menu->num_rows > 0) {
        while ($row = $result_get_menu->fetch_assoc()) {
            $menu_items[] = $row;
        }
    } else if (!$result_get_menu) {
        // Query gagal dieksekusi (contoh: VIEW tidak ada, sintaks salah pada SELECT)
        error_log("Failed to fetch menu items on menu_page: " . $conn->error);
        $error_message = "Terjadi kesalahan saat mengambil daftar menu dari database. Mohon coba lagi nanti. (SQL Error: " . $conn->error . ")";
    } else {
        // Query berhasil dieksekusi, tetapi tidak ada baris data (VIEW kosong)
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
    <title>Daftar Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Menggunakan CSS global dari direktori css/ -->
    <link rel="stylesheet" href="../css/css_order.css"> 
</head>
<body>
    <header class="navbar">
        <div class="navbar-brand">
            <span class="logo-placeholder">E/R</span> <!-- Placeholder logo -->
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
                                <!-- Menggunakan 'image' dari hasil query VIEW/produk -->
                                <img src="<?php echo htmlspecialchars($menu_item['image']); ?>" alt="Gambar <?php echo htmlspecialchars($menu_item['nama']); ?>">
                                <div class="card-content">
                                    <div>
                                        <!-- Menggunakan 'nama' dari hasil query VIEW/produk -->
                                        <h4><?php echo htmlspecialchars($menu_item['nama']); ?></h4>
                                        <!-- Menggunakan 'keterangan' dari hasil query VIEW/produk -->
                                        <p class="description"><?php echo htmlspecialchars($menu_item['keterangan']); ?></p>
                                        <!-- Menggunakan 'harga' dari hasil query VIEW/produk -->
                                        <p class="price">Rp <?php echo number_format($menu_item['harga'], 0, ',', '.'); ?></p>
                                        <!-- Menghilangkan elemen stok-info sesuai permintaan sebelumnya -->
                                    </div>
                                    <!-- Tombol Pesan baru per item menu -->
     
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
