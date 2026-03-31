-- ============================================================
-- DRIFTER — Railway Single Database Setup
-- Run this in Railway's MySQL shell or phpMyAdmin
-- All 3 databases merged into one (Railway free = 1 DB)
-- ============================================================

-- USERS & VEHICLES (same as local 'db')
CREATE TABLE IF NOT EXISTS signup (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('customer','owner','company') DEFAULT 'customer',
    phone      VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS support_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    phone      VARCHAR(20) DEFAULT NULL,
    service    VARCHAR(50) DEFAULT NULL,
    message    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COURIER (merged from 'drifter_courier')
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
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    company_id   INT NOT NULL,
    service_type ENUM('same_day','next_day','standard','international') NOT NULL,
    min_price    DECIMAL(10,2),
    max_price    DECIMAL(10,2),
    max_weight   DECIMAL(10,2),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

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
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- MOVERS (merged from 'moveeasy')
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
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS movers_services (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    company_id    INT NOT NULL,
    service_type  ENUM('packing','moving','packing_moving','full_service','vehicle','international') NOT NULL,
    min_price     DECIMAL(10,2),
    max_price     DECIMAL(10,2),
    property_type ENUM('1bhk','2bhk','3bhk','villa','office') NOT NULL,
    FOREIGN KEY (company_id) REFERENCES movers_companies(id) ON DELETE CASCADE
);

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
    FOREIGN KEY (company_id) REFERENCES movers_companies(id) ON DELETE SET NULL
);

-- ============================================================
-- VIEWS — allow move/ PHP files to query without table renames
-- ============================================================
-- Note: courier/ already uses 'companies','services','user_requests'
-- move/ also uses same names — we create aliases via views
-- Since both use same DB, move/ files use movers_ prefixed tables directly
-- The views below let move/ code work unchanged:

CREATE OR REPLACE VIEW movers_companies_view AS SELECT * FROM movers_companies;
CREATE OR REPLACE VIEW movers_services_view  AS SELECT * FROM movers_services;
CREATE OR REPLACE VIEW movers_requests_view  AS SELECT * FROM movers_requests;
