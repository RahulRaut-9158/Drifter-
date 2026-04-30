-- ============================================================
-- DRIFTER — InfinityFree Single-Database Setup v4
-- Import this ENTIRE file in your InfinityFree phpMyAdmin SQL tab
-- ============================================================

-- USERS & AUTHENTICATION
CREATE TABLE IF NOT EXISTS signup (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('customer','owner','company','admin') DEFAULT 'customer',
    phone      VARCHAR(20) DEFAULT NULL,
    is_active  TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- VEHICLES (TRANSPORT & TRAVEL)
CREATE TABLE IF NOT EXISTS vehicles (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    owner_name       VARCHAR(100) NOT NULL,
    mobile           VARCHAR(20)  NOT NULL,
    email            VARCHAR(100) NOT NULL,
    address          TEXT         NOT NULL,
    license_image    VARCHAR(255),
    vehicle_image    VARCHAR(255),
    capacity         DECIMAL(10,2),
    rate_per_km      DECIMAL(10,2),
    vehicle_category ENUM('transport','travel') DEFAULT 'transport',
    is_available     TINYINT(1) DEFAULT 1,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_owner (owner_name),
    INDEX idx_category (vehicle_category),
    INDEX idx_available (is_available)
);

-- BOOKINGS
CREATE TABLE IF NOT EXISTS booking (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id       INT NOT NULL,
    user_name        VARCHAR(100) NOT NULL,
    user_mobile      VARCHAR(20)  NOT NULL,
    pickup_location  TEXT NOT NULL,
    drop_location    TEXT NOT NULL,
    distance_km      DECIMAL(10,2),
    total_cost       DECIMAL(10,2),
    date             DATE,
    time             TIME,
    status           ENUM('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
    cancel_reason    VARCHAR(255) DEFAULT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    INDEX idx_user (user_name),
    INDEX idx_status (status),
    INDEX idx_date (date)
);

-- SUPPORT MESSAGES
CREATE TABLE IF NOT EXISTS support_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    phone      VARCHAR(20) DEFAULT NULL,
    service    VARCHAR(50) DEFAULT NULL,
    message    TEXT NOT NULL,
    status     ENUM('unread','read','replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- COURIER COMPANIES
CREATE TABLE IF NOT EXISTS companies (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    owner_username    VARCHAR(100) DEFAULT NULL,
    name              VARCHAR(100) NOT NULL,
    description       TEXT,
    email             VARCHAR(100) NOT NULL,
    phone             VARCHAR(20)  NOT NULL,
    address           TEXT NOT NULL,
    service_locations TEXT NOT NULL,
    services_offered  TEXT NOT NULL,
    rating            DECIMAL(3,1) DEFAULT 0,
    reviews           INT DEFAULT 0,
    logo_path         VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_owner (owner_username)
);

-- COURIER SERVICES
CREATE TABLE IF NOT EXISTS services (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    company_id   INT NOT NULL,
    service_type ENUM('same_day','next_day','standard','international') NOT NULL,
    min_price    DECIMAL(10,2),
    max_price    DECIMAL(10,2),
    max_weight   DECIMAL(10,2),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

-- COURIER REQUESTS
CREATE TABLE IF NOT EXISTS user_requests (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    company_id       INT DEFAULT NULL,
    sender_name      VARCHAR(100) NOT NULL,
    sender_phone     VARCHAR(20)  NOT NULL,
    sender_address   TEXT NOT NULL,
    pickup_date      DATE NOT NULL,
    receiver_name    VARCHAR(100) NOT NULL,
    receiver_phone   VARCHAR(20)  NOT NULL,
    receiver_address TEXT NOT NULL,
    delivery_date    DATE NOT NULL,
    package_details  TEXT,
    status           ENUM('Pending','Assigned','Delivered','Cancelled') DEFAULT 'Pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_status (status)
);

-- MOVERS COMPANIES
CREATE TABLE IF NOT EXISTS movers_companies (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    owner_username    VARCHAR(100) DEFAULT NULL,
    name              VARCHAR(100) NOT NULL,
    description       TEXT,
    email             VARCHAR(100) NOT NULL,
    phone             VARCHAR(20)  NOT NULL,
    address           TEXT NOT NULL,
    service_locations TEXT NOT NULL,
    services_offered  TEXT NOT NULL,
    rating            DECIMAL(3,1) DEFAULT 0,
    reviews           INT DEFAULT 0,
    logo_path         VARCHAR(255),
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_owner (owner_username)
);

-- MOVERS SERVICES
CREATE TABLE IF NOT EXISTS movers_services (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    company_id    INT NOT NULL,
    service_type  ENUM('packing','moving','packing_moving','full_service','vehicle','international') NOT NULL,
    min_price     DECIMAL(10,2),
    max_price     DECIMAL(10,2),
    property_type ENUM('1bhk','2bhk','3bhk','villa','office') NOT NULL,
    FOREIGN KEY (company_id) REFERENCES movers_companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

-- MOVERS REQUESTS
CREATE TABLE IF NOT EXISTS movers_requests (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    company_id       INT DEFAULT NULL,
    customer_name    VARCHAR(100) DEFAULT NULL,
    current_address  TEXT NOT NULL,
    new_address      TEXT NOT NULL,
    moving_date      DATE NOT NULL,
    property_type    ENUM('1bhk','2bhk','3bhk','villa','office') NOT NULL,
    work_type        ENUM('packing','moving','packing_moving','full_service','vehicle','international') NOT NULL,
    special_items    TEXT,
    additional_info  TEXT,
    status           ENUM('Pending','Assigned','Completed','Cancelled') DEFAULT 'Pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES movers_companies(id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_status (status)
);

-- ADMIN ACTIVITY LOG
CREATE TABLE IF NOT EXISTS admin_logs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    admin_user VARCHAR(100) NOT NULL,
    action     VARCHAR(255) NOT NULL,
    target     VARCHAR(255) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_user),
    INDEX idx_created (created_at)
);

-- ══════════════════════════════════════════════════════════════════════════════
-- SEED DATA — Admin account + test users
-- ══════════════════════════════════════════════════════════════════════════════

-- Admin account: username = drifter_admin, password = Admin@Drifter2025!
-- IMPORTANT: Change this password after first login!
INSERT IGNORE INTO signup (username, email, password, role, is_active) VALUES
('drifter_admin', 'admin@drifter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Test accounts (password = "password" for all)
INSERT IGNORE INTO signup (username, email, password, role) VALUES
('testcustomer', 'customer@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('testowner',    'owner@test.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner'),
('testcompany',  'company@test.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company');

-- ══════════════════════════════════════════════════════════════════════════════
-- IMPORTANT NOTES FOR INFINITYFREE:
-- 1. After importing this SQL, update config.php with your actual DB credentials
-- 2. Change admin password immediately after first login
-- 3. Delete gen_hash.php and admin/seed_admin.php after deployment
-- ══════════════════════════════════════════════════════════════════════════════
