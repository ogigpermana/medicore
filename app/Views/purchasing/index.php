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
</head>
<body class="bg-page">

    <!-- Mobile Drawer Backdrop -->
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

            <!-- Page Content -->
            <main class="app-content">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-truck-moving me-1"></i> Module 5</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Purchasing & PBF Purchase Orders (SP)</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Issue official BPOM Surat Pesanan (Reguler, Prekursor, OOT), track PBF shipments, and verify incoming inventory batches.
                        </p>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto">
                        <a href="/purchasing/ap-ledger" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                            <i class="fas fa-book-medical me-1"></i> Accounts Payable
                        </a>
                        <a href="/purchasing/create" class="btn btn-primary btn-sm flex-fill flex-md-grow-0">
                            <i class="fas fa-plus-circle me-1"></i> New Order (SP)
                        </a>
                    </div>
                </div>

                <!-- Status Filter Navigation Bar (Touch-Scrollable) -->
                <div class="rx-filter-nav">
                    <a href="/purchasing?status=all" class="btn btn-sm <?= ($currentStatus === 'all') ? 'btn-dark' : 'btn-outline-dark' ?> text-nowrap">
                        All Orders (<?= $statusCounts['all'] ?>)
                    </a>
                    <a href="/purchasing?status=ordered" class="btn btn-sm <?= ($currentStatus === 'ordered') ? 'btn-primary' : 'btn-outline-primary' ?> text-nowrap">
                        <i class="fas fa-paper-plane me-1"></i> Ordered (<?= $statusCounts['ordered'] ?>)
                    </a>
                    <a href="/purchasing?status=partial_received" class="btn btn-sm <?= ($currentStatus === 'partial_received') ? 'btn-warning text-dark' : 'btn-outline-warning' ?> text-nowrap">
                        <i class="fas fa-hourglass-half me-1"></i> Partial (<?= $statusCounts['partial_received'] ?>)
                    </a>
                    <a href="/purchasing?status=received" class="btn btn-sm <?= ($currentStatus === 'received') ? 'btn-success' : 'btn-outline-success' ?> text-nowrap">
                        <i class="fas fa-check-double me-1"></i> Received (<?= $statusCounts['received'] ?>)
                    </a>
                    <a href="/purchasing?status=draft" class="btn btn-sm <?= ($currentStatus === 'draft') ? 'btn-secondary' : 'btn-outline-secondary' ?> text-nowrap">
                        <i class="fas fa-file-edit me-1"></i> Draft (<?= $statusCounts['draft'] ?>)
                    </a>
                </div>

                <!-- PO Orders Container -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Showing <strong class="text-teal"><?= count($orders) ?></strong> purchase orders
                        </div>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-boxes-packing fs-3 d-block mb-2 text-secondary"></i>
                            No purchase orders found in this status filter.
                        </div>
                    <?php else: ?>
                        <!-- 1. Mobile Card List (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($orders as $po): ?>
                                <?php 
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
                                <div class="rx-mobile-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <a href="/purchasing/<?= $po['id'] ?>" class="fw-bold text-dark font-mono text-decoration-none">
                                                <?= htmlspecialchars($po['po_number']) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.68rem;">
                                                <?= date('d M Y', strtotime($po['order_date'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge-tag <?= $statusBadge ?> text-uppercase font-mono" style="font-size: 0.68rem;">
                                            <?= htmlspecialchars(str_replace('_', ' ', $po['status'])) ?>
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?= htmlspecialchars($po['supplier_name']) ?></div>
                                        <div class="d-flex align-items-center gap-1.5 mt-1">
                                            <span class="badge-tag <?= $spBadge ?> text-uppercase font-mono" style="font-size: 0.65rem;">
                                                SP <?= strtoupper($po['sp_type']) ?>
                                            </span>
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                • <?= $po['item_count'] ?> items
                                            </span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                            <div class="text-muted" style="font-size: 0.68rem;">Grand Total</div>
                                            <div class="fw-bold text-teal font-mono">Rp <?= number_format($po['grand_total'], 0, ',', '.') ?></div>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <a href="/purchasing/<?= $po['id'] ?>" class="btn btn-sm btn-outline-dark px-2.5 py-1" style="font-size: 0.78rem;">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="/purchasing/<?= $po['id'] ?>/print-sp" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" title="Print Official BPOM Surat Pesanan">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <?php if ($po['status'] !== 'received' && $po['status'] !== 'cancelled'): ?>
                                                <a href="/purchasing/<?= $po['id'] ?>/receive" class="btn btn-sm btn-primary px-2 py-1" title="Receive Goods (GRN)">
                                                    <i class="fas fa-truck-ramp-box"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 2. Desktop High-Density Table (>= 768px) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>PO / SP NUMBER</th>
                                        <th>SP TYPE</th>
                                        <th>DISTRIBUTOR (PBF)</th>
                                        <th>ORDER DATE</th>
                                        <th>PAYMENT TERMS</th>
                                        <th>STATUS</th>
                                        <th>TOTAL AMOUNT</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($orders as $po): ?>
                                        <?php 
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
                                        <tr>
                                            <td>
                                                <a href="/purchasing/<?= $po['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($po['po_number']) ?>
                                                </a>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;">
                                                    Created by <?= htmlspecialchars($po['created_by_name']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $spBadge ?> text-uppercase">
                                                    SP <?= htmlspecialchars($po['sp_type']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($po['supplier_name']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.72rem;"><?= htmlspecialchars($po['contact_person'] ?? '-') ?></div>
                                            </td>
                                            <td>
                                                <div class="font-sans"><?= date('d M Y', strtotime($po['order_date'])) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge-tag badge-tag-dark text-uppercase"><?= htmlspecialchars($po['payment_terms']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $statusBadge ?> text-uppercase">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $po['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-teal">Rp <?= number_format($po['grand_total'], 0, ',', '.') ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;"><?= $po['item_count'] ?> line items</div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="/purchasing/<?= $po['id'] ?>" class="btn btn-sm btn-outline-dark py-1 px-2" title="View PO Details">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                    <a href="/purchasing/<?= $po['id'] ?>/print-sp" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Print Official BPOM Surat Pesanan">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <?php if ($po['status'] !== 'received' && $po['status'] !== 'cancelled'): ?>
                                                        <a href="/purchasing/<?= $po['id'] ?>/receive" class="btn btn-sm btn-primary py-1 px-2" title="Receive Goods (GRN)">
                                                            <i class="fas fa-truck-ramp-box"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
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
