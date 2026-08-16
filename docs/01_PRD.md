# Product Requirements Document (PRD)

**Project:** MediCore - Pharmacy Management System  
**Version:** 1.0  
**Date:** August 16, 2026  
**Status:** Approved

## 1. Project Overview

### 1.1 Problem Statement
Apotek di Indonesia masih banyak yang menggunakan sistem manual (kertas, Excel) untuk:
- Manajemen stok obat
- Transaksi penjualan
- Tracking expired date
- Laporan keuangan

### 1.2 Solution Overview
Sistem manajemen apotek berbasis web yang terintegrasi dengan:
- Real-time inventory management
- Point of sale dengan barcode scanning
- Prescription management
- Automated expiry alerts
- Comprehensive reporting
- Multi-user dengan role-based access

## 2. Target Audience

### Primary Users
1. **Owner Apotek** - Fokus pada laporan, profit, dan oversight
2. **Kasir** - Fokus pada transaksi cepat dan mudah
3. **Apoteker** - Fokus pada validasi resep dan konsultasi
4. **Gudang/Inventory** - Fokus pada manajemen stok

## 3. Functional Requirements

### FR-1: Authentication & Authorization
- FR-1.1: User registration dengan email verification
- FR-1.2: Login dengan email/password dan JWT token
- FR-1.3: Role-based access control (RBAC)
- FR-1.4: Password reset via email
- FR-1.5: Session management dengan timeout
- FR-1.6: Activity logging untuk security audit

### FR-2: Inventory Management
- FR-2.1: CRUD data obat (kode, nama, kategori, manufacturer, expiry, dll)
- FR-2.2: Barcode/QR code generation untuk setiap obat
- FR-2.3: Stock adjustment (add/reduce/adjust)
- FR-2.4: Stock transfer antar lokasi (multi-cabang)
- FR-2.5: Low stock alert notification
- FR-2.6: Near expiry alert (30/60/90 days)
- FR-2.7: Supplier management (CRUD)
- FR-2.8: Purchase order (PO) ke supplier
- FR-2.9: Goods receipt note (GRN) saat stok masuk

### FR-3: Point of Sale (POS)
- FR-3.1: Quick product search (nama/kode/barcode)
- FR-3.2: Barcode scanning integration
- FR-3.3: Shopping cart dengan add/remove/quantity
- FR-3.4: Discount management (global, per-item, promo)
- FR-3.5: Multiple payment methods (cash, transfer, e-wallet)
- FR-3.6: Receipt generation (print thermal, PDF, email)
- FR-3.7: Tax calculation (PPN 11%)
- FR-3.8: Change calculation untuk cash payment
- FR-3.9: Hold/Resume transaction (antrian)
- FR-3.10: Void transaction dengan authorization

### FR-4: Prescription Management
- FR-4.1: Create prescription record (patient, doctor, list obat)
- FR-4.2: Scan/upload resep dokter (image/PDF)
- FR-4.3: Validation obat vs resep
- FR-4.4: Dispensing workflow (pick, validate, dispense)
- FR-4.5: Prescription history per patient
- FR-4.6: Doctor management (CRUD)
- FR-4.7: E-prescription integration (opsional - BPJS)

### FR-5: Customer Management
- FR-5.1: Customer registration (minimal: nama, phone)
- FR-5.2: Customer profile management
- FR-5.3: Purchase history per customer
- FR-5.4: Customer segmentation (regular, VIP, dll)
- FR-5.5: Loyalty points system (opsional)
- FR-5.6: Customer birthday notification (opsional)

### FR-6: Reporting & Analytics
- FR-6.1: Sales reports (daily, weekly, monthly)
- FR-6.2: Inventory reports (current stock, low stock, movement)
- FR-6.3: Financial reports (revenue, COGS, gross profit, net profit)
- FR-6.4: Product performance (best-selling, slow-moving, margin)
- FR-6.5: Expiry reports (expired, expiring soon)
- FR-6.6: User activity reports (admin)
- FR-6.7: Export reports ke PDF/Excel

### FR-7: System Configuration
- FR-7.1: Store profile management (nama, alamat, contact)
- FR-7.2: Tax configuration (PPN rate)
- FR-7.3: Payment method configuration
- FR-7.4: Printer configuration
- FR-7.5: Alert thresholds (low stock, expiry days)
- FR-7.6: Business hours configuration

## 4. Non-Functional Requirements

### NFR-1: Performance
- Page load time < 2 seconds
- API response time < 500ms (95th percentile)
- Support 100+ concurrent users
- Database query optimization

### NFR-2: Security
- HTTPS encryption (TLS 1.3)
- Password hashing (bcrypt/Argon2)
- SQL injection prevention
- XSS protection
- CSRF protection
- Rate limiting on API
- Input validation & sanitization
- Secure file upload

### NFR-3: Availability
- 99.5% uptime target
- Automated backup (daily)
- Disaster recovery plan

### NFR-4: Scalability
- Horizontal scaling ready
- Database indexing optimization
- Caching strategy (Redis opsional)

### NFR-5: Usability
- Mobile-responsive design
- Intuitive UI/UX
- Minimal clicks untuk common tasks
- Keyboard shortcuts untuk POS

### NFR-6: Compatibility
- Support modern browsers (Chrome, Firefox, Safari, Edge)
- Support mobile devices (responsive)
- Printer compatibility (thermal printers)

## 5. Technical Constraints
- Single database deployment (MariaDB)
- PHP 8.1+ required
- Bootstrap 5.3+ for frontend
- No external dependencies yang expensive
- Must work offline dengan local server

## 6. User Stories Summary

| Epic | User Stories | Total Story Points |
|------|-------------|-------------------|
| Authentication & Authorization | 4 | 21 |
| Inventory Management | 10 | 55 |
| Point of Sale | 7 | 40 |
| Prescription Management | 5 | 35 |
| Customer Management | 4 | 20 |
| Reporting & Analytics | 6 | 40 |
| **Total** | **36** | **211** |

## 7. Acceptance Criteria

### System Level
- All functional requirements implemented
- All non-functional requirements met
- Security audit passed
- Performance benchmarks achieved
- User acceptance testing completed

### User Story Level
- Each user story has clear acceptance criteria
- Defined in sprint backlog
- Validated during sprint review

## 8. Dependencies

### External Dependencies
- PHP 8.1+
- MariaDB 10.6+
- Bootstrap 5.3+
- Firebase PHP-JWT
- Monolog

### Internal Dependencies
- Custom PHP framework
- Database schema
- Authentication system
- Inventory module

## 9. Risks

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Scope creep | Medium | High | Change control process |
| Technical debt | High | Medium | Regular refactoring |
| Performance issues | Medium | High | Load testing, optimization |
| Security vulnerabilities | Low | Critical | Security audits, best practices |

## 10. Success Criteria

- System is usable for target users
- All user stories completed
- Documentation is comprehensive
- System is maintainable and extensible
- Portfolio demonstrates technical expertise

---

**Document Status:** Approved  
**Next Phase:** System Design