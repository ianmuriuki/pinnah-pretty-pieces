-- Create Database (if not exists)
CREATE DATABASE IF NOT EXISTS pinnahs_pretty_pieces CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pinnahs_pretty_pieces;

-- Users Table: For registration/login (hashed passwords)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Use password_hash() in PHP
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table: E-commerce items (beads, necklaces, etc.)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'assets/images/products/default.jpg',  -- Path relative to root
    category ENUM('bracelet', 'necklace', 'ring', 'waistbead', 'earrings') NOT NULL,
    stock INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Carts Table: Session-based for guests, DB for logged-in (link via user_id)
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255),  -- For guest carts (use session_id())
    user_id INT NULL,  -- NULL for guests
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table: Post-checkout (status tracking)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id VARCHAR(255),  -- For guest orders
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),  -- e.g., 'card', 'paypal' (mock for now)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table: Line items per order
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,  -- Price at time of order
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Custom Requests Table: From custom-design form (emails + stores)
CREATE TABLE IF NOT EXISTS custom_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    jewelry_type ENUM('bracelet', 'necklace', 'ring', 'waistbead') NOT NULL,
    description TEXT NOT NULL,
    budget DECIMAL(10,2),
    occasion VARCHAR(100),
    status ENUM('new', 'contacted', 'in_progress', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlist Table: Optional (if ENABLE_WISHLIST true)
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data: Users (1 admin-like user)
INSERT INTO users (full_name, email, password, phone, role) VALUES
('Admin User', 'admin@pinnahs.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+15551234567', 'admin');  -- Password: 'password' (change in prod)

-- Sample Data: Products (5 handcrafted items matching theme)
INSERT INTO products (name, description, price, image, category, stock, is_featured) VALUES
('Coral Sunset Necklace', 'Handcrafted vibrant coral beads capturing golden hour magic.', 45.99, 'assets/images/products/coral-necklace.jpg', 'necklace', 10, TRUE),
('Waistbead Bracelet', 'Elegant waist jewelry with soft pink and purple beads.', 29.99, 'assets/images/products/waistbead-bracelet.jpg', 'waistbead', 15, TRUE),
('Purple Ring', 'Delicate finger jewelry with luxurious bead accents.', 19.99, 'assets/images/products/purple-ring.jpg', 'ring', 20, FALSE),
('Beaded Bracelet', 'Wrist piece blending pink tones and purple highlights.', 34.50, 'assets/images/products/beaded-bracelet.jpg', 'bracelet', 8, FALSE),
('Custom Earrings', 'Sparkling ear treasures for everyday elegance.', 24.99, 'assets/images/products/custom-earrings.jpg', 'earrings', 12, FALSE);

-- Sample Data: Orders (1 test order)
INSERT INTO orders (user_id, session_id, total, status, shipping_address, payment_method) VALUES
(1, 'test_session_123', 75.98, 'pending', '123 Test St, Test City, SC 12345', 'card');

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 45.99),
(1, 2, 1, 29.99);

-- Sample Data: Custom Requests (1 test request)
INSERT INTO custom_requests (user_name, user_email, phone, jewelry_type, description, budget, occasion) VALUES
('Jane Doe', 'jane@example.com', '+15559876543', 'necklace', 'Elegant purple and pink beads for a wedding, under $100 budget.', 80.00, 'Wedding');

-- Verify: Run SELECT COUNT(*) FROM users; etc., to check inserts.
-- Notes: Add images to assets/images/products/. For carts, populate via PHP on add-to-cart.