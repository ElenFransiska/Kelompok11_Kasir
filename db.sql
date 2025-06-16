-- Database Kasir with Customers, Orders, History, and Reset History Procedure

DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS order_detail;
DROP TABLE IF EXISTS order_master;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS makanan;
DROP TABLE IF EXISTS minuman;

CREATE TABLE pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telepon VARCHAR(15) NOT NULL
);

CREATE TABLE makanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL
);

CREATE TABLE minuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT NOT NULL
);

CREATE TABLE order_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

CREATE TABLE order_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_master_id INT NOT NULL,
    id_makanan INT NULL,
    id_minuman INT NULL,
    jumlah INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_master_id) REFERENCES order_master(id) ON DELETE CASCADE,
    FOREIGN KEY (id_makanan) REFERENCES makanan(id),
    FOREIGN KEY (id_minuman) REFERENCES minuman(id)
);

CREATE TABLE history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_master_id INT NOT NULL,
    pelanggan_id INT NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_master_id) REFERENCES order_master(id) ON DELETE CASCADE,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

CREATE OR REPLACE VIEW view_menu AS
SELECT id, nama, harga, stok, 'Makanan' AS jenis FROM makanan
UNION ALL
SELECT id, nama, harga, stok, 'Minuman' AS jenis FROM minuman;

DELIMITER //

CREATE TRIGGER trg_reduce_stock_after_insert_order_detail
AFTER INSERT ON order_detail
FOR EACH ROW
BEGIN
    IF NEW.id_makanan IS NOT NULL THEN
        UPDATE makanan SET stok = stok - NEW.jumlah WHERE id = NEW.id_makanan;
    END IF;
    IF NEW.id_minuman IS NOT NULL THEN
        UPDATE minuman SET stok = stok - NEW.jumlah WHERE id = NEW.id_minuman;
    END IF;
END;
//

CREATE TRIGGER trg_add_stock_after_delete_order_detail
AFTER DELETE ON order_detail
FOR EACH ROW
BEGIN
    IF OLD.id_makanan IS NOT NULL THEN
        UPDATE makanan SET stok = stok + OLD.jumlah WHERE id = OLD.id_makanan;
    END IF;
    IF OLD.id_minuman IS NOT NULL THEN
        UPDATE minuman SET stok = stok + OLD.jumlah WHERE id = OLD.id_minuman;
    END IF;
END;
//

CREATE TRIGGER trg_insert_history_after_insert_order_master
AFTER INSERT ON order_master
FOR EACH ROW
BEGIN
    INSERT INTO history (order_master_id, pelanggan_id, tanggal) VALUES (NEW.id, NEW.pelanggan_id, NOW());
END;
//

CREATE PROCEDURE tambah_order_baru(
    IN p_pelanggan_id INT,
    IN p_id_makanan_list TEXT,
    IN p_jumlah_makanan_list TEXT,
    IN p_id_minuman_list TEXT,
    IN p_jumlah_minuman_list TEXT
)
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE total_makanan INT;
    DECLARE total_minuman INT;
    DECLARE v_order_id INT;

    INSERT INTO order_master (pelanggan_id, status) VALUES (p_pelanggan_id, 'pending');
    SET v_order_id = LAST_INSERT_ID();

    IF p_id_makanan_list != '' THEN
        SET total_makanan = (LENGTH(p_id_makanan_list) - LENGTH(REPLACE(p_id_makanan_list, ',', '')) + 1);
        WHILE i <= total_makanan DO
            DECLARE v_id_m INT;
            DECLARE v_jumlah_m INT;
            DECLARE v_harga_m DECIMAL(10,2);

            SET v_id_m = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_id_makanan_list, ',', i), ',', -1) AS UNSIGNED);
            SET v_jumlah_m = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_jumlah_makanan_list, ',', i), ',', -1) AS UNSIGNED);
            SELECT harga INTO v_harga_m FROM makanan WHERE id = v_id_m;

            INSERT INTO order_detail(order_master_id, id_makanan, jumlah, harga)
            VALUES(v_order_id, v_id_m, v_jumlah_m, v_harga_m);

            SET i = i + 1;
        END WHILE;
    END IF;

    SET i = 1;
    IF p_id_minuman_list != '' THEN
        SET total_minuman = (LENGTH(p_id_minuman_list) - LENGTH(REPLACE(p_id_minuman_list, ',', '')) + 1);
        WHILE i <= total_minuman DO
            DECLARE v_id_min INT;
            DECLARE v_jumlah_min INT;
            DECLARE v_harga_min DECIMAL(10,2);

            SET v_id_min = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_id_minuman_list, ',', i), ',', -1) AS UNSIGNED);
            SET v_jumlah_min = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_jumlah_minuman_list, ',', i), ',', -1) AS UNSIGNED);
            SELECT harga INTO v_harga_min FROM minuman WHERE id = v_id_min;

            INSERT INTO order_detail(order_master_id, id_minuman, jumlah, harga)
            VALUES(v_order_id, v_id_min, v_jumlah_min, v_harga_min);

            SET i = i + 1;
        END WHILE;
    END IF;
END;
//

CREATE PROCEDURE hapus_order(IN p_order_master_id INT)
BEGIN
    DELETE FROM order_master WHERE id = p_order_master_id;
END;
//

CREATE PROCEDURE reset_history()
BEGIN
    DELETE FROM history;
    DELETE FROM order_master;
END;
//

DELIMITER ;

-- Sample data pelanggan
INSERT INTO pelanggan (nama, email, telepon) VALUES
('Budi Santoso', 'budi@example.com', '081234567890'),
('Ani Wijaya', 'ani@example.com', '089876543210');

-- Sample data makanan
INSERT INTO makanan (nama, harga, stok) VALUES 
('Nasi Goreng', 15000, 50),
('Mie Ayam', 12000, 40),
('Ayam Bakar', 25000, 25);

-- Sample data minuman
INSERT INTO minuman (nama, harga, stok) VALUES
('Es Teh', 5000, 100),
('Jus Jeruk', 10000, 30),
('Air Mineral', 3000, 200);
