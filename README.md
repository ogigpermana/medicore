# MediCore — Intelligent Pharmacy & Dispensing ERP System

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![PHPUnit Tests](https://img.shields.io/badge/tests-87%20passed%20(100%25)-success.svg)](./tests)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/status-Production%20Ready%20v1.0.0-emerald.svg)](https://github.com/ogigpermana/medicore)

MediCore is an enterprise-grade Pharmacy Management and Clinical Dispensing Operating System built with a high-performance custom PHP MVC framework. Engineered specifically for retail drugstores, clinic dispensaries, and multi-branch pharmacy chains in compliance with BPOM and Indonesian Ministry of Health standards.

---

## 🎯 Key Architectural Highlights

* **Clinical Dispensing Engine:** Structured validation checklist for physician licenses, drug-drug interaction alerts, standard & compound (*racikan*) dispensing, and automated label printing.
* **FEFO Inventory & Batch Sentinel:** First-Expired, First-Out automatic stock allocation with proactive warning sentinels (90, 60, and 30-day expiry thresholds) eliminating medication write-off waste.
* **High-Speed Point of Sale (POS):** Lightning-fast barcode scanning (<120ms checkout), split payment methods (Cash, QRIS, Transfer), drawer management, and 58mm/80mm ESC/POS thermal printing.
* **Purchasing & AP Ledger:** Purchase Orders (Surat Pesanan PBF), Goods Receipt Notes (GRN) with batch/expiry ingestion, Accounts Payable ledger, and phased invoice settlement.
* **Multi-Branch Stock Transfers:** Inter-branch shipment requests, dispatch authorization, delivery note (*Surat Jalan*) generation, and receiving reconciliation.
* **Regulatory Compliance & Audit Trail:** Tamper-evident logging of every price override, stock adjustment, and dispense event to the `audit_logs` table with metadata inspection and CSV exports.
* **Dual-Layer Authentication:** Secure HTTP-only cookies for Web ERP + Stateless **JWT Bearer Authentication** (`firebase/php-jwt`) for mobile and external REST API integrations.

---

## 🚀 Quick Start & Installation

### 1. Requirements
* PHP 8.2 or higher (with `pdo_mysql`, `mbstring`, `openssl`)
* MySQL 8.0+ or MariaDB 10.6+
* Composer
* Web Server (Apache with `mod_rewrite` / Nginx / PHP built-in server)

### 2. Clone & Install Dependencies
```bash
git clone git@github.com:ogigpermana/medicore.git
cd medicore
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
# Edit .env and configure your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
```

### 4. Database Migration & Seeders
```bash
php database/migrate.php
php database/seed.php
```

### 5. Start Development Server
```bash
php -S localhost:8000 -t public
```
Open your browser and navigate to `http://localhost:8000`.

---

## 👥 Default Demo Credentials

All test accounts come pre-configured and verified:

| Role | Email | Password | Primary Module Access |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@medicore.com` | `admin123` | Full System Access, Audit Logs & Config |
| **Pharmacy Owner** | `owner@medicore.com` | `owner123` | Financial Analytics, AP Ledger & Multi-Branch |
| **Licensed Pharmacist** | `pharmacist@medicore.com` | `pharmacist123` | Clinical Review, Compound Formulas & Dispensing |
| **Cashier / Front Desk** | `cashier@medicore.com` | `cashier123` | POS Terminal, Barcode Scanning & Shift Drawer |
| **Warehouse Officer** | `warehouse@medicore.com` | `warehouse123` | Goods Receiving, Stock Opname & Transfers |

---

## 🔑 REST API & JWT Authentication

### 1. Obtain JWT Access Token
`POST /api/auth/login`
```json
{
  "email": "admin@medicore.com",
  "password": "admin123"
}
```
**Response:**
```json
{
  "success": true,
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### 2. Access Protected Endpoint
`GET /api/auth/me`
* **Header:** `Authorization: Bearer <access_token>`

### 3. Refresh Access Token
`POST /api/auth/refresh`
```json
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

## 🧪 Automated Test Suite

MediCore includes a comprehensive PHPUnit test suite covering all modules, models, controllers, and authentication layers:

```bash
./vendor/bin/phpunit
```
**Output:** `OK (87 tests, 332 assertions) — 100% Passing`

---

## 📁 Directory Structure

```
medicore/
├── app/
│   ├── Controllers/        # HTTP & REST API Request Controllers
│   ├── Models/             # Active Record & Repository Models
│   ├── Middleware/         # JWT, Role & Web Session Middleware
│   ├── Services/           # Business Services (Email, Validation)
│   └── Views/              # 31 Unified Modern Responsive Views
├── config/                 # App, Database, Email, and Session Configs
├── core/                   # Lightweight Custom Micro-Framework
│   ├── Application.php     # Bootstrap & Router Dispatcher
│   ├── Database.php        # PDO Database Wrapper
│   ├── Jwt.php             # Stateless JWT Token Issuer / Validator
│   └── Router.php          # RESTful URL Routing & Middleware Pipeline
├── database/
│   ├── migrations/         # 31 DDL Migration Files
│   └── seeders/            # 7 Production Data Seeders
├── public/                 # Web Root (index.php, CSS, JS, Uploads)
│   └── assets/             # CSS & Modular ES2025 JavaScript
├── storage/                # Daily Logs, File Uploads, Sockets
└── tests/                  # Unit & Integration PHPUnit Test Suite
```

---

## 📄 License
This project is open-source software licensed under the [MIT License](LICENSE).
