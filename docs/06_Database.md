# Database Schema Design

**Project:** MediCore - Pharmacy Management System  
**Version:** 1.0  
**Database:** MariaDB 10.6+  
**Date:** August 16, 2026

## 1. Entity Relationship Diagram (ERD)

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   users     │       │ user_roles  │       │   roles     │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │◄──────│ user_id (FK)│       │ id (PK)     │
│ email       │       │ role_id (FK)│──────►│ name        │
│ password    │       │ assigned_at │       │ description │
│ full_name   │       └─────────────┘       │ permissions │
│ phone       │                               └─────────────┘
│ avatar      │
│ is_active   │       ┌─────────────┐
│ created_at  │       │ businesses  │
│ updated_at  │       ├─────────────┤
└─────────────┘       │ id (PK)     │
                      │ user_id (FK)│───┐
                      │ name        │   │
                      │ address     │   │
                      │ phone       │   │
                      │ email       │   │
                      │ tax_id      │   │
                      │ created_at  │   │
                      └─────────────┘   │
                                        │
┌─────────────┐       ┌─────────────┐   │
│ suppliers   │       │  products   │◄──┘
├─────────────┤       ├─────────────┤
│ id (PK)     │       │ id (PK)     │
│ name        │       │ business_id │
│ contact     │       │ supplier_id │───┐
│ phone       │       │ category_id │   │
│ email       │       │ name        │   │
│ address     │       │ sku         │   │
│ is_active   │       │ barcode     │   │
│ created_at  │       │ description │   │
└─────────────┘       │ generic_name│   │
                      │ brand_name  │   │
┌─────────────┐       │ manufacturer│   │
│ categories  │       │ unit        │   │
├─────────────┤       │ cost_price  │   │
│ id (PK)     │◄──────│ sell_price  │   │
│ name        │       │ stock       │   │
│ description │       │ min_stock   │   │
│ is_active   │       │ max_stock   │   │
│ created_at  │       │ batch_no    │   │
└─────────────┘       │ expiry_date │   │
                      │ rack_location│  │
┌─────────────┐       │ is_active   │   │
│ customers   │       │ created_at  │   │
├─────────────┤       │ updated_at  │   │
│ id (PK)     │       └─────────────┘   │
│ name        │                          │
│ phone       │       ┌─────────────┐   │
│ email       │       │ stock_movements│
│ address     │◄──────├─────────────┤   │
│ birth_date  │       │ id (PK)     │   │
│ segment_id  │       │ product_id  │───┘
│ is_active   │       │ type        │
│ created_at  │       │ quantity    │
└─────────────┘       │ reference   │
                      │ notes       │
┌─────────────┐       │ created_at  │
│ segments    │       └─────────────┘
├─────────────┤       ┌─────────────┐
│ id (PK)     │       │   sales     │
│ name        │       ├─────────────┤
│ description │       │ id (PK)     │
│ discount    │       │ business_id │
│ created_at  │       │ customer_id │───┐
└─────────────┘       │ user_id     │   │
                      │ invoice_no  │   │
                      │ total_amount│   │
┌─────────────┐       │ discount    │   │
│ doctors     │       │ tax_amount  │   │
├─────────────┤       │ final_amount│   │
│ id (PK)     │       │ payment_method│  │
│ name        │       │ payment_status│  │
│ license_no  │       │ status      │   │
│ specialty   │       │ notes       │   │
│ phone       │       │ created_at  │   │
│ email       │       └─────────────┘   │
│ is_active   │                          │
│ created_at  │       ┌─────────────┐   │
└─────────────┘       │ sale_items  │◄──┘
                      ├─────────────┤
┌─────────────┐       │ id (PK)     │
│prescriptions│       │ sale_id (FK)│
├─────────────┤       │ product_id  │───┐
│ id (PK)     │       │ quantity    │   │
│ customer_id │       │ unit_price  │   │
│ doctor_id   │       │ discount    │   │
│ prescription_no│     │ subtotal    │   │
│ diagnosis   │       │ created_at  │   │
│ notes       │       └─────────────┘   │
│ status      │                          │
│ dispensed_at│       ┌─────────────┐   │
│ created_at  │       │ prescription│   │
└─────────────┘       │    _items   │   │
                      ├─────────────┤   │
                      │ id (PK)     │   │
                      │ prescription│   │
                      │    _id (FK) │   │
                      │ product_id  │───┘
                      │ dosage      │
                      │ frequency   │
                      │ duration    │
                      │ notes       │
                      └─────────────┘
```

## 2. Table Definitions

### 2.1 Users & Authentication

**Table: `users`**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `roles`**
```sql
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed roles
INSERT INTO roles (name, description, permissions) VALUES
('superadmin', 'Full system access', '["*"]'),
('owner', 'Business owner with full access to business data', '["products.*","sales.*","reports.*","customers.*"]'),
('pharmacist', 'Can manage inventory and validate prescriptions', '["products.read","products.write","prescriptions.*","customers.read"]'),
('cashier', 'Can process sales and view basic inventory', '["sales.*","products.read","customers.read"]'),
('warehouse', 'Can manage inventory only', '["products.*","stock.*"]');
```

**Table: `user_roles`**
```sql
CREATE TABLE user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role (user_id, role_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role_id (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 Business & Location

**Table: `businesses`**
```sql
CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    tax_id VARCHAR(50),
    license_no VARCHAR(100),
    logo VARCHAR(255),
    currency VARCHAR(3) DEFAULT 'IDR',
    tax_rate DECIMAL(5,2) DEFAULT 11.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3 Inventory Management

**Table: `categories`**
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id INT DEFAULT NULL,
    icon VARCHAR(50),
    color VARCHAR(7),
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `suppliers`**
```sql
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    tax_id VARCHAR(50),
    payment_terms VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `products`**
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    supplier_id INT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    barcode VARCHAR(100),
    generic_name VARCHAR(255),
    brand_name VARCHAR(255),
    manufacturer VARCHAR(255),
    description TEXT,
    unit VARCHAR(50) DEFAULT 'pcs',
    cost_price DECIMAL(15,2) NOT NULL,
    sell_price DECIMAL(15,2) NOT NULL,
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 10,
    max_stock INT DEFAULT 1000,
    batch_no VARCHAR(100),
    expiry_date DATE,
    rack_location VARCHAR(50),
    is_prescription_required TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_category_id (category_id),
    INDEX idx_sku (sku),
    INDEX idx_barcode (barcode),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_is_active (is_active),
    INDEX idx_stock (stock)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `stock_movements`**
```sql
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    product_id INT NOT NULL,
    type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    previous_stock INT NOT NULL,
    new_stock INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id INT,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_product_id (product_id),
    INDEX idx_type (type),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.4 Customer Management

**Table: `segments`**
```sql
CREATE TABLE segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    min_purchase_amount DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed segments
INSERT INTO segments (name, description, discount_percent) VALUES
('Regular', 'Regular customers', 0),
('VIP', 'VIP customers with special discounts', 5),
('Wholesale', 'Wholesale buyers', 10);
```

**Table: `customers`**
```sql
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    birth_date DATE,
    segment_id INT,
    loyalty_points INT DEFAULT 0,
    total_purchases DECIMAL(15,2) DEFAULT 0,
    last_purchase_at TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (segment_id) REFERENCES segments(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_segment_id (segment_id),
    INDEX idx_phone (phone),
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.5 Sales & Transactions

**Table: `sales`**
```sql
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    customer_id INT,
    user_id INT NOT NULL,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    change_amount DECIMAL(15,2) DEFAULT 0,
    payment_method ENUM('cash', 'transfer', 'ewallet', 'qris', 'credit') NOT NULL,
    payment_status ENUM('pending', 'paid', 'partial', 'refunded') DEFAULT 'paid',
    status ENUM('completed', 'voided', 'on_hold') DEFAULT 'completed',
    notes TEXT,
    voided_by INT,
    voided_at TIMESTAMP NULL,
    void_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (voided_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_user_id (user_id),
    INDEX idx_invoice_no (invoice_no),
    INDEX idx_payment_status (payment_status),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `sale_items`**
```sql
CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL,
    cost_price DECIMAL(15,2) NOT NULL,
    profit DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_sale_id (sale_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.6 Prescription Management

**Table: `doctors`**
```sql
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    license_no VARCHAR(100),
    specialty VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    clinic_address TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_license_no (license_no),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `prescriptions`**
```sql
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    customer_id INT,
    doctor_id INT,
    prescription_no VARCHAR(50) UNIQUE NOT NULL,
    diagnosis TEXT,
    chief_complaint TEXT,
    notes TEXT,
    attachment_path VARCHAR(255),
    status ENUM('pending', 'validated', 'dispensed', 'completed') DEFAULT 'pending',
    validated_by INT,
    validated_at TIMESTAMP NULL,
    dispensed_by INT,
    dispensed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (dispensed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_prescription_no (prescription_no),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `prescription_items`**
```sql
CREATE TABLE prescription_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    product_id INT NOT NULL,
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    duration VARCHAR(100),
    quantity INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_prescription_id (prescription_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.7 Audit & System

**Table: `audit_logs`**
```sql
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    user_id INT,
    action ENUM('create', 'update', 'delete', 'login', 'logout') NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_data JSON,
    new_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Table: `settings`**
```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    key VARCHAR(100) NOT NULL,
    value TEXT,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_business_setting (business_id, key),
    INDEX idx_business_id (business_id),
    INDEX idx_key (key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 3. Database Triggers

### Trigger: Update product stock on sale
```sql
DELIMITER //
CREATE TRIGGER after_sale_item_insert
AFTER INSERT ON sale_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.product_id;
    
    INSERT INTO stock_movements (
        business_id, product_id, type, quantity, 
        previous_stock, new_stock, reference_type, reference_id, created_by
    )
    SELECT 
        p.business_id, NEW.product_id, 'out', NEW.quantity,
        p.stock + NEW.quantity, p.stock, 'sale', NEW.sale_id, s.user_id
    FROM products p
    JOIN sales s ON s.id = NEW.sale_id
    WHERE p.id = NEW.product_id;
END//
DELIMITER ;
```

### Trigger: Calculate sale totals
```sql
DELIMITER //
CREATE TRIGGER before_sale_insert
BEFORE INSERT ON sales
FOR EACH ROW
BEGIN
    -- Generate invoice number
    SET NEW.invoice_no = CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));
END//
DELIMITER ;
```

## 4. Database Views

### View: Low stock products
```sql
CREATE VIEW v_low_stock_products AS
SELECT 
    p.id,
    p.business_id,
    p.name,
    p.sku,
    p.stock,
    p.min_stock,
    p.expiry_date,
    c.name as category_name,
    (p.min_stock - p.stock) as shortage
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.stock <= p.min_stock
AND p.is_active = 1;
```

### View: Expiring products
```sql
CREATE VIEW v_expiring_products AS
SELECT 
    p.id,
    p.business_id,
    p.name,
    p.sku,
    p.stock,
    p.expiry_date,
    DATEDIFF(p.expiry_date, CURDATE()) as days_until_expiry,
    c.name as category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
AND p.is_active = 1
ORDER BY p.expiry_date ASC;
```

### View: Daily sales summary
```sql
CREATE VIEW v_daily_sales_summary AS
SELECT 
    DATE(s.created_at) as sale_date,
    s.business_id,
    COUNT(*) as total_transactions,
    SUM(s.total_amount) as total_revenue,
    SUM(s.profit) as total_profit,
    AVG(s.total_amount) as average_transaction
FROM sales s
WHERE s.status = 'completed'
GROUP BY DATE(s.created_at), s.business_id;
```

## 5. Database Indexes Summary

### Performance Critical Indexes
- `products.sku` - For barcode scanning
- `products.barcode` - For barcode scanning
- `products.expiry_date` - For expiry alerts
- `products.stock` - For low stock alerts
- `sales.invoice_no` - For transaction lookup
- `sales.created_at` - For date-based reports
- `sale_items.sale_id` - For sale detail queries
- `stock_movements.product_id` - For stock history

---

**Document Status:** Approved  
**Next Phase:** Framework Design