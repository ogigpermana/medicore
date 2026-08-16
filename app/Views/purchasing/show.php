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

    $spBadge = match($po['sp_type']) {
        'precursor' => 'badge-tag-amber',
        'oot' => 'badge-tag-crimson',
        'narcotic_psychotropic' => 'badge-tag-dark',
        default => 'badge-tag-blue'
    };

    $statusBadge = match($po['status']) {
        'draft' => 'badge-tag-secondary',
        'ordered' => 'badge-tag-blue',
        'partial_received' => 'badge-tag-amber',
        'received' => 'badge-tag-emerald',
        'cancelled' => 'badge-tag-crimson',
        default => 'badge-tag-dark'
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
                    <a href="/purchasing" class="sidebar-menu-link active">
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
                    <a href="/reports" class="sidebar-menu-link">
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
                <!-- Header Title Bar -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag <?= $spBadge ?> text-uppercase font-mono">
                                SP <?= htmlspecialchars($po['sp_type']) ?>
                            </span>
                            <span class="badge-tag <?= $statusBadge ?> text-uppercase font-mono">
                                <?= htmlspecialchars(str_replace('_', ' ', $po['status'])) ?>
                            </span>
                            <h1 class="h4 fw-bold text-dark mb-0 font-mono"><?= htmlspecialchars($po['po_number']) ?></h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Issued on <?= date('d F Y', strtotime($po['order_date'])) ?> by <?= htmlspecialchars($po['created_by_name']) ?>
                        </p>
                    </div>

                    <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                        <a href="/purchasing/<?= $po['id'] ?>/print-sp" target="_blank" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                            <i class="fas fa-print me-1"></i> Print Official SP
                        </a>
                        <?php if ($po['status'] !== 'received' && $po['status'] !== 'cancelled'): ?>
                            <a href="/purchasing/<?= $po['id'] ?>/receive" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                                <i class="fas fa-truck-ramp-box me-1"></i> Receive Goods (GRN)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Cards Grid -->
                <div class="row g-3 g-md-4 mb-4">
                    <!-- PBF Supplier Info Card -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-modern p-3 p-sm-4 h-100">
                            <div class="fw-bold text-dark small pb-2 mb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="fas fa-building text-teal"></i> Distributor (PBF)
                            </div>
                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($po['supplier_name']) ?></div>
                            <div class="text-muted font-mono small mb-2"><?= htmlspecialchars($po['supplier_code'] ?? 'PBF') ?></div>
                            <div class="small text-secondary mb-1">
                                <i class="fas fa-user-tie text-muted me-1"></i> CP: <?= htmlspecialchars($po['contact_person'] ?? '-') ?>
                            </div>
                            <div class="small text-secondary mb-1">
                                <i class="fas fa-phone text-muted me-1"></i> <?= htmlspecialchars($po['supplier_phone'] ?? '-') ?>
                            </div>
                            <div class="small text-secondary">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i> <?= htmlspecialchars($po['supplier_address'] ?? 'Bandung, Indonesia') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Schedule & Payment Terms -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-modern p-3 p-sm-4 h-100">
                            <div class="fw-bold text-dark small pb-2 mb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-check text-primary"></i> Procurement Terms
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-1.5">
                                <span>Order Date:</span>
                                <strong class="text-dark font-mono"><?= date('d M Y', strtotime($po['order_date'])) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-1.5">
                                <span>Expected Delivery:</span>
                                <strong class="text-dark font-mono"><?= $po['expected_delivery_date'] ? date('d M Y', strtotime($po['expected_delivery_date'])) : '-' ?></strong>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-1.5">
                                <span>Payment Terms:</span>
                                <span class="badge-tag badge-tag-dark text-uppercase"><?= htmlspecialchars($po['payment_terms']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary">
                                <span>Created By:</span>
                                <strong class="text-dark"><?= htmlspecialchars($po['created_by_name']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Pharmacist Regulatory Compliance Card -->
                    <div class="col-12 col-md-12 col-lg-4">
                        <div class="card-modern p-3 p-sm-4 h-100 bg-light border">
                            <div class="fw-bold text-dark small pb-2 mb-2 border-bottom d-flex align-items-center gap-2">
                                <i class="fas fa-stamp text-amber"></i> BPOM License & Compliance
                            </div>
                            <div class="small text-secondary mb-1">Apoteker Penanggung Jawab:</div>
                            <div class="fw-bold text-dark small">apt. MediCore Head Pharmacist, S.Farm.</div>
                            <div class="font-mono text-teal small fw-bold mb-2"><?= htmlspecialchars($po['pharmacist_sipa'] ?? 'SIPA Active') ?></div>
                            <div class="p-2 bg-white rounded border small text-muted" style="font-size: 0.75rem;">
                                Official BPOM SP generated in accordance with standard pharmaceutical regulation.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ordered Items Breakdown Table -->
                <div class="card-modern p-3 p-sm-4 mb-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div><i class="fas fa-boxes text-teal me-1"></i> Ordered Items Matrix</div>
                        <span class="text-muted small"><?= count($po['items']) ?> items</span>
                    </div>

                    <!-- Mobile Cards (< 768px) -->
                    <div class="d-md-none">
                        <?php foreach ($po['items'] as $it): ?>
                            <div class="p-2.5 bg-light rounded border mb-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($it['product_name']) ?></div>
                                        <div class="text-muted font-mono" style="font-size: 0.68rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-teal font-mono" style="font-size: 0.85rem;">Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></div>
                                        <div class="text-muted font-mono" style="font-size: 0.68rem;">Rp <?= number_format($it['unit_price'], 0, ',', '.') ?> / unit</div>
                                    </div>
                                </div>
                                <div class="pt-1.5 border-top d-flex justify-content-between align-items-center small">
                                    <span>Ordered: <strong><?= $it['quantity_ordered'] ?></strong> <?= htmlspecialchars($it['unit_symbol'] ?? '') ?></span>
                                    <span>Received: <strong class="text-success"><?= $it['quantity_received'] ?></strong></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Desktop Table (>= 768px) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>MEDICATION ITEM & SKU</th>
                                    <th>BUY PRICE</th>
                                    <th>ORDERED QTY</th>
                                    <th>RECEIVED QTY</th>
                                    <th>DISC %</th>
                                    <th>SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php foreach ($po['items'] as $it): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                        </td>
                                        <td>Rp <?= number_format($it['unit_price'], 0, ',', '.') ?></td>
                                        <td><strong><?= $it['quantity_ordered'] ?></strong> <?= htmlspecialchars($it['unit_symbol'] ?? '') ?></td>
                                        <td>
                                            <span class="badge-tag <?= ($it['quantity_received'] >= $it['quantity_ordered']) ? 'badge-tag-emerald' : ($it['quantity_received'] > 0 ? 'badge-tag-amber' : 'badge-tag-secondary') ?>">
                                                <?= $it['quantity_received'] ?> / <?= $it['quantity_ordered'] ?>
                                            </span>
                                        </td>
                                        <td><?= $it['discount_percent'] ?>%</td>
                                        <td><strong class="text-teal">Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Totals Box -->
                    <div class="row justify-content-end mt-4">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="p-3 bg-light rounded border font-mono small">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>Subtotal:</span>
                                    <span class="text-dark fw-bold">Rp <?= number_format($po['subtotal'], 0, ',', '.') ?></span>
                                </div>
                                <?php if ($po['discount_amount'] > 0): ?>
                                    <div class="d-flex justify-content-between text-danger mb-1">
                                        <span>Discount:</span>
                                        <span>- Rp <?= number_format($po['discount_amount'], 0, ',', '.') ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>PPN (11% Tax):</span>
                                    <span class="text-dark fw-bold">Rp <?= number_format($po['tax_amount'], 0, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-6">
                                    <span class="fw-bold text-dark font-sans">Grand Total:</span>
                                    <strong class="text-teal">Rp <?= number_format($po['grand_total'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Goods Receipts (GRN) History -->
                <div class="card-modern p-3 p-sm-4 mb-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div><i class="fas fa-truck-ramp-box text-teal me-1"></i> Goods Receipts (GRN) & Invoices</div>
                        <?php if ($po['status'] !== 'received' && $po['status'] !== 'cancelled'): ?>
                            <a href="/purchasing/<?= $po['id'] ?>/receive" class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-plus me-1"></i> Log New Receiving
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($po['receipts'])): ?>
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-box-open fs-4 d-block mb-1 text-secondary"></i>
                            No shipments received yet for this purchase order.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>GRN NUMBER</th>
                                        <th>PBF INVOICE NO</th>
                                        <th>RECEIVE DATE</th>
                                        <th>DUE DATE</th>
                                        <th>INVOICE AMOUNT</th>
                                        <th>PAYMENT STATUS</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($po['receipts'] as $gr): ?>
                                        <?php 
                                            $pBadge = match($gr['payment_status']) {
                                                'paid' => 'badge-tag-emerald',
                                                'partial' => 'badge-tag-amber',
                                                default => 'badge-tag-crimson'
                                            };
                                        ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($gr['grn_number']) ?></strong></td>
                                            <td><?= htmlspecialchars($gr['invoice_number']) ?></td>
                                            <td><?= date('d M Y', strtotime($gr['invoice_date'])) ?></td>
                                            <td><?= date('d M Y', strtotime($gr['due_date'])) ?></td>
                                            <td><strong class="text-teal">Rp <?= number_format($gr['total_amount'], 0, ',', '.') ?></strong></td>
                                            <td>
                                                <span class="badge-tag <?= $pBadge ?> text-uppercase">
                                                    <?= htmlspecialchars($gr['payment_status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
