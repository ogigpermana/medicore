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
                    <a href="/transfers" class="sidebar-menu-link active">
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
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-truck-ramp-box me-1"></i> Module 7</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Multi-Branch & Inter-Warehouse Stock Transfers</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Manage medication movements across central warehouse and pharmacy branches with official delivery notes.
                        </p>
                    </div>

                    <a href="/transfers/create" class="btn btn-primary btn-sm w-100 w-md-auto">
                        <i class="fas fa-plus-circle me-1"></i> New Transfer Request
                    </a>
                </div>

                <!-- Status Filter Navigation -->
                <div class="rx-filter-nav">
                    <a href="/transfers?status=all" class="btn btn-sm <?= ($currentStatus === 'all') ? 'btn-dark' : 'btn-outline-dark' ?> text-nowrap">
                        All Transfers (<?= count($transfers) ?>)
                    </a>
                    <a href="/transfers?status=in_transit" class="btn btn-sm <?= ($currentStatus === 'in_transit') ? 'btn-primary' : 'btn-outline-primary' ?> text-nowrap">
                        <i class="fas fa-truck fa-bounce me-1"></i> In Transit
                    </a>
                    <a href="/transfers?status=pending_approval" class="btn btn-sm <?= ($currentStatus === 'pending_approval') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' ?> text-nowrap">
                        <i class="fas fa-clock me-1"></i> Pending Approval
                    </a>
                    <a href="/transfers?status=received" class="btn btn-sm <?= ($currentStatus === 'received') ? 'btn-success' : 'btn-outline-success' ?> text-nowrap">
                        <i class="fas fa-check-circle me-1"></i> Received
                    </a>
                </div>

                <!-- Transfers List Card -->
                <div class="card-modern p-3 p-sm-4">
                    <?php if (empty($transfers)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-truck-moving fs-3 d-block mb-2 text-secondary"></i>
                            No stock transfers found for this filter.
                        </div>
                    <?php else: ?>
                        <!-- Mobile Cards (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($transfers as $trf): ?>
                                <?php 
                                    $stBadge = match($trf['status']) {
                                        'received' => 'badge-tag-emerald',
                                        'in_transit' => 'badge-tag-blue',
                                        'pending_approval' => 'badge-tag-amber',
                                        'cancelled', 'rejected' => 'badge-tag-crimson',
                                        default => 'badge-tag-secondary'
                                    };
                                ?>
                                <div class="rx-mobile-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <a href="/transfers/<?= $trf['id'] ?>" class="fw-bold text-dark font-mono text-decoration-none">
                                                <?= htmlspecialchars($trf['transfer_number']) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.68rem;">
                                                <?= date('d M Y H:i', strtotime($trf['created_at'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge-tag <?= $stBadge ?> text-uppercase font-mono" style="font-size: 0.68rem;">
                                            <?= htmlspecialchars(str_replace('_', ' ', $trf['status'])) ?>
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded border">
                                        <div class="text-truncate flex-fill">
                                            <div class="text-muted" style="font-size: 0.65rem;">SOURCE:</div>
                                            <div class="fw-bold text-dark text-truncate small"><?= htmlspecialchars($trf['source_branch_name']) ?></div>
                                        </div>
                                        <i class="fas fa-arrow-right text-teal"></i>
                                        <div class="text-truncate flex-fill text-end">
                                            <div class="text-muted" style="font-size: 0.65rem;">DESTINATION:</div>
                                            <div class="fw-bold text-dark text-truncate small"><?= htmlspecialchars($trf['destination_branch_name']) ?></div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                            <div class="text-muted" style="font-size: 0.68rem;">Items Sent:</div>
                                            <div class="fw-bold font-mono text-dark"><?= $trf['total_items'] ?> SKU (<?= $trf['total_qty_sent'] ?> units)</div>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <a href="/transfers/<?= $trf['id'] ?>/print-surat-jalan" target="_blank" class="btn btn-sm btn-outline-dark px-2 py-1" title="Print Delivery Note">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="/transfers/<?= $trf['id'] ?>" class="btn btn-sm btn-primary px-3 py-1">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Desktop Table (>= 768px) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>TRANSFER #</th>
                                        <th>SOURCE BRANCH</th>
                                        <th>DESTINATION</th>
                                        <th class="text-center">ITEMS</th>
                                        <th class="text-center">QTY SENT</th>
                                        <th>LOGISTICS / DRIVER</th>
                                        <th>STATUS</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($transfers as $trf): ?>
                                        <?php 
                                            $stBadge = match($trf['status']) {
                                                'received' => 'badge-tag-emerald',
                                                'in_transit' => 'badge-tag-blue',
                                                'pending_approval' => 'badge-tag-amber',
                                                'cancelled', 'rejected' => 'badge-tag-crimson',
                                                default => 'badge-tag-secondary'
                                            };
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="/transfers/<?= $trf['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($trf['transfer_number']) ?>
                                                </a>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;">
                                                    <?= date('d M Y H:i', strtotime($trf['created_at'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($trf['source_branch_name']) ?></div>
                                                <span class="badge-tag badge-tag-dark" style="font-size: 0.65rem;"><?= htmlspecialchars($trf['source_branch_code']) ?></span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($trf['destination_branch_name']) ?></div>
                                                <span class="badge-tag badge-tag-teal" style="font-size: 0.65rem;"><?= htmlspecialchars($trf['destination_branch_code']) ?></span>
                                            </td>
                                            <td class="text-center font-bold"><?= $trf['total_items'] ?> SKU</td>
                                            <td class="text-center font-bold text-dark"><?= number_format($trf['total_qty_sent']) ?></td>
                                            <td>
                                                <?php if ($trf['driver_name']): ?>
                                                    <div class="font-sans fw-semibold text-dark"><?= htmlspecialchars($trf['driver_name']) ?></div>
                                                    <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($trf['vehicle_number'] ?? '-') ?></div>
                                                <?php else: ?>
                                                    <span class="text-muted font-sans">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $stBadge ?> text-uppercase">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $trf['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="/transfers/<?= $trf['id'] ?>/print-surat-jalan" target="_blank" class="btn btn-sm btn-outline-dark py-1 px-2" title="Print Delivery Note">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <a href="/transfers/<?= $trf['id'] ?>" class="btn btn-sm btn-primary py-1 px-2.5">
                                                        <i class="fas fa-eye me-1"></i> Details
                                                    </a>
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
