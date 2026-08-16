<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'FEFO Expiry Sentinel — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">

<?php
    $currentRole = strtolower($role ?? $user['role_name'] ?? $user['role'] ?? 'pharmacist');
    $currentName = $user['full_name'] ?? $user['name'] ?? 'Staff User';
?>
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
                    <a href="/stock-opname" class="sidebar-menu-link">
                        <i class="fas fa-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                <?php endif; ?>

                <!-- Inventory & FEFO (Visible to superadmin, pharmacist, warehouse, owner) -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Inventory & FEFO</div>
                    <a href="/inventory/products" class="sidebar-menu-link">
                        <i class="fas fa-boxes"></i> <span>Medications</span>
                    </a>
                    <a href="/inventory/fefo" class="sidebar-menu-link active">
                        <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                    </a>
                <?php endif; ?>

                <!-- Master Data (Visible to superadmin, warehouse, owner) -->
                <?php if (in_array($currentRole, ['superadmin', 'warehouse', 'owner'])): ?>
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

            <!-- Main Content -->
            <main class="app-content">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-amber"><i class="fas fa-shield-alt me-1"></i> Wastage Prevention</span>
                            <h1 class="h4 fw-bold text-dark mb-0">FEFO Expiry Sentinel Matrix</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Automated First-Expired, First-Out queue prioritizing batches for shelf dispatch to achieve zero expiration losses.
                        </p>
                    </div>

                    <div class="d-flex gap-1.5 flex-wrap">
                        <a href="/inventory/fefo?days=30" class="btn btn-sm <?= ($daysThreshold == 30) ? 'btn-dark' : 'btn-outline-dark' ?>">
                            &lt; 30 Days (Critical)
                        </a>
                        <a href="/inventory/fefo?days=60" class="btn btn-sm <?= ($daysThreshold == 60) ? 'btn-dark' : 'btn-outline-dark' ?>">
                            &lt; 60 Days (Warning)
                        </a>
                        <a href="/inventory/fefo?days=90" class="btn btn-sm <?= ($daysThreshold == 90) ? 'btn-dark' : 'btn-outline-dark' ?>">
                            &lt; 90 Days (Advisory)
                        </a>
                        <a href="/inventory/fefo?days=365" class="btn btn-sm <?= ($daysThreshold == 365) ? 'btn-dark' : 'btn-outline-dark' ?>">
                            All Batches
                        </a>
                    </div>
                </div>

                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Batches expiring within <span class="text-teal"><?= $daysThreshold ?> days</span>: <strong class="text-dark"><?= count($batches) ?></strong> lots detected
                        </div>
                        <span class="badge-tag badge-tag-teal">FEFO Dispatch Engine Active</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>BATCH LOT</th>
                                    <th>MEDICATION</th>
                                    <th>REMAINING STOCK</th>
                                    <th>EXPIRATION DATE</th>
                                    <th>DAYS REMAINING</th>
                                    <th>SUPPLIER (PBF)</th>
                                    <th>DISPATCH PRIORITY</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php if (empty($batches)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle text-success fs-4 d-block mb-2"></i>
                                            No batches expiring within <?= $daysThreshold ?> days. All inventory is safe!
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($batches as $b): ?>
                                        <?php 
                                            $badgeClass = ($b['days_until_expiry'] <= 30) ? 'badge-tag-crimson' : (($b['days_until_expiry'] <= 60) ? 'badge-tag-amber' : 'badge-tag-emerald');
                                            $priorityLabel = ($b['days_until_expiry'] <= 30) ? 'URGENT DISPENSE' : (($b['days_until_expiry'] <= 60) ? 'ACTIVE FRONT SHELF' : 'NORMAL ROTATION');
                                        ?>
                                        <tr>
                                            <td><code><?= htmlspecialchars($b['batch_number']) ?></code></td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($b['product_name']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;">SKU: <?= htmlspecialchars($b['product_sku']) ?></div>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?= $b['current_quantity'] ?></span> <?= htmlspecialchars($b['unit_symbol'] ?? 'unit') ?>
                                            </td>
                                            <td class="fw-bold"><?= htmlspecialchars($b['expiry_date']) ?></td>
                                            <td>
                                                <span class="badge-tag <?= $badgeClass ?>">
                                                    <?= $b['days_until_expiry'] ?> Days Left
                                                </span>
                                            </td>
                                            <td class="text-secondary font-sans"><?= htmlspecialchars($b['supplier_name'] ?? 'Direct') ?></td>
                                            <td>
                                                <span class="badge-tag <?= $badgeClass ?> font-sans">
                                                    <?= $priorityLabel ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
