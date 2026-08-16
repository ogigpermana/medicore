<?php
    $currentRole = $user['role_name'] ?? 'pharmacist';
    $currentName = $user['full_name'] ?? 'Staff Member';
    $roleBadgeClass = match($currentRole) {
        'superadmin' => 'badge-tag-dark',
        'owner' => 'badge-tag-dark',
        'pharmacist' => 'badge-tag-teal',
        'cashier' => 'badge-tag-blue',
        'warehouse' => 'badge-tag-amber',
        default => 'badge-tag-teal'
    };

    $totalBuyVal = (float)$summary['total_asset_buy_value'];
    $totalSellVal = (float)$summary['total_potential_retail_value'];
    $lockedMargin = $totalSellVal - $totalBuyVal;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    
    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS & Custom MediCore Design System -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand-wrapper d-flex align-items-center justify-content-between">
                <div class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div class="brand-text">
                        <div class="brand-title">MediCore</div>
                        <div class="brand-subtitle">PHARMACY ERP</div>
                    </div>
                </div>
                <button class="btn btn-sm text-white d-lg-none" onclick="toggleSidebar()" aria-label="Close Sidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-nav-container">
                <div class="sidebar-section-label">Main Menu</div>
                <a href="/dashboard" class="sidebar-menu-link">
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
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Purchasing & PBF</div>
                    <a href="/purchasing" class="sidebar-menu-link">
                        <i class="fas fa-file-invoice"></i> <span>Purchase Orders (SP)</span>
                    </a>
                    <a href="/purchasing/ap-ledger" class="sidebar-menu-link">
                        <i class="fas fa-book-medical"></i> <span>Accounts Payable (AP)</span>
                    </a>
                <?php endif; ?>

                <!-- Multi-Branch & Transfers (Module 7) -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Multi-Branch Transfers</div>
                    <a href="/transfers" class="sidebar-menu-link">
                        <i class="fas fa-truck-ramp-box"></i> <span>Stock Transfers</span>
                    </a>
                <?php endif; ?>

                <!-- Reports & Stock Opname (Module 6) -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Reports & Audit</div>
                    <a href="/reports" class="sidebar-menu-link active">
                        <i class="fas fa-chart-line"></i> <span>Financial Reports</span>
                    </a>
                    <a href="/stock-opname" class="sidebar-menu-link">
                        <i class="fas fa-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                <?php endif; ?>

                <!-- Inventory & FEFO -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Inventory & FEFO</div>
                    <a href="/inventory/products" class="sidebar-menu-link">
                        <i class="fas fa-boxes"></i> <span>Medications</span>
                    </a>
                    <a href="/inventory/fefo" class="sidebar-menu-link">
                        <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                    </a>
                    <a href="/inventory/categories" class="sidebar-menu-link">
                        <i class="fas fa-tags"></i> <span>Categories</span>
                    </a>
                    <a href="/inventory/suppliers" class="sidebar-menu-link">
                        <i class="fas fa-truck-loading"></i> <span>Suppliers (PBF)</span>
                    </a>
                <?php endif; ?>

                <!-- Settings -->
                <div class="sidebar-section-label mt-2">Account</div>
                <a href="/profile" class="sidebar-menu-link">
                    <i class="fas fa-id-badge"></i> <span>My Profile</span>
                </a>
            </div>

            <div class="sidebar-user-card d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 170px;">
                    <div class="icon-box-solid icon-box-teal" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="text-truncate">
                        <div class="fw-bold small text-dark text-truncate"><?= htmlspecialchars($currentName) ?></div>
                        <div class="text-muted font-mono" style="font-size: 0.68rem; text-transform: uppercase;">
                            <?= htmlspecialchars($currentRole) ?>
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

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Slim Topbar (Clean & Responsive on Mobile) -->
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
                                <div class="fw-bold text-dark small"><?= htmlspecialchars($currentName) ?></div>
                                <div class="text-muted font-mono" style="font-size: 0.72rem;"><?= htmlspecialchars($user['email']) ?></div>
                                <div class="mt-1">
                                    <span class="badge-tag <?= $roleBadgeClass ?> text-uppercase font-mono" style="font-size: 0.65rem;">
                                        Role: <?= htmlspecialchars($currentRole) ?>
                                    </span>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-th-large text-muted"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="/pos"><i class="fas fa-cash-register text-muted"></i> POS Register</a></li>
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

            <main class="app-content">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal">Asset Valuation</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Laporan Valuasi Nilai Aset Stok FEFO</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Real-time valuation of pharmaceutical physical stock assets based on procurement cost and retail price.
                        </p>
                    </div>

                    <a href="/inventory/fefo" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-calendar-alt me-1"></i> FEFO Sentinel
                    </a>
                </div>

                <!-- KPI Metric Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Asset Buy Value -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Buy Value (Asset Base)</span>
                                <div class="icon-box-solid icon-box-dark" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-dark font-mono">
                                Rp <?= number_format($totalBuyVal, 0, ',', '.') ?>
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                <?= number_format($summary['total_products']) ?> active SKU catalogs
                            </div>
                        </div>
                    </div>

                    <!-- Potential Retail Value -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Potential Retail Sales Value</span>
                                <div class="icon-box-solid icon-box-teal" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-teal font-mono">
                                Rp <?= number_format($totalSellVal, 0, ',', '.') ?>
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                Full sell out valuation
                            </div>
                        </div>
                    </div>

                    <!-- Locked Potential Profit -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Estimated Unrealized Margin</span>
                                <div class="icon-box-solid icon-box-blue" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-blue font-mono">
                                Rp <?= number_format($lockedMargin, 0, ',', '.') ?>
                            </div>
                            <div class="text-blue small font-mono mt-1" style="font-size: 0.72rem;">
                                Margin: <strong><?= $totalBuyVal > 0 ? number_format(($lockedMargin / $totalBuyVal) * 100, 1) : 0 ?>%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Total Physical Units -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Physical Units</span>
                                <div class="icon-box-solid icon-box-amber" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-dark font-mono">
                                <?= number_format($summary['total_units_in_stock']) ?> Pcs / Strip
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                Physical stock across pharmacy
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown Table -->
                <div class="card-modern p-3 p-sm-4 mb-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-layer-group text-teal me-1"></i> Stock Valuation by Category</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>CATEGORY</th>
                                    <th class="text-center">PRODUCTS</th>
                                    <th class="text-center">PHYSICAL STOCK</th>
                                    <th class="text-end">BUY COST VALUE (RP)</th>
                                    <th class="text-end">RETAIL VALUE (RP)</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php foreach ($categoryBreakdown as $cat): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($cat['category_name']) ?></div>
                                        </td>
                                        <td class="text-center font-sans"><?= $cat['product_count'] ?></td>
                                        <td class="text-center"><strong><?= number_format($cat['stock_qty']) ?></strong></td>
                                        <td class="text-end fw-bold text-dark">Rp <?= number_format($cat['category_buy_value'], 0, ',', '.') ?></td>
                                        <td class="text-end text-teal fw-bold">Rp <?= number_format($cat['category_sell_value'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- High-Value Catalog Valuation Table -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-box-archive text-teal me-1"></i> Asset Valuation by Medication SKU</div>
                        <span class="text-muted small"><?= count($products) ?> items</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>MEDICATION & SKU</th>
                                    <th>CATEGORY</th>
                                    <th class="text-center">STOCK</th>
                                    <th>BUY PRICE</th>
                                    <th class="text-end">TOTAL ASSET COST (RP)</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($p['name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($p['sku']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge-tag badge-tag-teal"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></span>
                                        </td>
                                        <td class="text-center">
                                            <strong><?= $p['stock_quantity'] ?></strong> <?= htmlspecialchars($p['unit_symbol'] ?? '') ?>
                                        </td>
                                        <td>Rp <?= number_format($p['buy_price'], 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-dark">
                                            Rp <?= number_format($p['total_buy_value'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
    </script>
</body>
</html>
