<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Digital Prescription Queue — MediCore' ?></title>
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

                <!-- Clinical & Prescriptions -->
                <div class="sidebar-section-label mt-2">Clinical Pharmacy</div>
                <a href="/prescriptions" class="sidebar-menu-link active">
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
                    <a href="/inventory/fefo" class="sidebar-menu-link">
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

            <!-- Page Content -->
            <main class="app-content">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-clinic-medical me-1"></i> Module 4</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Clinical Digital Prescription Queue</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Screen incoming doctor orders, verify SIP & clinical doses, schedule compounding (racikan), and dispense medications.
                        </p>
                    </div>

                    <a href="/prescriptions/create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> New Prescription (Rx)
                    </a>
                </div>

                <!-- Status Filter Navigation Bar (Touch-Scrollable) -->
                <div class="rx-filter-nav">
                    <a href="/prescriptions?status=all" class="btn btn-sm <?= ($currentStatus === 'all') ? 'btn-dark' : 'btn-outline-dark' ?> text-nowrap">
                        All Orders (<?= $statusCounts['all'] ?>)
                    </a>
                    <a href="/prescriptions?status=pending" class="btn btn-sm <?= ($currentStatus === 'pending') ? 'btn-danger' : 'btn-outline-danger' ?> text-nowrap">
                        <i class="fas fa-clock me-1"></i> Pending (<?= $statusCounts['pending'] ?>)
                    </a>
                    <a href="/prescriptions?status=reviewed" class="btn btn-sm <?= ($currentStatus === 'reviewed') ? 'btn-primary' : 'btn-outline-primary' ?> text-nowrap">
                        <i class="fas fa-check-circle me-1"></i> Reviewed (<?= $statusCounts['reviewed'] ?>)
                    </a>
                    <a href="/prescriptions?status=compounding" class="btn btn-sm <?= ($currentStatus === 'compounding') ? 'btn-warning text-dark' : 'btn-outline-warning' ?> text-nowrap">
                        <i class="fas fa-mortar-pestle me-1"></i> Compounding (<?= $statusCounts['compounding'] ?>)
                    </a>
                    <a href="/prescriptions?status=ready" class="btn btn-sm <?= ($currentStatus === 'ready') ? 'btn-success' : 'btn-outline-success' ?> text-nowrap">
                        <i class="fas fa-box-check me-1"></i> Ready (<?= $statusCounts['ready'] ?>)
                    </a>
                    <a href="/prescriptions?status=dispensed" class="btn btn-sm <?= ($currentStatus === 'dispensed') ? 'btn-secondary' : 'btn-outline-secondary' ?> text-nowrap">
                        <i class="fas fa-hand-holding-medical me-1"></i> Dispensed (<?= $statusCounts['dispensed'] ?>)
                    </a>
                </div>

                <!-- Prescription Queue Container -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Showing <strong class="text-teal"><?= count($prescriptions) ?></strong> prescription orders in queue
                        </div>
                    </div>

                    <?php if (empty($prescriptions)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-medical-alt fs-3 d-block mb-2 text-secondary"></i>
                            No prescriptions found in this queue status.
                        </div>
                    <?php else: ?>
                        <!-- 1. Mobile Card List (Visible on < 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($prescriptions as $rx): ?>
                                <?php 
                                    $statusBadge = match($rx['status']) {
                                        'pending' => 'badge-tag-crimson',
                                        'reviewed' => 'badge-tag-blue',
                                        'compounding' => 'badge-tag-amber',
                                        'ready' => 'badge-tag-emerald',
                                        'dispensed' => 'badge-tag-dark',
                                        default => 'badge-tag-secondary'
                                    };
                                ?>
                                <div class="rx-mobile-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <a href="/prescriptions/<?= $rx['id'] ?>" class="fw-bold text-dark font-mono text-decoration-none">
                                                <?= htmlspecialchars($rx['prescription_number']) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.68rem;">
                                                <?= date('d M Y H:i', strtotime($rx['created_at'])) ?>
                                            </div>
                                        </div>
                                        <span class="badge-tag <?= $statusBadge ?> text-uppercase font-mono" style="font-size: 0.68rem;">
                                            <?= htmlspecialchars($rx['status']) ?>
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($rx['patient_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <?= $rx['patient_age'] ? $rx['patient_age'] . ' yrs' : '-' ?> • 
                                            <?= ucfirst($rx['patient_gender']) ?>
                                            <?= $rx['patient_weight'] ? ' • ' . $rx['patient_weight'] . ' kg' : '' ?>
                                        </div>
                                    </div>

                                    <div class="p-2 bg-light rounded mb-2 small text-secondary">
                                        <div><i class="fas fa-user-md text-primary me-1"></i> <?= htmlspecialchars($rx['doctor_name']) ?></div>
                                        <?php if (!empty($rx['doctor_sip'])): ?>
                                            <div class="font-mono text-muted" style="font-size: 0.68rem;"><?= htmlspecialchars($rx['doctor_sip']) ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                            <span class="badge-tag badge-tag-teal"><?= $rx['item_count'] ?> Finished</span>
                                            <?php if ($rx['compound_count'] > 0): ?>
                                                <span class="badge-tag badge-tag-amber ms-1"><i class="fas fa-mortar-pestle me-1"></i><?= $rx['compound_count'] ?> Compound</span>
                                            <?php endif; ?>
                                            <div class="fw-bold text-teal font-mono mt-1">Rp <?= number_format($rx['total_amount'], 0, ',', '.') ?></div>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <a href="/prescriptions/<?= $rx['id'] ?>" class="btn btn-sm btn-outline-dark px-2.5 py-1" style="font-size: 0.78rem;">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="/prescriptions/<?= $rx['id'] ?>/label" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" title="Print Etiket">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 2. Desktop High-Density Table (Visible on >= 768px) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>RX NUMBER</th>
                                        <th>PATIENT & AGE</th>
                                        <th>PRESCRIBING DOCTOR</th>
                                        <th>ITEMS / RACIKAN</th>
                                        <th>STATUS</th>
                                        <th>TOTAL DUE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($prescriptions as $rx): ?>
                                        <?php 
                                            $statusBadge = match($rx['status']) {
                                                'pending' => 'badge-tag-crimson',
                                                'reviewed' => 'badge-tag-blue',
                                                'compounding' => 'badge-tag-amber',
                                                'ready' => 'badge-tag-emerald',
                                                'dispensed' => 'badge-tag-dark',
                                                default => 'badge-tag-secondary'
                                            };
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="/prescriptions/<?= $rx['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($rx['prescription_number']) ?>
                                                </a>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;">
                                                    <?= date('d M Y H:i', strtotime($rx['created_at'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($rx['patient_name']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.72rem;">
                                                    <?= $rx['patient_age'] ? $rx['patient_age'] . ' yrs' : '-' ?> • 
                                                    <?= ucfirst($rx['patient_gender']) ?>
                                                    <?= $rx['patient_weight'] ? ' • ' . $rx['patient_weight'] . ' kg' : '' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($rx['doctor_name']) ?></div>
                                                <div class="text-muted font-mono" style="font-size: 0.7rem;"><?= htmlspecialchars($rx['doctor_sip'] ?? '-') ?></div>
                                            </td>
                                            <td>
                                                <div class="font-sans">
                                                    <span class="badge-tag badge-tag-teal"><?= $rx['item_count'] ?> Finished</span>
                                                    <?php if ($rx['compound_count'] > 0): ?>
                                                        <span class="badge-tag badge-tag-amber ms-1"><i class="fas fa-mortar-pestle me-1"></i><?= $rx['compound_count'] ?> Compound</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $statusBadge ?> text-uppercase">
                                                    <?= htmlspecialchars($rx['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-teal">Rp <?= number_format($rx['total_amount'], 0, ',', '.') ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="/prescriptions/<?= $rx['id'] ?>" class="btn btn-sm btn-outline-dark py-1 px-2" title="Review & Screen">
                                                        <i class="fas fa-eye me-1"></i> Review
                                                    </a>
                                                    <a href="/prescriptions/<?= $rx['id'] ?>/label" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Print Etiket">
                                                        <i class="fas fa-tag"></i>
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
