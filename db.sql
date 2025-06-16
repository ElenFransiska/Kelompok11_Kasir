-- Kasir Database

-- DROP TABLES untuk instalasi ulang bersih
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS order_detail;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS pelanggan;

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telepon VARCHAR(15) NOT NULL
);

-- Tabel Menu (Makanan & Minuman)
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis ENUM('Makanan', 'Minuman') NOT NULL,
    stok INT NOT NULL CHECK (stok >= 0),
    harga DECIMAL(10,2) NOT NULL,
    keterangan TEXT
);

-- INDEX untuk mempercepat pencarian berdasarkan jenis dan nama
CREATE INDEX idx_menu_jenis ON menu(jenis);
CREATE INDEX idx_menu_nama ON menu(nama);

-- View untuk menampilkan menu lengkap
CREATE OR REPLACE VIEW view_menu AS
SELECT id, nama, jenis, stok, harga, keterangan
FROM menu;

-- Tabel Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelanggan_id INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL DEFAULT 0,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- Tabel Order_Detail (detil tiap item pesanan)
CREATE TABLE order_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    jumlah INT NOT NULL CHECK(jumlah > 0),
    harga DECIMAL(10,2) NOT NULL, -- harga saat order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id)
);

-- INDEX untuk detail order berdasarkan order_id dan menu_id
CREATE INDEX idx_order_detail_order ON order_detail(order_id);
CREATE INDEX idx_order_detail_menu ON order_detail(menu_id);

-- Tabel History (menyimpan ringkasan pesanan final)
CREATE TABLE history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    pelanggan_id INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- Trigger untuk update stok setelah insert order_detail (kurangi stok)
DELIMITER //
CREATE TRIGGER trg_reduce_stock_after_insert_order_detail
AFTER INSERT ON order_detail
FOR EACH ROW
BEGIN
    UPDATE menu SET stok = stok - NEW.jumlah WHERE id = NEW.menu_id;
END;
//

-- Trigger untuk menambah stok setelah hapus order_detail (batalkan order)
CREATE TRIGGER trg_add_stock_after_delete_order_detail
AFTER DELETE ON order_detail
FOR EACH ROW
BEGIN
    UPDATE menu SET stok = stok + OLD.jumlah WHERE id = OLD.menu_id;
END;
//

-- Trigger isi tabel history saat insert order final
CREATE TRIGGER trg_insert_history_after_insert_orders
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO history(order_id, pelanggan_id, total_harga, tanggal)
    VALUES(NEW.id, NEW.pelanggan_id, NEW.total_harga, NEW.tanggal);
END;
//
DELIMITER ;

-- Procedure untuk tambah menu baru
DELIMITER //
CREATE PROCEDURE tambah_menu(
    IN p_nama VARCHAR(100),
    IN p_jenis ENUM('Makanan', 'Minuman'),
    IN p_stok INT,
    IN p_harga DECIMAL(10,2),
    IN p_keterangan TEXT
)
BEGIN
    INSERT INTO menu(nama, jenis, stok, harga, keterangan)
    VALUES(p_nama, p_jenis, p_stok, p_harga, p_keterangan);
END;
//

-- Procedure untuk edit menu
CREATE PROCEDURE edit_menu(
    IN p_id INT,
    IN p_nama VARCHAR(100),
    IN p_jenis ENUM('Makanan', 'Minuman'),
    IN p_stok INT,
    IN p_harga DECIMAL(10,2),
    IN p_keterangan TEXT
)
BEGIN
    UPDATE menu
    SET nama = p_nama, jenis = p_jenis, stok = p_stok, harga = p_harga, keterangan = p_keterangan
    WHERE id = p_id;
END;
//

-- Procedure hapus menu berdasarkan id
CREATE PROCEDURE hapus_menu(
    IN p_id INT
)
BEGIN
    DELETE FROM menu WHERE id = p_id;
END;
//

-- Procedure untuk menambah order baru
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

    -- Insert ke orders
    INSERT INTO orders (pelanggan_id, total_harga) VALUES (p_pelanggan_id, 0);
    SET v_order_id = LAST_INSERT_ID();

    -- Proses makanan
    IF p_id_makanan_list != '' THEN
        SET total_makanan = (LENGTH(p_id_makanan_list) - LENGTH(REPLACE(p_id_makanan_list, ',', '')) + 1);
        WHILE i <= total_makanan DO
            DECLARE v_id_m INT;
            DECLARE v_jumlah_m INT;
            DECLARE v_harga_m DECIMAL(10,2);
            DECLARE v_total_harga_m DECIMAL(10,2);

            SET v_id_m = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_id_makanan_list, ',', i), ',', -1) AS UNSIGNED);
            SET v_jumlah_m = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_jumlah_makanan_list, ',', i), ',', -1) AS UNSIGNED);
            SELECT harga INTO v_harga_m FROM menu WHERE id = v_id_m;

            -- Hitung total harga makanan
            SET v_total_harga_m = v_harga_m * v_jumlah_m;

            -- Insert ke order_detail
            INSERT INTO order_detail(order_id, menu_id, jumlah, harga) 
            VALUES(v_order_id, v_id_m, v_jumlah_m, v_harga_m);

            -- Update total harga di orders
            UPDATE orders SET total_harga = total_harga + v_total_harga_m WHERE id = v_order_id;

            SET i = i + 1;
        END WHILE;
    END IF;

    -- Reset i untuk minuman
    SET i = 1;

    -- Proses minuman
    IF p_id_minuman_list != '' THEN
        SET total_minuman = (LENGTH(p_id_minuman_list) - LENGTH(REPLACE(p_id_minuman_list, ',', '')) + 1);
        WHILE i <= total_minuman DO
            DECLARE v_id_min INT;
            DECLARE v_jumlah_min INT;
            DECLARE v_harga_min DECIMAL(10,2);
            DECLARE v_total_harga_min DECIMAL(10,2);

            SET v_id_min = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_id_minuman_list, ',', i), ',', -1) AS UNSIGNED);
            SET v_jumlah_min = CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(p_jumlah_minuman_list, ',', i), ',', -1) AS UNSIGNED);
            SELECT harga INTO v_harga_min FROM menu WHERE id = v_id_min;

            -- Hitung total harga minuman
            SET v_total_harga_min = v_harga_min * v_jumlah_min;

            -- Insert ke order_detail
            INSERT INTO order_detail(order_id, menu_id, jumlah, harga) 
            VALUES(v_order_id, v_id_min, v_jumlah_min, v_harga_min);

            -- Update total harga di orders
            UPDATE orders SET total_harga = total_harga + v_total_harga_min WHERE id = v_order_id;

            SET i = i + 1;
        END WHILE;
    END IF;
END;
//

DELIMITER ;