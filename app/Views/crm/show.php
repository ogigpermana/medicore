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

    $age = null;
    if (!empty($customer['birth_date'])) {
        $dob = new DateTime($customer['birth_date']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
    }
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
                            </li>
                            <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-th-large text-muted"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="/crm/customers"><i class="fas fa-hospital-user text-muted"></i> Patients</a></li>
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
                <!-- Patient Profile Card -->
                <div class="card-modern p-4 mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box-solid icon-box-teal rounded-circle" style="width: 54px; height: 54px; font-size: 1.4rem;">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge-tag badge-tag-dark font-mono"><?= htmlspecialchars($customer['code']) ?></span>
                                        <span class="badge-tag <?= $customer['is_active'] ? 'badge-tag-emerald' : 'badge-tag-dark' ?>">
                                            <?= $customer['is_active'] ? 'Active Patient' : 'Inactive' ?>
                                        </span>
                                        <span class="badge-tag badge-tag-blue text-capitalize font-mono">
                                            <?= htmlspecialchars($customer['gender']) ?> <?= $age !== null ? "({$age} yo)" : '' ?>
                                        </span>
                                    </div>
                                    <h1 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($customer['name']) ?></h1>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 text-md-end">
                            <div class="small text-muted">Lifetime Pharmacy Purchases:</div>
                            <div class="fs-4 fw-bold text-teal font-mono">Rp <?= number_format($customer['total_spend'], 0, ',', '.') ?></div>
                            <div class="text-muted small"><?= (int)$customer['total_visits'] ?> pharmacy visits</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3 pt-3 border-top">
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="text-muted small">Phone Number:</div>
                            <div class="fw-bold font-mono text-dark"><?= htmlspecialchars($customer['phone'] ?? '-') ?></div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="text-muted small">Email Address:</div>
                            <div class="fw-bold font-mono text-dark"><?= htmlspecialchars($customer['email'] ?? '-') ?></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="text-muted small">Home Address:</div>
                            <div class="text-dark small"><?= htmlspecialchars($customer['address'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Clinical Safety Alerts -->
                <?php if (!empty($customer['allergy_notes'])): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-3 p-3 mb-4 shadow-sm border-danger" role="alert">
                        <div class="fs-2 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <div class="fw-bold text-danger fs-6 mb-0">CLINICAL DRUG ALLERGY WARNING</div>
                            <div class="text-danger small mt-0.5">
                                <?= htmlspecialchars($customer['allergy_notes']) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($customer['chronic_disease_notes'])): ?>
                    <div class="card-modern p-3 mb-4 border-info">
                        <div class="d-flex align-items-center gap-2 text-teal fw-bold small mb-1">
                            <i class="fas fa-heartbeat"></i> CHRONIC DISEASE & MEDICAL HISTORY
                        </div>
                        <div class="text-dark small">
                            <?= htmlspecialchars($customer['chronic_disease_notes']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- History Tabs -->
                <div class="card-modern p-3 p-sm-4">
                    <ul class="nav nav-tabs nav-fill mb-3" id="patientTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold small py-2" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-content" type="button" role="tab">
                                <i class="fas fa-receipt me-1"></i> Sales & Medication Receipts (<?= count($customer['sales_history']) ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small py-2" id="rx-tab" data-bs-toggle="tab" data-bs-target="#rx-content" type="button" role="tab">
                                <i class="fas fa-file-medical me-1"></i> Clinical Prescriptions (<?= count($customer['prescription_history']) ?>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="patientTabContent">
                        <!-- Sales History Tab -->
                        <div class="tab-pane fade show active" id="sales-content" role="tabpanel">
                            <?php if (empty($customer['sales_history'])): ?>
                                <div class="text-center py-4 text-muted small">No past point-of-sale transactions recorded for this patient.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 font-mono small">
                                        <thead>
                                            <tr class="text-muted" style="font-size: 0.75rem;">
                                                <th>INVOICE #</th>
                                                <th>DATE</th>
                                                <th>PAYMENT METHOD</th>
                                                <th>CASHIER</th>
                                                <th class="text-end">TOTAL AMOUNT</th>
                                                <th class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customer['sales_history'] as $s): ?>
                                                <tr>
                                                    <td class="fw-bold text-dark font-mono"><?= htmlspecialchars($s['invoice_number']) ?></td>
                                                    <td class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></td>
                                                    <td>
                                                        <span class="badge-tag badge-tag-dark text-uppercase"><?= htmlspecialchars($s['payment_method']) ?></span>
                                                    </td>
                                                    <td class="font-sans text-secondary small"><?= htmlspecialchars($s['cashier_name'] ?? 'Cashier') ?></td>
                                                    <td class="text-end fw-bold font-mono text-teal">
                                                        Rp <?= number_format($s['total_amount'], 0, ',', '.') ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/pos/receipt/<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-outline-dark py-0.5 px-2 font-sans" title="View Thermal Receipt">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Prescriptions History Tab -->
                        <div class="tab-pane fade" id="rx-content" role="tabpanel">
                            <?php if (empty($customer['prescription_history'])): ?>
                                <div class="text-center py-4 text-muted small">No doctor prescriptions on file for this patient.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 font-mono small">
                                        <thead>
                                            <tr class="text-muted" style="font-size: 0.75rem;">
                                                <th>PRESCRIPTION #</th>
                                                <th>DOCTOR / CLINIC</th>
                                                <th>REVIEW STATUS</th>
                                                <th>PHARMACIST</th>
                                                <th>DATE</th>
                                                <th class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customer['prescription_history'] as $rx): ?>
                                                <tr>
                                                    <td class="fw-bold text-dark font-mono"><?= htmlspecialchars($rx['prescription_number']) ?></td>
                                                    <td class="font-sans"><?= htmlspecialchars($rx['doctor_name'] ?? '-') ?></td>
                                                    <td>
                                                        <span class="badge-tag <?= $rx['status'] === 'dispensed' ? 'badge-tag-emerald' : 'badge-tag-teal' ?> text-capitalize font-sans">
                                                            <?= htmlspecialchars($rx['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="font-sans text-secondary small"><?= htmlspecialchars($rx['reviewer_name'] ?? '-') ?></td>
                                                    <td class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y', strtotime($rx['created_at'])) ?></td>
                                                    <td class="text-center">
                                                        <a href="/prescriptions/<?= $rx['id'] ?>" class="btn btn-sm btn-outline-teal py-0.5 px-2 font-sans">
                                                            <i class="fas fa-eye"></i> Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
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
