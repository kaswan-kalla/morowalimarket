-- Marketplace Multi Vendor Database Schema
-- MySQL / MariaDB

CREATE DATABASE IF NOT EXISTS marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace;

-- ============================================================
-- TABEL: users
-- ============================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    role ENUM('admin','seller','buyer') NOT NULL DEFAULT 'buyer',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: stores
-- ============================================================
CREATE TABLE stores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT NULL,
    banner VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    province VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(10) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    is_open TINYINT(1) NOT NULL DEFAULT 1,
    rating DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    total_sales INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_slug (slug),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: categories
-- ============================================================
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(110) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT NULL,
    parent_id BIGINT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_slug (slug),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: products
-- ============================================================
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED DEFAULT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    sku VARCHAR(50) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_price DECIMAL(12,2) DEFAULT NULL,
    weight DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    sold INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    main_image VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_slug (slug),
    INDEX idx_store_id (store_id),
    INDEX idx_category_id (category_id),
    INDEX idx_price (price),
    INDEX idx_sold (sold),
    INDEX idx_created (created_at),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: product_images
-- ============================================================
CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_main TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_product_id (product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: carts
-- ============================================================
CREATE TABLE carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: cart_items
-- ============================================================
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    qty INT UNSIGNED NOT NULL DEFAULT 1,
    notes VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cart_id (cart_id),
    INDEX idx_product_id (product_id),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: addresses
-- ============================================================
CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    label VARCHAR(100) NOT NULL COMMENT 'Label: Rumah, Kantor, dll',
    recipient_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: orders
-- ============================================================
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    store_id BIGINT UNSIGNED DEFAULT NULL,
    address_id BIGINT UNSIGNED DEFAULT NULL,
    recipient_name VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    shipping_address TEXT DEFAULT NULL,
    courier VARCHAR(50) DEFAULT NULL,
    tracking_number VARCHAR(100) DEFAULT NULL,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    shipping_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    voucher_code VARCHAR(50) DEFAULT NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('pending','awaiting_payment','processing','shipped','completed','cancelled') NOT NULL DEFAULT 'pending',
    paid_at DATETIME DEFAULT NULL,
    shipped_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    cancel_reason VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_store_id (store_id),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: order_items
-- ============================================================
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    store_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_slug VARCHAR(220) DEFAULT NULL,
    product_image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(12,2) NOT NULL,
    qty INT UNSIGNED NOT NULL DEFAULT 1,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    INDEX idx_store_id (store_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: payments
-- ============================================================
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('transfer','qris') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    proof_image VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    verified_by BIGINT UNSIGNED DEFAULT NULL,
    verified_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: wishlists
-- ============================================================
CREATE TABLE wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: reviews
-- ============================================================
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX idx_product_id (product_id),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABEL: vouchers
-- ============================================================
CREATE TABLE vouchers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percentage','fixed') NOT NULL DEFAULT 'fixed',
    discount_value DECIMAL(12,2) NOT NULL,
    min_purchase DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    max_discount DECIMAL(12,2) DEFAULT NULL,
    max_usage INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = unlimited',
    used_count INT UNSIGNED NOT NULL DEFAULT 0,
    start_date DATE NOT NULL,
    expired_at DATE NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB;

-- ============================================================
-- DATA AWAL: Admin default
-- Email: admin@marketplace.com | Password: admin123
-- ============================================================
INSERT INTO users (name, email, password, role, is_active) VALUES
('Administrator', 'admin@marketplace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
