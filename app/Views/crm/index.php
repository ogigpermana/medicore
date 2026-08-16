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
                <a href="/crm/customers" class="sidebar-menu-link active">
                    <i class="fas fa-hospital-user"></i> <span>Customer Directory</span>
                </a>

                <!-- Purchasing & PBF Management -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Purchasing & PBF</div>
                    <a href="/purchasing" class="sidebar-menu-link">
                        <i class="fas fa-file-invoice"></i> <span>Purchase Orders (SP)</span>
                    </a>
                    <a href="/purchasing/ap-ledger" class="sidebar-menu-link">
                        <i class="fas fa-book-medical"></i> <span>Accounts Payable (AP)</span>
                    </a>
                <?php endif; ?>

                <!-- Multi-Branch & Transfers -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Multi-Branch Transfers</div>
                    <a href="/transfers" class="sidebar-menu-link">
                        <i class="fas fa-truck-ramp-box"></i> <span>Stock Transfers</span>
                    </a>
                <?php endif; ?>

                <!-- Reports & Stock Opname -->
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
            <!-- Topbar -->
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
                            <li><a class="dropdown-item" href="/crm/customers"><i class="fas fa-hospital-user text-muted"></i> Patient Directory</a></li>
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
                <div id="crmAlert"></div>

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-hospital-user me-1"></i> Patient Care CRM</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Customer & Patient Master Directory</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Central patient profiles, clinical drug allergy alerts, chronic condition tracking, and lifetime pharmacy spend.
                        </p>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm w-100 w-md-auto fw-bold" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        <i class="fas fa-user-plus me-1"></i> Register Patient
                    </button>
                </div>

                <!-- CRM Metric Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Registered</span>
                                <div class="icon-box-solid icon-box-dark" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['total_customers']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Master patient profiles</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Drug Allergy Alerts</span>
                                <div class="icon-box-solid bg-danger text-white" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-allergies"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-danger font-mono"><?= number_format($stats['allergy_flagged']) ?></div>
                            <div class="text-danger small" style="font-size: 0.72rem;">Clinical allergy warnings</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Chronic Condition</span>
                                <div class="icon-box-solid icon-box-amber" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['chronic_patients']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Hypertension, Diabetes, etc.</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Lifetime Pharmacy Spend</span>
                                <div class="icon-box-solid icon-box-teal" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-teal font-mono">Rp <?= number_format($stats['lifetime_spend'], 0, ',', '.') ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Cumulative patient revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="card-modern p-3 mb-4">
                    <form method="GET" action="/crm/customers" class="row g-2 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search patient name, phone, code, allergies..." value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="filter" class="form-select form-select-sm">
                                <option value="all" <?= ($currentFilter === 'all') ? 'selected' : '' ?>>All Clinical Categories</option>
                                <option value="allergy" <?= ($currentFilter === 'allergy') ? 'selected' : '' ?>>⚠️ Has Drug Allergies</option>
                                <option value="chronic" <?= ($currentFilter === 'chronic') ? 'selected' : '' ?>>🩺 Chronic Disease Patients</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="all" <?= ($currentStatus === 'all') ? 'selected' : '' ?>>All Status</option>
                                <option value="active" <?= ($currentStatus === 'active') ? 'selected' : '' ?>>Active Only</option>
                                <option value="inactive" <?= ($currentStatus === 'inactive') ? 'selected' : '' ?>>Inactive Only</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-dark btn-sm flex-fill">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <?php if (!empty($currentSearch) || $currentFilter !== 'all' || $currentStatus !== 'all'): ?>
                                <a href="/crm/customers" class="btn btn-outline-secondary btn-sm" title="Reset">
                                    <i class="fas fa-redo"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Patient Cards Grid / Table -->
                <div class="row g-3 g-md-4">
                    <?php if (empty($customers)): ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-user-slash fs-2 d-block mb-2 text-secondary"></i>
                            No patient records found matching your filters.
                        </div>
                    <?php else: ?>
                        <?php foreach ($customers as $cust): ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between <?= !empty($cust['allergy_notes']) ? 'border-danger-subtle' : '' ?>">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge-tag badge-tag-dark font-mono"><?= htmlspecialchars($cust['code']) ?></span>
                                            <div class="d-flex gap-1">
                                                <?php if (!empty($cust['allergy_notes'])): ?>
                                                    <span class="badge-tag badge-tag-crimson" title="Clinical Drug Allergy Alert">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Allergy Alert
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($cust['is_active']): ?>
                                                    <span class="badge-tag badge-tag-emerald">Active</span>
                                                <?php else: ?>
                                                    <span class="badge-tag badge-tag-dark">Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <h3 class="h5 fw-bold text-dark mb-1 d-flex align-items-center justify-content-between">
                                            <a href="/crm/customers/<?= $cust['id'] ?>" class="text-dark text-decoration-none hover-teal">
                                                <?= htmlspecialchars($cust['name']) ?>
                                            </a>
                                            <span class="text-muted small font-mono" style="font-size: 0.75rem;">
                                                <i class="fas fa-<?= $cust['gender'] === 'female' ? 'venus text-danger' : ($cust['gender'] === 'male' ? 'mars text-primary' : 'genderless') ?>"></i>
                                            </span>
                                        </h3>

                                        <div class="small text-secondary mb-1 font-mono">
                                            <i class="fas fa-phone text-muted me-1.5" style="width: 16px;"></i> <?= htmlspecialchars($cust['phone'] ?? '-') ?>
                                        </div>
                                        <div class="small text-secondary mb-2 font-mono">
                                            <i class="fas fa-envelope text-muted me-1.5" style="width: 16px;"></i> <?= htmlspecialchars($cust['email'] ?? '-') ?>
                                        </div>

                                        <?php if (!empty($cust['allergy_notes'])): ?>
                                            <div class="p-2 bg-danger-subtle text-danger rounded border border-danger-subtle small mb-2" style="font-size: 0.75rem;">
                                                <strong><i class="fas fa-allergies me-1"></i> Drug Allergy:</strong> <?= htmlspecialchars($cust['allergy_notes']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($cust['chronic_disease_notes'])): ?>
                                            <div class="p-2 bg-light text-secondary rounded border small mb-2" style="font-size: 0.75rem;">
                                                <strong><i class="fas fa-heartbeat text-teal me-1"></i> Chronic:</strong> <?= htmlspecialchars($cust['chronic_disease_notes']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="p-2.5 bg-light rounded border mb-3">
                                            <div class="row g-2 text-center font-mono" style="font-size: 0.72rem;">
                                                <div class="col-6 border-end">
                                                    <div class="text-muted">Total Visits:</div>
                                                    <div class="fw-bold text-dark fs-6"><?= (int)$cust['total_visits'] ?> visits</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted">Total Spend:</div>
                                                    <div class="fw-bold text-teal fs-6">
                                                        Rp <?= number_format($cust['total_spend'], 0, ',', '.') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top gap-2">
                                        <a href="/crm/customers/<?= $cust['id'] ?>" class="btn btn-sm btn-outline-teal flex-fill" title="View Patient Profile & History">
                                            <i class="fas fa-folder-open me-1"></i> History
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-dark px-2.5" onclick='openEditCustomerModal(<?= json_encode($cust, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="Edit Profile">
                                            <i class="fas fa-user-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger px-2.5" onclick="openDeleteCustomerModal(<?= $cust['id'] ?>, '<?= addslashes($cust['name']) ?>')" title="Delete Customer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Server-Side Pagination Bar -->
                <?php if (isset($pagination) && ($pagination['total_pages'] > 1 || $pagination['total'] > 10)): ?>
                    <?php
                        $queryParams = $_GET ?? [];
                        $buildUrl = function($pageNum) use ($queryParams) {
                            $params = array_merge($queryParams, ['page' => $pageNum]);
                            return '/crm/customers?' . http_build_query($params);
                        };
                        $curPage = $pagination['page'];
                        $totPages = $pagination['total_pages'];
                    ?>
                    <div class="card-modern p-3 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <span>Showing <?= $pagination['from'] ?>–<?= $pagination['to'] ?> of <?= number_format($pagination['total']) ?> patients</span>
                            <span class="mx-2">•</span>
                            <span>Per page:</span>
                            <select class="form-select form-select-sm" style="width: 75px;" onchange="location.href='/crm/customers?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)), per_page: this.value, page: 1}).toString()">
                                <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $pagination['per_page'] == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                            </select>
                        </div>

                        <nav aria-label="Patient pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $curPage > 1 ? $buildUrl($curPage - 1) : '#' ?>">
                                        <i class="fas fa-chevron-left me-1"></i> Prev
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $totPages; $i++): ?>
                                    <li class="page-item <?= ($i === $curPage) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $curPage < $totPages ? $buildUrl($curPage + 1) : '#' ?>">
                                        Next <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Register Patient Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-user-plus text-teal me-1"></i> Register New Customer / Patient
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCustomerForm" onsubmit="submitAddCustomer(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold">Patient Code *</label>
                                <input type="text" name="code" class="form-control form-control-sm font-mono text-uppercase" value="<?= htmlspecialchars($nextCode) ?>" required>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-bold">Patient Full Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Ibu Siti Aminah / Bpk. Hendra" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-sm font-mono" placeholder="e.g. 081234567890">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm font-mono" placeholder="patient@example.com">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Gender</label>
                                <select name="gender" class="form-select form-select-sm">
                                    <option value="other">Other</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Birth Date</label>
                                <input type="date" name="birth_date" class="form-control form-control-sm font-mono">
                            </div>

                            <div class="col-12">
                                <label class="form-label small">Residential Address</label>
                                <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="Patient home address..."></textarea>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded border">
                                    <div class="fw-bold text-dark small mb-2"><i class="fas fa-stethoscope text-teal me-1"></i> Clinical Pharmacy Safety Notes</div>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small text-danger fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Drug Allergies (Alergi Obat)
                                            </label>
                                            <textarea name="allergy_notes" class="form-control form-control-sm border-danger-subtle" rows="2" placeholder="e.g. Alergi Penicillin / Amoxicillin, Alergi Sulfa..."></textarea>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small text-dark fw-bold">
                                                <i class="fas fa-heartbeat text-teal me-1"></i> Chronic Illness (Penyakit Kronis)
                                            </label>
                                            <textarea name="chronic_disease_notes" class="form-control form-control-sm" rows="2" placeholder="e.g. Hipertensi, Diabetes Mellitus Tipe 2, Asma..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="addCustomerBtn">
                            <i class="fas fa-save me-1"></i> Save Patient Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-user-edit text-teal me-1"></i> Edit Patient Record
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCustomerForm" onsubmit="submitEditCustomer(event)">
                    <input type="hidden" name="id" id="edit_cust_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold">Patient Code</label>
                                <input type="text" name="code" id="edit_cust_code" class="form-control form-control-sm font-mono text-uppercase" readonly disabled>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-bold">Patient Full Name *</label>
                                <input type="text" name="name" id="edit_cust_name" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" name="phone" id="edit_cust_phone" class="form-control form-control-sm font-mono">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Email Address</label>
                                <input type="email" name="email" id="edit_cust_email" class="form-control form-control-sm font-mono">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Gender</label>
                                <select name="gender" id="edit_cust_gender" class="form-select form-select-sm">
                                    <option value="other">Other</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="is_active" id="edit_cust_is_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small">Birth Date</label>
                                <input type="date" name="birth_date" id="edit_cust_birth_date" class="form-control form-control-sm font-mono">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">Residential Address</label>
                                <input type="text" name="address" id="edit_cust_address" class="form-control form-control-sm">
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded border">
                                    <div class="fw-bold text-dark small mb-2"><i class="fas fa-stethoscope text-teal me-1"></i> Clinical Pharmacy Safety Notes</div>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small text-danger fw-bold">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Drug Allergies (Alergi Obat)
                                            </label>
                                            <textarea name="allergy_notes" id="edit_cust_allergy" class="form-control form-control-sm border-danger-subtle" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small text-dark fw-bold">
                                                <i class="fas fa-heartbeat text-teal me-1"></i> Chronic Illness (Penyakit Kronis)
                                            </label>
                                            <textarea name="chronic_disease_notes" id="edit_cust_chronic" class="form-control form-control-sm" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="editCustomerBtn">
                            <i class="fas fa-save me-1"></i> Update Patient Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditCustomerModal(cust) {
            document.getElementById('edit_cust_id').value = cust.id;
            document.getElementById('edit_cust_code').value = cust.code;
            document.getElementById('edit_cust_name').value = cust.name || '';
            document.getElementById('edit_cust_phone').value = cust.phone || '';
            document.getElementById('edit_cust_email').value = cust.email || '';
            document.getElementById('edit_cust_gender').value = cust.gender || 'other';
            document.getElementById('edit_cust_birth_date').value = cust.birth_date || '';
            document.getElementById('edit_cust_address').value = cust.address || '';
            document.getElementById('edit_cust_allergy').value = cust.allergy_notes || '';
            document.getElementById('edit_cust_chronic').value = cust.chronic_disease_notes || '';
            document.getElementById('edit_cust_is_active').value = cust.is_active ? '1' : '0';

            const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            modal.show();
        }

        async function submitAddCustomer(e) {
            e.preventDefault();
            const btn = document.getElementById('addCustomerBtn');
            const alertBox = document.getElementById('crmAlert');
            const form = document.getElementById('addCustomerForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            try {
                const res = await fetch('/crm/customers', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(Object.fromEntries(formData.entries()))
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Patient Profile';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Patient Profile';
            }
        }

        async function submitEditCustomer(e) {
            e.preventDefault();
            const btn = document.getElementById('editCustomerBtn');
            const alertBox = document.getElementById('crmAlert');
            const form = document.getElementById('editCustomerForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

            try {
                const res = await fetch('/crm/customers/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(Object.fromEntries(formData.entries()))
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Patient Profile';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Patient Profile';
            }
        }

        async function openDeleteCustomerModal(id, name) {
            if (!confirm(`Are you sure you want to remove or deactivate patient record for "${name}"?`)) {
                return;
            }

            const alertBox = document.getElementById('crmAlert');

            try {
                const res = await fetch('/crm/customers/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ id: id })
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        }
    </script>
</body>
</html>
