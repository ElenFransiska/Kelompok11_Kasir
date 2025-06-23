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