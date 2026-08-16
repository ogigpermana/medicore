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

    <style>
        @media print {
            .app-sidebar, .app-topbar, .no-print, .sidebar-backdrop {
                display: none !important;
            }
            .app-main {
                margin: 0 !important;
                padding: 0 !important;
            }
            .app-content {
                padding: 0 !important;
            }
            .card-modern {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
            }
        }
    </style>
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
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 no-print">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal">P&L Statement</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Pharmacy Profit & Loss Statement</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Period: <strong><?= date('d M Y', strtotime($startDate)) ?></strong> to <strong><?= date('d M Y', strtotime($endDate)) ?></strong>
                        </p>
                    </div>

                    <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                        <form method="GET" action="/reports/profit-loss" class="d-flex align-items-center gap-1 flex-fill flex-md-grow-0">
                            <input type="date" name="start_date" class="form-control form-control-sm font-mono" value="<?= htmlspecialchars($startDate) ?>">
                            <span class="text-muted small">-</span>
                            <input type="date" name="end_date" class="form-control form-control-sm font-mono" value="<?= htmlspecialchars($endDate) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-dark">Filter</button>
                        </form>
                        <button onclick="window.print()" class="btn btn-sm btn-primary">
                            <i class="fas fa-print me-1"></i> Print Statement
                        </button>
                    </div>
                </div>

                <!-- Printable Profit & Loss Statement Card -->
                <div class="card-modern p-4 p-md-5">
                    <!-- Pharmacy Statement Header -->
                    <div class="text-center pb-4 mb-4 border-bottom">
                        <h4 class="fw-bold text-dark text-uppercase mb-1 tracking-wide">MEDICORE PHARMACY</h4>
                        <div class="text-muted small">STATEMENT OF PROFIT AND LOSS (OPERATING FINANCIAL STATEMENT)</div>
                        <div class="font-mono text-dark small fw-semibold mt-1">
                            Period: <?= date('d F Y', strtotime($startDate)) ?> – <?= date('d F Y', strtotime($endDate)) ?>
                        </div>
                    </div>

                    <!-- Ledger Breakdown Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0 font-mono small">
                            <tbody>
                                <!-- Section 1: Revenue -->
                                <tr class="border-bottom bg-light">
                                    <th colspan="2" class="text-dark fw-bold py-2.5 font-sans" style="font-size: 0.88rem;">
                                        <i class="fas fa-arrow-trend-up text-teal me-2"></i> 1. OPERATING REVENUE
                                    </th>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-secondary">OTC & Prescription Medication Sales (POS Gross Sales) [<?= $pl['transaction_count'] ?> transactions]</td>
                                    <td class="text-end fw-semibold text-dark">Rp <?= number_format($pl['gross_sales'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-secondary">Compounding Fee & Tuslah Revenue [<?= $pl['prescription_count'] ?> prescriptions]</td>
                                    <td class="text-end fw-semibold text-dark">Rp <?= number_format($pl['total_tuslah'] + $pl['total_embalase'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-muted">Discounts & Register Deductions</td>
                                    <td class="text-end text-danger">- Rp <?= number_format($pl['total_discounts'], 0, ',', '.') ?></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="ps-3 fw-bold text-dark font-sans">TOTAL NET SALES REVENUE</td>
                                    <td class="text-end fw-bold text-dark fs-6">Rp <?= number_format($pl['net_sales'], 0, ',', '.') ?></td>
                                </tr>

                                <tr><td colspan="2" class="py-2"></td></tr>

                                <!-- Section 2: COGS / HPP -->
                                <tr class="border-bottom bg-light">
                                    <th colspan="2" class="text-dark fw-bold py-2.5 font-sans" style="font-size: 0.88rem;">
                                        <i class="fas fa-boxes text-danger me-2"></i> 2. COST OF GOODS SOLD (COGS / HPP)
                                    </th>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-secondary">Cost of Medication Inventory Sold (Buy Price Base)</td>
                                    <td class="text-end fw-semibold text-danger">Rp <?= number_format($pl['total_cogs'], 0, ',', '.') ?></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="ps-3 fw-bold text-dark font-sans">TOTAL COST OF GOODS SOLD (COGS)</td>
                                    <td class="text-end fw-bold text-danger fs-6">- Rp <?= number_format($pl['total_cogs'], 0, ',', '.') ?></td>
                                </tr>

                                <tr><td colspan="2" class="py-2"></td></tr>

                                <!-- Section 3: Gross Profit -->
                                <tr class="border-top border-bottom bg-teal text-white">
                                    <td class="py-3 ps-3 fw-bold fs-6 font-sans">
                                        <i class="fas fa-hand-holding-dollar me-2"></i> GROSS OPERATING PROFIT
                                    </td>
                                    <td class="text-end py-3 pe-3 fw-bold fs-5 font-mono">
                                        Rp <?= number_format($pl['gross_profit'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-3 text-muted pt-2" style="font-size: 0.78rem;">
                                        Gross Profit Margin (vs Net Sales):
                                    </td>
                                    <td class="text-end text-teal fw-bold pt-2 fs-6">
                                        <?= number_format($pl['profit_margin'], 2) ?> %
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Signatures footer for printed statement -->
                    <div class="row justify-content-between mt-5 pt-4 border-top">
                        <div class="col-4 text-center">
                            <div class="small text-muted mb-4">Prepared by (Finance / Staff):</div>
                            <div class="fw-bold small text-dark mt-4">( <?= htmlspecialchars($currentName) ?> )</div>
                            <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase;"><?= htmlspecialchars($currentRole) ?></div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="small text-muted mb-4">Approved by (Supervising Pharmacist / APJ):</div>
                            <div class="fw-bold small text-dark mt-4">( apt. MediCore APJ, S.Farm. )</div>
                            <div class="text-muted" style="font-size: 0.72rem;">SIPA: 19880415/SIPA_32.73/2022/2001</div>
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
    </script>
</body>
</html>
