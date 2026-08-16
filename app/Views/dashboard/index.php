<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MediCore — Pharmacy Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="bg-page">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation (Role-Aware) -->
        <aside id="appSidebar" class="app-sidebar">
            <div class="sidebar-brand-wrapper">
                <a href="/dashboard" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="icon-box-solid icon-box-teal" style="width: 34px; height: 34px; font-size: 0.95rem;">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <span class="fs-5 fw-bold text-dark" style="letter-spacing: -0.03em;">MediCore</span>
                </a>
                <button class="btn btn-sm btn-outline-dark d-lg-none py-0 px-2" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-nav-container">
                <div class="sidebar-section-label">Main Menu</div>
                <a href="/dashboard" class="sidebar-menu-link active">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>

                <!-- Clinical Prescriptions -->
                <div class="sidebar-section-label mt-2">Clinical Pharmacy</div>
                <a href="/prescriptions" class="sidebar-menu-link">
                    <i class="fas fa-file-medical"></i> <span>Prescription Queue</span>
                </a>

                <!-- Point of Sale (POS) -->
                <div class="sidebar-section-label mt-2">Point of Sale</div>
                <a href="/pos" class="sidebar-menu-link">
                    <i class="fas fa-cash-register"></i> <span>POS Register</span>
                </a>
                <a href="/pos/history" class="sidebar-menu-link">
                    <i class="fas fa-receipt"></i> <span>Sales History</span>
                </a>
                <!-- CRM & Patients -->
                <div class="sidebar-section-label mt-2">CRM & Patients</div>
                <a href="/crm/customers" class="sidebar-menu-link">
                    <i class="fas fa-hospital-user"></i> <span>Customer Directory</span>
                </a>


                <!-- Purchasing & PBF Management (Module 5) -->
                <?php if (in_array($role, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Purchasing & PBF</div>
                    <a href="/purchasing" class="sidebar-menu-link">
                        <i class="fas fa-file-invoice"></i> <span>Purchase Orders (SP)</span>
                    </a>
                    <a href="/purchasing/ap-ledger" class="sidebar-menu-link">
                        <i class="fas fa-book-medical"></i> <span>Accounts Payable (AP)</span>
                    </a>
                <?php endif; ?>

                <!-- Multi-Branch & Transfers (Module 7) -->
                <?php if (in_array($role, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Multi-Branch Transfers</div>
                    <a href="/transfers" class="sidebar-menu-link">
                        <i class="fas fa-truck-ramp-box"></i> <span>Stock Transfers</span>
                    </a>
                <?php endif; ?>

                <!-- Reports & Stock Opname (Module 6) -->
                <?php if (in_array($role, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Reports & Audit</div>
                    <a href="/reports" class="sidebar-menu-link">
                        <i class="fas fa-chart-line"></i> <span>Financial Reports</span>
                    </a>
                    <a href="/stock-opname" class="sidebar-menu-link">
                        <i class="fas fa-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                <?php endif; ?>

                <!-- Inventory & FEFO -->
                <?php if (in_array($role, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Inventory & FEFO</div>
                    <a href="/inventory/products" class="sidebar-menu-link">
                        <i class="fas fa-boxes"></i> <span>Medications</span>
                    </a>
                    <a href="/inventory/fefo" class="sidebar-menu-link">
                        <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                    </a>
                <?php endif; ?>

                <!-- Master Data -->
                <?php if (in_array($role, ['superadmin', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Master Data</div>
                    <a href="/inventory/categories" class="sidebar-menu-link">
                        <i class="fas fa-tags"></i> <span>Categories</span>
                    </a>
                    <a href="/inventory/suppliers" class="sidebar-menu-link">
                        <i class="fas fa-truck-loading"></i> <span>Suppliers (PBF)</span>
                    </a>
                <?php endif; ?>

                <!-- Account & Settings -->
                <div class="sidebar-section-label mt-2">Account</div>
                <a href="/profile" class="sidebar-menu-link">
                    <i class="fas fa-user-circle"></i> <span>My Profile</span>
                </a>
                <a href="/change-password" class="sidebar-menu-link">
                    <i class="fas fa-key"></i> <span>Security</span>
                </a>
            </div>

            <div class="sidebar-user-card d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 170px;">
                    <div class="icon-box-solid icon-box-teal" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="fw-bold small text-dark text-truncate"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></div>
                        <div class="text-muted font-mono" style="font-size: 0.68rem; text-transform: uppercase;">
                            <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?>
                        </div>
                    </div>
                </div>
                <form method="POST" action="/logout" class="m-0">
                    <button type="submit" class="btn btn-sm btn-outline-dark p-1 px-2" title="Sign Out">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Application Shell -->
        <div class="app-main">
            <!-- Slim Topbar (Clean on Mobile) -->
                        <header class="app-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="mobile-nav-toggle d-lg-none" type="button" onclick="toggleSidebar()" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-dark small fw-bold">Apotek Central Branch</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-dark btn-sm topbar-avatar-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Account Profile">
                            <div class="icon-box-solid icon-box-teal rounded-circle" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark small"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></div>
                                <div class="text-muted font-mono" style="font-size: 0.72rem;"><?= htmlspecialchars($user['email']) ?></div>
                                <div class="mt-1">
                                    <span class="badge-tag <?= $roleBadgeClass ?> text-uppercase font-mono" style="font-size: 0.65rem;">
                                        Role: <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?>
                                    </span>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-th-large text-muted"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="/pos"><i class="fas fa-cash-register text-muted"></i> POS Terminal</a></li>
                            <li><a class="dropdown-item" href="/inventory/products"><i class="fas fa-boxes text-muted"></i> Medications</a></li>
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-id-badge text-muted"></i> Profile Settings</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="/logout" class="m-0">
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="app-content">
                <!-- Welcome Banner (With Generous Mobile Padding) -->
                <div class="card-modern p-4 p-sm-4 p-md-5 mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box-solid <?= ($role === 'pharmacist' || $role === 'superadmin') ? 'icon-box-teal' : (($role === 'cashier') ? 'icon-box-blue' : 'icon-box-amber') ?> flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.35rem;">
                                <i class="<?= match($role) {
                                    'superadmin' => 'fas fa-shield-alt',
                                    'owner' => 'fas fa-chart-line',
                                    'pharmacist' => 'fas fa-clinic-medical',
                                    'cashier' => 'fas fa-cash-register',
                                    'warehouse' => 'fas fa-warehouse',
                                    default => 'fas fa-clinic-medical'
                                } ?>"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h1 class="h5 h4-md fw-bold text-dark mb-0">Welcome back, <?= htmlspecialchars($user['full_name'] ?? 'Staff') ?>!</h1>
                                    <span class="badge-tag badge-tag-emerald"><i class="fas fa-check-circle" style="font-size: 8px;"></i> Shift Active</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    Role: <strong class="text-dark text-capitalize"><?= htmlspecialchars($user['role_name'] ?? 'Staff') ?></strong> • 
                                    <?= match($role) {
                                        'superadmin' => 'System-wide administrative privileges and configuration control',
                                        'owner' => 'Executive business intelligence, revenue, and gross profit oversight',
                                        'pharmacist' => 'Clinical prescription verification & automated FEFO batch scheduling',
                                        'cashier' => 'Point of Sale register, barcode checkout, and shift drawer balancing',
                                        'warehouse' => 'Supply chain receiving, GRN matching, and batch lot arrangement',
                                        default => 'Apotek Central Management Portal'
                                    } ?>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto mt-2 mt-md-0">
                            <a href="/pos" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                                <i class="fas fa-cash-register me-1"></i> POS Register
                            </a>
                            <a href="/inventory/products" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                                <i class="fas fa-boxes me-1"></i> Catalog
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KPI Metric Counters -->
                <div class="row g-3 g-md-4 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="stat-label">Today's Sales</span>
                                    <div class="icon-box-solid icon-box-teal" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                </div>
                                <div class="stat-value text-dark mb-1"><?= $metrics['today_sales'] ?></div>
                            </div>
                            <div class="pt-2">
                                <span class="badge-tag badge-tag-emerald">
                                    <i class="fas fa-arrow-up"></i> <?= $metrics['today_orders'] ?> Orders Today
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="stat-label">Active SKUs</span>
                                    <div class="icon-box-solid icon-box-blue" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                </div>
                                <div class="stat-value text-dark mb-1"><?= $metrics['total_skus'] ?></div>
                            </div>
                            <div class="pt-2">
                                <span class="badge-tag badge-tag-blue">
                                    <i class="fas fa-boxes"></i> <?= $metrics['total_units'] ?> Units
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="stat-label">Low Stock Alert</span>
                                    <div class="icon-box-solid icon-box-amber" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div class="stat-value text-dark mb-1"><?= $metrics['low_stock_count'] ?> <span class="fs-6 text-muted fw-normal">Items</span></div>
                            </div>
                            <div class="pt-2">
                                <?php if ((int)$metrics['low_stock_count'] > 0): ?>
                                    <span class="badge-tag badge-tag-amber">
                                        <i class="fas fa-exclamation-triangle"></i> Reorder Required
                                    </span>
                                <?php else: ?>
                                    <span class="badge-tag badge-tag-emerald">
                                        <i class="fas fa-check"></i> Stock Safe
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="stat-label">FEFO Expiry Sentinel</span>
                                    <div class="icon-box-solid icon-box-emerald" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                                <div class="stat-value text-dark mb-1"><?= $metrics['expiring_soon_count'] ?> <span class="fs-6 text-muted fw-normal">Batches</span></div>
                            </div>
                            <div class="pt-2">
                                <?php if ((int)$metrics['expiring_soon_count'] > 0): ?>
                                    <span class="badge-tag badge-tag-crimson">
                                        <i class="fas fa-clock"></i> &lt; 30d Priority
                                    </span>
                                <?php else: ?>
                                    <span class="badge-tag badge-tag-emerald">
                                        <i class="fas fa-shield-alt"></i> Zero Expired Loss
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Charts Section (Bar Chart & Doughnut Chart) -->
                <div class="row g-3 g-md-4 mb-4">
                    <!-- Chart 1: 7-Day Revenue Trend (Bar & Line Combo) -->
                    <div class="col-12 col-xl-8">
                        <div class="card-modern p-3.5 p-sm-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                                <div>
                                    <h2 class="h6 fw-bold text-dark mb-0">7-Day Sales & Transaction Volume</h2>
                                    <div class="text-muted small">Daily revenue in thousands IDR (k) vs order volume</div>
                                </div>
                                <span class="badge-tag badge-tag-teal font-mono">Daily Trend</span>
                            </div>
                            <div style="position: relative; height: 260px; width: 100%;">
                                <canvas id="salesTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 2: Inventory Classification Distribution (Doughnut Chart) -->
                    <div class="col-12 col-xl-4">
                        <div class="card-modern p-3.5 p-sm-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                                <div>
                                    <h2 class="h6 fw-bold text-dark mb-0">Stock by Category</h2>
                                    <div class="text-muted small">Therapeutic classification split</div>
                                </div>
                                <span class="badge-tag badge-tag-blue font-mono">Inventory</span>
                            </div>
                            <div style="position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
                                <canvas id="categoryPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Workspaces -->
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <span class="badge-tag badge-tag-teal"><i class="fas fa-cubes me-1"></i> Dedicated Workspace</span>
                    <h2 class="h5 fw-bold text-dark mb-0">Operational Actions for <?= htmlspecialchars($user['role_name'] ?? 'Staff') ?></h2>
                </div>

                <div class="row g-3 g-md-4 mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-modern p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="icon-box-solid icon-box-teal">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <span class="badge-tag badge-tag-teal">Module 2</span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-1">Medication Catalog & Pricing</h3>
                                <p class="text-secondary small mb-3">
                                    Search drugs by barcode/SKU, manage retail prices, stock units, and bulk import medications via CSV/Excel.
                                </p>
                            </div>
                            <a href="/inventory/products" class="btn btn-outline-dark btn-sm w-100">
                                <i class="fas fa-pills me-1"></i> Open Catalog (<?= $metrics['total_skus'] ?>)
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-modern p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="icon-box-solid icon-box-amber">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <span class="badge-tag badge-tag-amber">FEFO Engine</span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-1">FEFO Expiry Sentinel Matrix</h3>
                                <p class="text-secondary small mb-3">
                                    Monitor batch lots prioritized for earliest dispatch (30, 60, and 90-day alert thresholds) to eliminate expiration waste.
                                </p>
                            </div>
                            <a href="/inventory/fefo" class="btn btn-outline-dark btn-sm w-100">
                                <i class="fas fa-clock me-1"></i> Review FEFO Matrix
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-modern p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="icon-box-solid icon-box-blue">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <span class="badge-tag badge-tag-blue">Module 3</span>
                                </div>
                                <h3 class="h6 fw-bold text-dark mb-1">Point of Sale (POS) Checkout</h3>
                                <p class="text-secondary small mb-3">
                                    High-throughput barcode scanning, split payment handling (Cash/QRIS), and thermal receipt printing with automated FEFO.
                                </p>
                            </div>
                            <a href="/pos" class="btn btn-outline-dark btn-sm w-100">
                                <i class="fas fa-cash-register me-1"></i> Open POS Register
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Live Inventory Status Widgets (FEFO & Low Stock) -->
                <div class="row g-3 g-md-4">
                    <div class="col-12 col-lg-6">
                        <div class="card-modern p-3 p-sm-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                                <div class="fw-bold text-dark small">
                                    <i class="fas fa-exclamation-circle text-danger me-1"></i> Critical Batches (&lt; 30 Days)
                                </div>
                                <a href="/inventory/fefo?days=30" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size: 0.75rem;">View All</a>
                            </div>

                            <?php if (empty($criticalBatches)): ?>
                                <div class="text-center py-4 text-muted small">
                                    <i class="fas fa-check-circle text-success fs-5 d-block mb-1"></i>
                                    No critical batches expiring within 30 days.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0 font-mono small">
                                        <thead>
                                            <tr class="text-muted" style="font-size: 0.72rem;">
                                                <th>BATCH</th>
                                                <th>MEDICATION</th>
                                                <th>QTY</th>
                                                <th>EXPIRY</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 0.78rem;">
                                            <?php foreach ($criticalBatches as $cb): ?>
                                                <tr>
                                                    <td><code><?= htmlspecialchars($cb['batch_number']) ?></code></td>
                                                    <td class="fw-bold font-sans text-truncate" style="max-width: 140px;"><?= htmlspecialchars($cb['product_name']) ?></td>
                                                    <td><?= $cb['current_quantity'] ?> <?= htmlspecialchars($cb['unit_symbol'] ?? '') ?></td>
                                                    <td><span class="badge-tag badge-tag-crimson"><?= $cb['days_until_expiry'] ?>d left</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card-modern p-3 p-sm-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                                <div class="fw-bold text-dark small">
                                    <i class="fas fa-boxes text-warning me-1"></i> Stock Reorder Warnings
                                </div>
                                <a href="/inventory/products?low_stock=1" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size: 0.75rem;">View All</a>
                            </div>

                            <?php if (empty($lowStockItems)): ?>
                                <div class="text-center py-4 text-muted small">
                                    <i class="fas fa-check-circle text-success fs-5 d-block mb-1"></i>
                                    All medication stock levels are above safety thresholds.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0 font-mono small">
                                        <thead>
                                            <tr class="text-muted" style="font-size: 0.72rem;">
                                                <th>SKU</th>
                                                <th>MEDICATION</th>
                                                <th>CURRENT</th>
                                                <th>MIN THRESHOLD</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 0.78rem;">
                                            <?php foreach ($lowStockItems as $lsi): ?>
                                                <tr>
                                                    <td><code><?= htmlspecialchars($lsi['sku']) ?></code></td>
                                                    <td class="fw-bold font-sans text-truncate" style="max-width: 140px;"><?= htmlspecialchars($lsi['name']) ?></td>
                                                    <td><span class="text-danger fw-bold"><?= $lsi['stock_quantity'] ?></span></td>
                                                    <td class="text-muted"><?= $lsi['min_stock'] ?> <?= htmlspecialchars($lsi['unit_symbol'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        }

        // Render Clean Zero-Gradient Clinical Charts
        const chartData = <?= json_encode($charts ?? []) ?>;

        // 1. Sales Trend Bar Chart
        if (document.getElementById('salesTrendChart') && chartData.revenue_dates) {
            new Chart(document.getElementById('salesTrendChart'), {
                type: 'bar',
                data: {
                    labels: chartData.revenue_dates,
                    datasets: [
                        {
                            label: 'Revenue (k IDR)',
                            data: chartData.revenue_values,
                            backgroundColor: '#0d9488',
                            borderRadius: 4,
                            barThickness: 24
                        },
                        {
                            label: 'Orders Count',
                            data: chartData.order_counts,
                            type: 'line',
                            borderColor: '#2563eb',
                            backgroundColor: '#2563eb',
                            borderWidth: 2,
                            pointRadius: 4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { family: 'inherit', size: 12 } } },
                        tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 6 }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { 
                            grid: { color: '#f1f5f9' },
                            ticks: { callback: v => 'Rp ' + v.toLocaleString() + 'k' }
                        },
                        y1: {
                            position: 'right',
                            grid: { display: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // 2. Category Doughnut Chart
        if (document.getElementById('categoryPieChart') && chartData.category_labels) {
            new Chart(document.getElementById('categoryPieChart'), {
                type: 'doughnut',
                data: {
                    labels: chartData.category_labels,
                    datasets: [{
                        data: chartData.category_values,
                        backgroundColor: chartData.category_colors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                        tooltip: { backgroundColor: '#0f172a', padding: 8, cornerRadius: 6 }
                    },
                    cutout: '65%'
                }
            });
        }
    </script>
</body>
</html>
