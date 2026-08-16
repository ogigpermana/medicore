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
                    <a href="/stock-opname" class="sidebar-menu-link active">
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
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-clipboard-check me-1"></i> Physical Audit</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Stock Opname & Inventory Reconciliation</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Perform physical stock count audits, track damaged or lost units, and execute supervisor-approved inventory adjustments.
                        </p>
                    </div>

                    <a href="/stock-opname/create" class="btn btn-primary btn-sm w-100 w-md-auto">
                        <i class="fas fa-plus-circle me-1"></i> Start New Opname
                    </a>
                </div>

                <!-- Status Filter Navigation -->
                <div class="rx-filter-nav">
                    <a href="/stock-opname?status=all" class="btn btn-sm <?= ($currentStatus === 'all') ? 'btn-dark' : 'btn-outline-dark' ?> text-nowrap">
                        All Sessions (<?= count($sessions) ?>)
                    </a>
                    <a href="/stock-opname?status=in_progress" class="btn btn-sm <?= ($currentStatus === 'in_progress') ? 'btn-primary' : 'btn-outline-primary' ?> text-nowrap">
                        <i class="fas fa-spinner fa-spin me-1"></i> In Progress
                    </a>
                    <a href="/stock-opname?status=completed" class="btn btn-sm <?= ($currentStatus === 'completed') ? 'btn-success' : 'btn-outline-success' ?> text-nowrap">
                        <i class="fas fa-check-circle me-1"></i> Completed
                    </a>
                </div>

                <!-- Opname Sessions List Card -->
                <div class="card-modern p-3 p-sm-4">
                    <?php if (empty($sessions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fs-3 d-block mb-2 text-secondary"></i>
                            No stock opname sessions found.
                        </div>
                    <?php else: ?>
                        <!-- Mobile Cards (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($sessions as $so): ?>
                                <?php 
                                    $sBadge = match($so['status']) {
                                        'completed' => 'badge-tag-emerald',
                                        'in_progress' => 'badge-tag-blue',
                                        'cancelled' => 'badge-tag-crimson',
                                        default => 'badge-tag-secondary'
                                    };
                                ?>
                                <div class="rx-mobile-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <a href="/stock-opname/<?= $so['id'] ?>" class="fw-bold text-dark font-mono text-decoration-none">
                                                <?= htmlspecialchars($so['opname_number']) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.68rem;">
                                                <?= date('d M Y H:i', strtotime($so['created_at'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge-tag <?= $sBadge ?> text-uppercase font-mono" style="font-size: 0.68rem;">
                                            <?= htmlspecialchars(str_replace('_', ' ', $so['status'])) ?>
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?= htmlspecialchars($so['title']) ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Auditor: <?= htmlspecialchars($so['creator_name']) ?></div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                            <div class="text-muted" style="font-size: 0.68rem;">Variance:</div>
                                            <div class="fw-bold font-mono <?= $so['total_variance_qty'] < 0 ? 'text-danger' : ($so['total_variance_qty'] > 0 ? 'text-teal' : 'text-dark') ?>">
                                                <?= $so['total_variance_qty'] > 0 ? '+' : '' ?><?= $so['total_variance_qty'] ?> units
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <?php if ($so['status'] === 'in_progress'): ?>
                                                <a href="/stock-opname/<?= $so['id'] ?>/count" class="btn btn-sm btn-primary px-3 py-1">
                                                    <i class="fas fa-list-check me-1"></i> Count
                                                </a>
                                            <?php endif; ?>
                                            <a href="/stock-opname/<?= $so['id'] ?>" class="btn btn-sm btn-outline-dark px-2.5 py-1">
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
                                        <th>OPNAME NUMBER</th>
                                        <th>SESSION TITLE</th>
                                        <th>AUDITOR</th>
                                        <th>SYSTEM QTY</th>
                                        <th>PHYSICAL QTY</th>
                                        <th>VARIANCE</th>
                                        <th>STATUS</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($sessions as $so): ?>
                                        <?php 
                                            $sBadge = match($so['status']) {
                                                'completed' => 'badge-tag-emerald',
                                                'in_progress' => 'badge-tag-blue',
                                                'cancelled' => 'badge-tag-crimson',
                                                default => 'badge-tag-secondary'
                                            };
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="/stock-opname/<?= $so['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($so['opname_number']) ?>
                                                </a>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;"><?= date('d M Y', strtotime($so['created_at'])) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($so['title']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;"><?= $so['total_items_counted'] ?> SKU items</div>
                                            </td>
                                            <td>
                                                <div class="font-sans"><?= htmlspecialchars($so['creator_name']) ?></div>
                                                <?php if ($so['approver_name']): ?>
                                                    <div class="text-muted font-sans" style="font-size: 0.68rem;">Approved: <?= htmlspecialchars($so['approver_name']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($so['total_system_qty']) ?></td>
                                            <td><?= number_format($so['total_physical_qty']) ?></td>
                                            <td>
                                                <span class="badge-tag <?= $so['total_variance_qty'] < 0 ? 'badge-tag-crimson' : ($so['total_variance_qty'] > 0 ? 'badge-tag-teal' : 'badge-tag-dark') ?>">
                                                    <?= $so['total_variance_qty'] > 0 ? '+' : '' ?><?= $so['total_variance_qty'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $sBadge ?> text-uppercase">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $so['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <?php if ($so['status'] === 'in_progress'): ?>
                                                        <a href="/stock-opname/<?= $so['id'] ?>/count" class="btn btn-sm btn-primary py-1 px-2.5">
                                                            <i class="fas fa-list-check me-1"></i> Count
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="/stock-opname/<?= $so['id'] ?>" class="btn btn-sm btn-outline-dark py-1 px-2">
                                                        <i class="fas fa-eye me-1"></i> View
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
