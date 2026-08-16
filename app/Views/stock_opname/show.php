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

    $sBadge = match($so['status']) {
        'completed' => 'badge-tag-emerald',
        'in_progress' => 'badge-tag-blue',
        'cancelled' => 'badge-tag-crimson',
        default => 'badge-tag-secondary'
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
                <div id="approveAlert"></div>

                <!-- Header Information -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag <?= $sBadge ?> text-uppercase font-mono">
                                <?= htmlspecialchars(str_replace('_', ' ', $so['status'])) ?>
                            </span>
                            <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($so['opname_number']) ?></span>
                            <h1 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($so['title']) ?></h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Audited by <strong><?= htmlspecialchars($so['creator_name']) ?></strong> on <?= date('d F Y', strtotime($so['created_at'])) ?>
                            <?php if ($so['approved_by']): ?>
                                • Approved by <strong><?= htmlspecialchars($so['approver_name']) ?></strong> on <?= date('d M Y H:i', strtotime($so['completed_at'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                        <?php if ($so['status'] === 'in_progress'): ?>
                            <a href="/stock-opname/<?= $so['id'] ?>/count" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                                <i class="fas fa-list-check me-1"></i> Edit Counts
                            </a>
                            <?php if (in_array($currentRole, ['superadmin', 'owner', 'pharmacist'])): ?>
                                <button type="button" class="btn btn-primary btn-sm px-3 fw-bold flex-fill flex-md-grow-0" onclick="approveReconciliation()" id="approveBtn">
                                    <i class="fas fa-check-double me-1"></i> Approve & Reconcile
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge-tag badge-tag-emerald py-1.5 px-3"><i class="fas fa-check-circle me-1"></i> Inventory Reconciled</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Variance KPI Summary Row -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <span class="text-muted small">System Recorded Qty</span>
                            <div class="fs-5 fw-bold text-dark font-mono mt-1">
                                <?= number_format($so['total_system_qty']) ?> units
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <span class="text-muted small">Physical Counted Qty</span>
                            <div class="fs-5 fw-bold text-teal font-mono mt-1">
                                <?= number_format($so['total_physical_qty']) ?> units
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <span class="text-muted small">Net Discrepancy Units</span>
                            <div class="fs-5 fw-bold font-mono mt-1 <?= $so['total_variance_qty'] < 0 ? 'text-danger' : ($so['total_variance_qty'] > 0 ? 'text-teal' : 'text-dark') ?>">
                                <?= $so['total_variance_qty'] > 0 ? '+' : '' ?><?= number_format($so['total_variance_qty']) ?> units
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <span class="text-muted small">Financial Variance Value</span>
                            <div class="fs-5 fw-bold font-mono mt-1 <?= $so['total_variance_value'] < 0 ? 'text-danger' : ($so['total_variance_value'] > 0 ? 'text-teal' : 'text-dark') ?>">
                                Rp <?= number_format($so['total_variance_value'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discrepancy Matrix Table -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div><i class="fas fa-boxes-packing text-teal me-1"></i> Stock Opname Variance Report</div>
                        <span class="text-muted small"><?= count($so['items']) ?> items audited</span>
                    </div>

                    <!-- Mobile Cards (< 768px) -->
                    <div class="d-md-none">
                        <?php foreach ($so['items'] as $it): ?>
                            <?php $var = (int)$it['variance_qty']; ?>
                            <div class="p-3 bg-light rounded border mb-2 <?= $var !== 0 ? 'border-danger' : '' ?>">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($it['product_name']) ?></div>
                                        <div class="text-muted font-mono small"><?= htmlspecialchars($it['sku']) ?></div>
                                    </div>
                                    <span class="badge-tag <?= $var < 0 ? 'badge-tag-crimson' : ($var > 0 ? 'badge-tag-teal' : 'badge-tag-emerald') ?> font-mono">
                                        <?= $var > 0 ? '+' : '' ?><?= $var ?> units
                                    </span>
                                </div>
                                <div class="row g-1 small pt-1 border-top mt-1">
                                    <div class="col-6">System: <strong><?= $it['system_qty'] ?></strong></div>
                                    <div class="col-6">Physical: <strong><?= $it['physical_qty'] ?></strong></div>
                                    <div class="col-12 text-muted">Reason: <strong class="text-capitalize"><?= htmlspecialchars($it['adjustment_reason']) ?></strong></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Desktop Table (>= 768px) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>MEDICATION & SKU</th>
                                    <th class="text-center">SYSTEM</th>
                                    <th class="text-center">PHYSICAL</th>
                                    <th class="text-center">VARIANCE</th>
                                    <th>BUY PRICE</th>
                                    <th>VARIANCE VALUE</th>
                                    <th>REASON</th>
                                    <th>AUDIT NOTES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php foreach ($so['items'] as $it): ?>
                                    <?php $var = (int)$it['variance_qty']; ?>
                                    <tr class="<?= $var !== 0 ? 'table-danger-subtle' : '' ?>">
                                        <td>
                                            <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                        </td>
                                        <td class="text-center"><?= $it['system_qty'] ?></td>
                                        <td class="text-center fw-bold"><?= $it['physical_qty'] ?></td>
                                        <td class="text-center">
                                            <span class="badge-tag <?= $var < 0 ? 'badge-tag-crimson' : ($var > 0 ? 'badge-tag-teal' : 'badge-tag-dark') ?>">
                                                <?= $var > 0 ? '+' : '' ?><?= $var ?>
                                            </span>
                                        </td>
                                        <td>Rp <?= number_format($it['buy_price'], 0, ',', '.') ?></td>
                                        <td class="<?= $var < 0 ? 'text-danger' : ($var > 0 ? 'text-teal' : 'text-muted') ?> fw-bold">
                                            Rp <?= number_format($it['variance_value'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge-tag badge-tag-dark text-capitalize font-sans"><?= htmlspecialchars($it['adjustment_reason']) ?></span>
                                        </td>
                                        <td class="text-muted font-sans" style="font-size: 0.75rem;"><?= htmlspecialchars($it['notes'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function approveReconciliation() {
            if (!confirm('Are you sure you want to approve this Stock Opname? This will automatically update physical inventory stock levels in the ERP and log stock movement ledger.')) {
                return;
            }

            const btn = document.getElementById('approveBtn');
            const alertBox = document.getElementById('approveAlert');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Reconciling Stock...';

            try {
                const res = await fetch('/stock-opname/approve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ stock_opname_id: <?= (int)$so['id'] ?> })
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-double me-1"></i> Approve & Reconcile';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double me-1"></i> Approve & Reconcile';
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
