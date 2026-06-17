-- ============================================================
-- Payment Processing Pipeline (v2.0)
-- Run after main schema
-- ============================================================

-- Add payment_status to orders
ALTER TABLE orders
  ADD COLUMN payment_status VARCHAR(20) DEFAULT 'unpaid' AFTER status,
  ADD COLUMN invoice_no VARCHAR(50) DEFAULT NULL AFTER order_number,
  ADD INDEX idx_payment_status (payment_status);

-- ============================================================
-- Tabel: order_histories
-- Catat histori perubahan status pesanan
-- ============================================================
CREATE TABLE IF NOT EXISTS order_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Tabel: stock_movements
-- Catat mutasi stok produk
-- ============================================================
CREATE TABLE IF NOT EXISTS stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    qty INT NOT NULL,
    type ENUM('IN','OUT') NOT NULL,
    reference_no VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_id (product_id),
    INDEX idx_type (type),
    INDEX idx_reference (reference_no),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Tabel: notifications
-- Notifikasi untuk user/admin
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    type VARCHAR(30) NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Tabel: order_fulfillments
-- Proses picking/packing barang
-- ============================================================
CREATE TABLE IF NOT EXISTS order_fulfillments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    status ENUM('waiting_pick','picking','picked','packed','ready','done') NOT NULL DEFAULT 'waiting_pick',
    notes TEXT DEFAULT NULL,
    picked_by BIGINT UNSIGNED DEFAULT NULL,
    picked_at DATETIME DEFAULT NULL,
    packed_by BIGINT UNSIGNED DEFAULT NULL,
    packed_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Tabel: audit_logs
-- Audit trail untuk semua aktivitas penting
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED DEFAULT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;
