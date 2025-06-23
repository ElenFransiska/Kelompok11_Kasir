-- Membuat tabel produk
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

DELIMITER //

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
SELECT 
    o.id_order, 
    o.nama_pembeli, 
    o.meja, 
    o.total_harga, 
    DATE(o.created_at) AS tanggal,
    GROUP_CONCAT(CONCAT(p.nama, ' (', oi.jumlah, 'x Rp', oi.harga_satuan, ')') SEPARATOR ', ') AS items
FROM orders o
JOIN order_items oi ON o.id_order = oi.id_order
JOIN produk p ON oi.id_produk = p.id_produk
GROUP BY o.id_order
ORDER BY o.created_at DESC;