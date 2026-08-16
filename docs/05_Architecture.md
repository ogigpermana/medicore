# Technical Architecture & Stack

**Project:** MediCore - Pharmacy Management System  
**Version:** 1.0  
**Date:** August 16, 2026

## 1. Technology Stack

### Backend Stack

| Component | Technology | Version | Rationale |
|-----------|-----------|---------|-----------|
| **Language** | PHP | 8.1+ | Modern PHP with performance improvements, JIT compiler |
| **Web Server** | Apache/Nginx | Latest | Production-ready, widely supported |
| **Database** | MariaDB | 10.6+ | MySQL-compatible, better performance, open source |
| **ORM** | Custom PDO Wrapper | - | Lightweight, full control, portfolio showcase |
| **Routing** | Custom Router | - | Clean URLs, middleware support |
| **Authentication** | JWT (Firebase-JWT) | Latest | Stateless, scalable, secure |
| **Validation** | Custom Validator | - | Lightweight, reusable |
| **Logging** | Monolog | Latest | Industry standard, flexible |
| **Testing** | PHPUnit | Latest | PHP unit testing framework |

### Frontend Stack

| Component | Technology | Version | Rationale |
|-----------|-----------|---------|-----------|
| **Framework** | Modern Vanilla JS (ES2025) | Latest | Security-focused, maintainable, no dependencies |
| **Build Tool** | Vite | Latest | Modern ES features, hot reload, fast builds |
| **CSS Framework** | Bootstrap | 5.3+ | Responsive, modern, good documentation |
| **Icons** | FontAwesome 6.5.1 | Latest | Comprehensive icon library |
| **Charts** | Chart.js | Latest | Lightweight, good for reports |
| **PDF Generation** | TCPDF/DOMPDF | Latest | Server-side PDF generation |
| **Barcode** | JsBarcode | Latest | Client-side barcode generation |

### DevOps Stack

| Component | Technology | Version | Rationale |
|-----------|-----------|---------|-----------|
| **Version Control** | Git | Latest | Industry standard |
| **Containerization** | Docker (Optional) | Latest | Consistent environment |
| **Deployment** | FTP/SSH | - | Simple deployment |
| **Monitoring** | Custom logs | - | Basic error tracking |

## 2. System Architecture

### 2.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         Client Layer                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Desktop    │  │    Mobile    │  │    Tablet    │     │
│  │   Browser    │  │   Browser    │  │   Browser    │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTPS
┌───────────────────────────┴─────────────────────────────────┐
│                      Web Server Layer                         │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Apache/Nginx + PHP 8.1+                 │   │
│  └──────────────────────────────────────────────────────┘   │
└───────────────────────────┬─────────────────────────────────┘
                            │
┌───────────────────────────┴─────────────────────────────────┐
│                  Application Layer (PHP)                    │
│  ┌──────────────────────────────────────────────────────┐   │
│  │           Custom PHP Micro Framework                   │   │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐      │   │
│  │  │ Router  │ │ Auth    │ │ Validator│ │ Logging │      │   │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘      │   │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐      │   │
│  │  │Controller│ │Model   │ │View    │ │Middleware│     │   │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘      │   │
│  └──────────────────────────────────────────────────────┘   │
└───────────────────────────┬─────────────────────────────────┘
                            │ PDO
┌───────────────────────────┴─────────────────────────────────┐
│                    Data Layer                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                  MariaDB 10.6+                        │   │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐    │   │
│  │  │ Users   │ │Products │ │Sales    │ │Reports │    │   │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘    │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Framework Architecture (MVC Pattern)

```
Framework Structure:
├── Core/
│   ├── Application.php          # Main application bootstrap
│   ├── Router.php               # URL routing & dispatching
│   ├── Controller.php           # Base controller
│   ├── Model.php                # Base model with database
│   ├── View.php                 # View renderer
│   ├── Database.php             # PDO wrapper
│   ├── Request.php              # HTTP request handler
│   ├── Response.php             # HTTP response handler
│   ├── Session.php              # Session management
│   ├── Auth.php                 # Authentication (JWT)
│   ├── Validator.php           # Input validation
│   ├── Middleware.php           # Middleware pipeline
│   └── Logger.php               # Logging interface
├── Config/
│   ├── config.php               # App configuration
│   ├── database.php             # Database config
│   └── routes.php               # Route definitions
├── Controllers/
│   ├── AuthController.php
│   ├── ProductController.php
│   ├── SaleController.php
│   └── ReportController.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Sale.php
│   └── Prescription.php
├── Views/
│   ├── layouts/
│   │   └── main.php
│   ├── auth/
│   ├── products/
│   └── sales/
├── Middleware/
│   ├── AuthMiddleware.php
│   ├── RoleMiddleware.php
│   └── CsrfMiddleware.php
└── Public/
    ├── index.php                # Entry point
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   └── images/
```

## 3. Security Architecture

### 3.1 Security Layers

```
┌─────────────────────────────────────────┐
│         Application Security            │
│  ┌──────────────────────────────────┐  │
│  │  • Input Validation              │  │
│  │  • Output Escaping               │  │
│  │  • CSRF Protection              │  │
│  │  • XSS Prevention               │  │
│  └──────────────────────────────────┘  │
├─────────────────────────────────────────┤
│         Authentication Security         │
│  ┌──────────────────────────────────┐  │
│  │  • JWT Tokens                    │  │
│  │  • Password Hashing (bcrypt)     │  │
│  │  • Session Management            │  │
│  │  • Rate Limiting                 │  │
│  └──────────────────────────────────┘  │
├─────────────────────────────────────────┤
│         Network Security               │
│  ┌──────────────────────────────────┐  │
│  │  • HTTPS/TLS 1.3                 │  │
│  │  • Secure Headers                │  │
│  │  • CORS Configuration            │  │
│  └──────────────────────────────────┘  │
├─────────────────────────────────────────┤
│         Database Security               │
│  ┌──────────────────────────────────┐  │
│  │  • Prepared Statements           │  │
│  │  • Least Privilege Principle      │  │
│  │  • Data Encryption at Rest       │  │
│  │  • Regular Backups                │  │
│  └──────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

### 3.2 Security Implementation

**Password Hashing:**
```php
password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

**SQL Injection Prevention:**
```php
// Always use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

**XSS Prevention:**
```php
htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

**CSRF Protection:**
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new CsrfException();
}
```

## 4. API Design (RESTful)

### 4.1 API Endpoints

**Authentication:**
```
POST   /api/auth/register     - Register new user
POST   /api/auth/login        - Login user
POST   /api/auth/logout       - Logout user
POST   /api/auth/refresh      - Refresh JWT token
POST   /api/auth/forgot       - Request password reset
POST   /api/auth/reset        - Reset password
```

**Products:**
```
GET    /api/products          - List all products
GET    /api/products/{id}     - Get single product
POST   /api/products          - Create product
PUT    /api/products/{id}     - Update product
DELETE /api/products/{id}     - Delete product
GET    /api/products/search   - Search products
GET    /api/products/low-stock - Get low stock products
GET    /api/products/expiring - Get expiring products
```

**Sales:**
```
GET    /api/sales             - List all sales
GET    /api/sales/{id}        - Get single sale
POST   /api/sales             - Create sale
PUT    /api/sales/{id}        - Update sale
DELETE /api/sales/{id}        - Void sale
GET    /api/sales/daily       - Daily sales report
GET    /api/sales/monthly     - Monthly sales report
```

**Prescriptions:**
```
GET    /api/prescriptions     - List prescriptions
GET    /api/prescriptions/{id} - Get single prescription
POST   /api/prescriptions     - Create prescription
PUT    /api/prescriptions/{id} - Update prescription
POST   /api/prescriptions/{id}/dispense - Dispense prescription
```

**Reports:**
```
GET    /api/reports/sales      - Sales report
GET    /api/reports/inventory  - Inventory report
GET    /api/reports/profit     - Profit/Loss report
GET    /api/reports/expiry     - Expiry report
GET    /api/reports/pdf        - Generate PDF report
GET    /api/reports/excel      - Generate Excel report
```

### 4.2 Response Format

**Success Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Paracetamol",
        "price": 5000
    },
    "message": "Product retrieved successfully"
}
```

**Error Response:**
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Invalid input data",
        "details": {
            "name": ["Name is required"],
            "price": ["Price must be greater than 0"]
        }
    }
}
```

## 5. Performance Optimization

### 5.1 Database Optimization
- Indexing on frequently queried columns
- Query optimization with EXPLAIN
- Connection pooling
- Read replicas (if scaling needed)

### 5.2 Caching Strategy
- Query result caching (optional Redis)
- Static asset caching
- Browser caching headers

### 5.3 Code Optimization
- Opcache enabled
- Lazy loading
- Minimize database queries
- Use pagination for large datasets

## 6. Frontend Architecture (Modern Vanilla JS)

### 6.1 Module-Based Structure

```
public/assets/js/
├── app.js                    # Main application entry point
├── modules/
│   ├── cart.js              # Shopping cart management
│   ├── api.js               # API client with security
│   ├── validator.js        # Input validation & sanitization
│   ├── barcode.js          # Barcode scanner integration
│   ├── auth.js             # Authentication handling
│   └── ui.js               # UI components & helpers
├── utils/
│   ├── format.js           # Number/date formatting
│   ├── storage.js          # LocalStorage wrapper
│   └── dom.js              # DOM manipulation helpers
└── config/
    └── constants.js        # Application constants
```

### 6.2 Security Features

**Built-in Security:**
- CSRF token handling di setiap API request
- Input sanitization untuk prevent XSS
- Content Security Policy headers
- Secure cookie handling
- No third-party dependency vulnerabilities

**Example Security Implementation:**
```javascript
// modules/api.js
export class ApiClient {
    #getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
    
    async post(endpoint, data) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': this.#getCsrfToken()
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        return response.json();
    }
}
```

### 6.3 Maintainability Features

**Modern ES2025 Features:**
- ES Modules untuk code organization
- Private class fields (#) untuk encapsulation
- Async/await untuk clean async code
- Optional chaining (?.) untuk safe property access
- Nullish coalescing (??) untuk default values
- Pattern matching untuk complex logic

**Class-Based Architecture:**
```javascript
// modules/cart.js
export class CartManager {
    #items = [];
    #listeners = [];
    
    constructor() {
        this.#loadFromStorage();
    }
    
    addItem(product, quantity = 1) {
        const existingItem = this.#items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.#items.push({ ...product, quantity });
        }
        this.#saveToStorage();
        this.#notifyListeners();
    }
    
    #saveToStorage() {
        localStorage.setItem('cart', JSON.stringify(this.#items));
    }
}
```

### 6.4 Performance Benefits

**Optimizations:**
- No framework overhead → faster load times
- Tree-shaking dengan Vite → hanya include used code
- Lazy loading modules → on-demand loading
- Efficient DOM manipulation → direct control
- Small bundle size → faster downloads

## 7. Deployment Architecture

### 7.1 Development Environment
- Local PHP 8.1+ installation
- Local MariaDB instance
- Apache/Nginx with mod_rewrite
- Git for version control

### 7.2 Production Environment
- VPS or cloud hosting
- SSL certificate (Let's Encrypt)
- Database backups (daily)
- Error logging and monitoring
- CDN for static assets (optional)

### 7.3 Backup Strategy
- Daily database backups
- Weekly full system backups
- Offsite backup storage
- Backup retention policy (30 days)

## 8. Monitoring & Logging

### 8.1 Application Logging
- Error logs (all errors)
- Access logs (API calls)
- Audit logs (user actions)
- Performance logs (slow queries)

### 8.2 Monitoring Metrics
- Response time
- Error rate
- Database query time
- Memory usage
- Disk space

---

**Document Status:** Approved  
**Next Phase:** Database Design