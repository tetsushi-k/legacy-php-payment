-- legacy-php-payment 共通スキーマ
-- legacy/ と modern/ で共有

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL COMMENT '金額（円）',
    status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    before_data JSON NULL,
    after_data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_entity (entity_type, entity_id)
);

-- テストユーザー（password: password123）
INSERT INTO users (email, password_hash, role) VALUES
    ('test@example.com', '$2y$10$Jq5X0YYyowXIy12hpDwrhulJNVOFS02xcwUmKXFE/zyvA.S6YcFKu', 'user'),
    ('admin@example.com', '$2y$10$Jq5X0YYyowXIy12hpDwrhulJNVOFS02xcwUmKXFE/zyvA.S6YcFKu', 'admin');

-- テスト注文
INSERT INTO orders (user_id, amount, status) VALUES
    (1, 1500, 'pending'),
    (1, 3200, 'paid'),
    (1, 800, 'pending'),
    (2, 5000, 'pending');
