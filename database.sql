-- database.sql
-- Zeekay Store Database Schema

CREATE DATABASE IF NOT EXISTS zeekay_store;
USE zeekay_store;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.png',
    stock INT DEFAULT 0,
    category VARCHAR(100),
    featured BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(12, 2) NOT NULL,
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_zip VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transaction_id VARCHAR(100),
    status VARCHAR(50),
    payload JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Insert sample products
INSERT INTO products (name, description, price, image, stock, category, featured) VALUES
('Premium Wireless Headphones', 'High-quality wireless headphones with noise cancellation', 149.99, 'headphones.jpg', 25, 'Electronics', 1),
('Smart Fitness Watch', 'Track your health and fitness with this advanced smart watch', 199.99, 'watch.jpg', 15, 'Electronics', 1),
('Organic Cotton T-Shirt', 'Comfortable 100% organic cotton t-shirt', 29.99, 'tshirt.jpg', 50, 'Clothing', 0),
('Professional Backpack', 'Durable waterproof backpack for travel and work', 79.99, 'backpack.jpg', 30, 'Accessories', 1),
('Wireless Charging Pad', 'Fast wireless charging for all Qi-compatible devices', 39.99, 'charger.jpg', 40, 'Electronics', 0),
('Yoga Mat Premium', 'Eco-friendly non-slip yoga mat', 45.99, 'yoga.jpg', 20, 'Sports', 0);

-- Insert admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@zeekay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');