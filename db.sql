CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    kategori VARCHAR(50),
    nama VARCHAR(100),
    image VARCHAR(255),
    keterangan TEXT,
    stok INT,
    harga INT
);

INSERT INTO produk (kategori, nama, image, keterangan, stok, harga) VALUES
('Makanan', 'Nasi Goreng', '/images/nasi_goreng.jpg', 'Nasi yang digoreng dengan bumbu khas.', 50, 20000),
('Makanan', 'Sate Ayam', '/images/sate_ayam.jpg', 'Daging ayam yang ditusuk dan dibakar.', 30, 25000),
('Makanan', 'Rendang', '/images/rendang.jpg', 'Daging sapi yang dimasak dengan rempah.', 20, 30000),
('Makanan', 'Gado-Gado', '/images/gado_gado.jpg', 'Salad sayuran dengan bumbu kacang.', 40, 15000),
('Makanan', 'Bakso', '/images/bakso.jpg', 'Bola daging yang disajikan dengan kuah.', 60, 20000),
('Minuman', 'Teh Manis', '/images/teh_manis.jpg', 'Teh yang disajikan dengan gula.', 100, 5000),
('Minuman', 'Kopi', '/images/kopi.jpg', 'Kopi hitam yang diseduh panas.', 80, 10000),
('Minuman', 'Es Teh', '/images/es_teh.jpg', 'Teh dingin yang menyegarkan.', 90, 7000),
('Minuman', 'Jus Jeruk', '/images/jus_jeruk.jpg', 'Jus segar dari jeruk.', 70, 12000),
('Minuman', 'Soda', '/images/soda.jpg', 'Minuman berkarbonasi yang menyegarkan.', 50, 8000);


CREATE TRIGGER check_duplicate_nama
BEFORE INSERT ON produk
FOR EACH ROW
BEGIN
    DECLARE duplicate_count INT;

    -- Check for existing product with the same name
    SELECT COUNT(*) INTO duplicate_count
    FROM produk
    WHERE nama = NEW.nama;

    -- If a duplicate is found, signal an error
    IF duplicate_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk sudah ada';
    END IF;
END;

//

DELIMITER ;

CREATE TABLE orders (
    id_order INT AUTO_INCREMENT PRIMARY KEY,
    nama_pembeli VARCHAR(100) NOT NULL,
    meja INT NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id_order_item INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_order) REFERENCES orders(id_order) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);

--inputan dari order.php ke orders dan order_items
--nanti di order.php akan memunculkan produk apa saja yang di inputkan dari tabel order_items
--nanti dari tabel orders akan masuk ke tabel history yang nantinya akan di display di history.php

--jangan dlu di INSERT
-- SELECT 
--     o.id_order, 
--     o.nama_pembeli, 
--     o.meja, 
--     o.total_harga, 
--     DATE(o.created_at) AS tanggal,
--     GROUP_CONCAT(CONCAT(p.nama, ' (', oi.jumlah, 'x Rp', oi.harga_satuan, ')') SEPARATOR ', ') AS items
-- FROM orders o
-- JOIN order_items oi ON o.id_order = oi.id_order
-- JOIN produk p ON oi.id_produk = p.id_produk
-- GROUP BY o.id_order
-- ORDER BY o.created_at DESC;

CREATE VIEW view_menu AS SELECT id_produk, kategori, nama, image, keterangan, harga FROM produk ORDER BY kategori, nama;

    CREATE TABLE `admin` (
    `id_admin` int(5) NOT NULL,
    `nama` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    --
    -- Dumping data for table `admin`
    --

    INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`) VALUES
    (1, 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3'),
    (2, 'elen', 'elen', '508c71d57a2c2dd1ed8c3ede5b3012d5'),
    (3, 'raymond', 'raymond', 'f2a415aa78c7621831da5995e1447242');

CREATE VIEW vw_order_summary AS
SELECT 
    o.id_order,
    o.nama_pembeli,
    o.meja,
    SUM(oi.jumlah) AS total_produk,
    SUM(oi.jumlah * oi.harga_satuan) AS total_harga,
    o.created_at
FROM 
    orders o
JOIN 
    order_items oi ON o.id_order = oi.id_order
GROUP BY 
    o.id_order, o.nama_pembeli, o.meja, o.created_at
ORDER BY 
    o.created_at DESC, o.nama_pembeli ASC;

DELIMITER //

CREATE PROCEDURE GetOrderSummary()
BEGIN
    SELECT * FROM vw_order_summary;
END //

DELIMITER ;

INSERT INTO `produk` (`id_produk`, `kategori`, `nama`, `image`, `keterangan`, `stok`, `harga`) VALUES
(1, 'makanan', 'Nasi Goreng Spesial', 'images/nasi_goreng_spesial.jpg', 'Nasi goreng dengan bumbu rempah pilihan dan telur mata sapi.', 50, 25000),
(2, 'makanan', 'Mie Ayam Bakso', 'images/mie_ayam_bakso.jpg', 'Mie ayam dengan toping bakso sapi dan pangsit goreng.', 45, 22000),
(3, 'makanan', 'Sate Ayam Madura', 'images/sate_ayam_madura.jpg', 'Sate ayam dengan bumbu kacang khas Madura.', 40, 30000),
(4, 'makanan', 'Gado-Gado Siram', 'images/gado-gado_siram.jpg', 'Sayuran segar dengan lontong dan bumbu kacang siram.', 34, 20000),
(5, 'makanan', 'Soto Betawi', 'images/soto_betawi.webp', 'Soto santan khas Betawi dengan daging sapi.', 30, 28000),
(6, 'minuman', 'Es Teh Manis', 'images/es_teh_manis.webp', 'Minuman teh segar dengan gula asli.', 60, 8000),
(7, 'minuman', 'Es Jeruk Peras', 'images/es_jeruk_peras.jpeg', 'Minuman jeruk peras asli tanpa pengawet.', 55, 10000),
(8, 'minuman', 'Kopi Susu Dingin', 'images/kopi_susu_dingin.webp', 'Kopi robusta dengan susu dan es.', 50, 15000),
(9, 'minuman', 'Jus Alpukat', 'images/jus_alpukat.jpg', 'Jus alpukat segar dengan susu cokelat.', 45, 18000),
(10, 'minuman', 'Wedang Jahe', 'images/wedang_jahe.jpeg', 'Minuman hangat jahe dengan gula merah.', 40, 12000),
(51, 'Minuman', 'Vanila Spesial', 'images/vanila_spesial.jpg', 'Jus Vanila dengan ekstrak Kopi', 4, 25000);



CREATE TRIGGER trg_check_stock_before_order_item_insert
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;

    SELECT stok INTO current_stock
    FROM produk
    WHERE id_produk = NEW.id_produk;

    IF NEW.jumlah > current_stock THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok produk tidak cukup untuk pesanan ini.';
    END IF;

    -
END$$

DELIMITER ; 