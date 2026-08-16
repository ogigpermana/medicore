# MediCore - Pharmacy Management System

A modern, comprehensive pharmacy management system built with a custom PHP micro framework. Designed for small to medium-sized pharmacies in Indonesia.

## <i class="fas fa-clipboard-list"></i> Project Overview

**Project Name:** MediCore  
**Version:** 1.0  
**Status:** Planning Phase  
**Development Timeline:** 13 Weeks

## <i class="fas fa-bullseye"></i> Features

### Core Modules
- **Authentication & Authorization** - JWT-based auth with role-based access control
- **Inventory Management** - Complete product, supplier, and stock management
- **Point of Sale (POS)** - Barcode scanning, multiple payment methods, receipt generation
- **Prescription Management** - Digital prescription handling and validation
- **Customer Management** - Customer database with purchase history
- **Reporting & Analytics** - Comprehensive financial and inventory reports

### Security Features
- Row-level security with role-based access
- JWT token authentication
- SQL injection prevention
- XSS and CSRF protection
- Secure password hashing (bcrypt)
- Audit logging

## <i class="fas fa-tools"></i> Tech Stack

### Backend
- **Language:** PHP 8.1+
- **Database:** MariaDB 10.6+
- **Web Server:** Apache/Nginx
- **Custom Framework:** MediCore Framework (MVC Pattern)
- **Authentication:** JWT (Firebase PHP-JWT)
- **Logging:** Monolog

### Frontend
- **Framework:** Modern Vanilla JS (ES2025)
- **Build Tool:** Vite
- **CSS Framework:** Bootstrap 5.3+
- **Icons:** Bootstrap Icons
- **Charts:** Chart.js
- **PDF Generation:** TCPDF/DOMPDF
- **Barcode:** JsBarcode

### DevOps
- **Version Control:** Git
- **Package Manager:** Composer
- **Testing:** PHPUnit
- **Code Quality:** PHP CodeSniffer

## 📁 Project Structure

```
medicore/
├── app/                    # Application code
│   ├── Controllers/        # Request handlers
│   ├── Models/            # Database models
│   ├── Views/             # View templates
│   ├── Middleware/        # HTTP middleware
│   └── Services/          # Business logic
├── core/                  # Framework core
│   ├── Application.php    # Main bootstrap
│   ├── Router.php        # URL routing
│   ├── Controller.php     # Base controller
│   ├── Model.php          # Base model
│   ├── Database.php       # PDO wrapper
│   └── ...
├── config/                # Configuration files
├── public/                # Public assets
├── storage/               # Storage (logs, cache, sessions)
├── database/              # Migrations and seeds
├── tests/                 # Test files
└── docs/                  # Documentation
```

## <i class="fas fa-rocket"></i> Quick Start

### Prerequisites
- PHP 8.1 or higher
- MariaDB 10.6 or higher
- Apache/Nginx web server
- Composer

### Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/medicore.git
cd medicore
```

2. Install dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
# Edit .env with your database credentials
```

4. Run database migrations
```bash
php database/migrate.php
```

5. Start development server
```bash
php -S localhost:8000 -t public
```

6. Access the application
```
http://localhost:8000
```

## <i class="fas fa-book"></i> Documentation

### Planning Documents
- [PRD (Product Requirements Document)](docs/01_PRD.md)
- [SDLC Methodology](docs/02_SDLC.md)
- [Project Milestones](docs/03_Milestones.md)
- [Agile Scrum Framework](docs/04_Scrum.md)
- [Technical Architecture](docs/05_Architecture.md)
- [Database Schema](docs/06_Database.md)
- [Framework Design](docs/07_Framework.md)
- [Implementation Roadmap](docs/08_Roadmap.md)

### User Documentation
- [User Manual](docs/user-manual.md)
- [API Documentation](docs/api-documentation.md)
- [Deployment Guide](docs/deployment.md)

## 👥 User Roles

| Role | Permissions |
|------|-------------|
| **Superadmin** | Full system access |
| **Owner** | Business data access, reports |
| **Pharmacist** | Inventory, prescriptions |
| **Cashier** | POS, basic inventory view |
| **Warehouse** | Inventory management only |

## <i class="fas fa-lock"></i> Security

- HTTPS/TLS encryption
- JWT token authentication
- bcrypt password hashing
- Prepared statements (SQL injection prevention)
- XSS and CSRF protection
- Rate limiting
- Input validation and sanitization

## <i class="fas fa-chart-bar"></i> Development Progress

- [x] Requirements Analysis
- [x] System Design
- [x] Database Schema
- [x] Framework Architecture
- [x] Implementation Planning
- [ ] Core Framework Development
- [ ] Authentication System
- [ ] Inventory Management
- [ ] Point of Sale
- [ ] Prescription Management
- [ ] Reporting System
- [ ] Testing
- [ ] Deployment

## 🤝 Contributing

This is a portfolio project. For contributions:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📝 License

MIT License - See LICENSE file for details

## <i class="fas fa-user-code"></i> Author

Built as a portfolio project to demonstrate:
- Custom PHP framework development
- System architecture design
- Database design and optimization
- Security best practices
- Project management skills

## 📞 Support

For questions or support, please open an issue in the repository.

---

**Built with <i class="fas fa-heart"></i> using custom PHP micro framework**
