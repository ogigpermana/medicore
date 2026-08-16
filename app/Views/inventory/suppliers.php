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
                    <a href="/inventory/suppliers" class="sidebar-menu-link active">
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
            <!-- Slim Topbar -->
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
                <div id="supplierAlert"></div>

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-amber"><i class="fas fa-truck-loading me-1"></i> PBF Directory</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Pharmaceutical Suppliers & Distributors</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Verified pharmaceutical distributors (Pedagang Besar Farmasi) for purchase orders, AP billing, and GRN inventory receiving.
                        </p>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm w-100 w-md-auto fw-bold" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="fas fa-plus-circle me-1"></i> Add New Supplier
                    </button>
                </div>

                <!-- Search & Filters -->
                <div class="card-modern p-3 mb-4">
                    <form method="GET" action="/inventory/suppliers" class="row g-2 align-items-center">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search supplier name, PBF code, contact, phone..." value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-3 col-lg-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="all" <?= ($currentStatus === 'all') ? 'selected' : '' ?>>All Status (Active & Inactive)</option>
                                <option value="active" <?= ($currentStatus === 'active') ? 'selected' : '' ?>>Active Only</option>
                                <option value="inactive" <?= ($currentStatus === 'inactive') ? 'selected' : '' ?>>Inactive Only</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <button type="submit" class="btn btn-dark btn-sm w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        <?php if (!empty($currentSearch) || $currentStatus !== 'all'): ?>
                            <div class="col-12 col-lg-2 text-lg-end">
                                <a href="/inventory/suppliers" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Suppliers Cards Grid -->
                <div class="row g-3 g-md-4">
                    <?php if (empty($suppliers)): ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-truck-ramp-box fs-2 d-block mb-2 text-secondary"></i>
                            No suppliers found matching your query.
                        </div>
                    <?php else: ?>
                        <?php foreach ($suppliers as $sup): ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card-modern p-3 p-sm-4 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge-tag badge-tag-dark font-mono"><?= htmlspecialchars($sup['code']) ?></span>
                                            <?php if ($sup['is_active']): ?>
                                                <span class="badge-tag badge-tag-emerald"><i class="fas fa-check-circle me-1"></i> Active PBF</span>
                                            <?php else: ?>
                                                <span class="badge-tag badge-tag-crimson"><i class="fas fa-ban me-1"></i> Inactive</span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="h5 fw-bold text-dark mb-2"><?= htmlspecialchars($sup['name']) ?></h3>
                                        
                                        <div class="small text-secondary mb-1">
                                            <i class="fas fa-user-tie text-muted me-1.5" style="width: 16px;"></i> Contact: <strong><?= htmlspecialchars($sup['contact_person'] ?? '-') ?></strong>
                                        </div>
                                        <div class="small text-secondary mb-1 font-mono">
                                            <i class="fas fa-phone text-muted me-1.5" style="width: 16px;"></i> <?= htmlspecialchars($sup['phone'] ?? '-') ?>
                                        </div>
                                        <div class="small text-secondary mb-1 font-mono">
                                            <i class="fas fa-envelope text-muted me-1.5" style="width: 16px;"></i> <?= htmlspecialchars($sup['email'] ?? '-') ?>
                                        </div>
                                        <div class="small text-muted mb-3">
                                            <i class="fas fa-map-marker-alt text-muted me-1.5" style="width: 16px;"></i> <?= htmlspecialchars($sup['address'] ?? '-') ?>
                                        </div>

                                        <!-- Procurement stats -->
                                        <div class="p-2.5 bg-light rounded border mb-3">
                                            <div class="row g-2 text-center font-mono" style="font-size: 0.72rem;">
                                                <div class="col-6 border-end">
                                                    <div class="text-muted">Total Orders:</div>
                                                    <div class="fw-bold text-dark fs-6"><?= number_format($sup['total_po_count'] ?? 0) ?> PO</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted">Outstanding AP:</div>
                                                    <div class="fw-bold <?= ($sup['total_ap_outstanding'] ?? 0) > 0 ? 'text-danger' : 'text-teal' ?> fs-6">
                                                        Rp <?= number_format($sup['total_ap_outstanding'] ?? 0, 0, ',', '.') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top gap-2">
                                        <a href="/purchasing/create?supplier_id=<?= $sup['id'] ?>" class="btn btn-sm btn-outline-teal flex-fill" title="Create Purchase Order for this PBF">
                                            <i class="fas fa-cart-plus me-1"></i> Order (SP)
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-dark px-2.5" onclick='openEditModal(<?= json_encode($sup, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="Edit Supplier Details">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger px-2.5" onclick="openDeleteModal(<?= $sup['id'] ?>, '<?= addslashes($sup['name']) ?>')" title="Remove Supplier">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-truck-loading text-teal me-1"></i> Add New Pharmaceutical Supplier (PBF)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSupplierForm" onsubmit="submitAddSupplier(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-bold">PBF Code *</label>
                                <input type="text" name="code" class="form-control form-control-sm font-mono text-uppercase" value="<?= htmlspecialchars($nextCode) ?>" required>
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="form-label small fw-bold">Supplier / PBF Name *</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. PT Anugrah Argon Medica" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control form-control-sm" placeholder="e.g. Budi Santoso (Sales)">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-sm font-mono" placeholder="e.g. 021-8889999 / 0812...">
                            </div>

                            <div class="col-12">
                                <label class="form-label small">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-sm font-mono" placeholder="e.g. order@distributor.com">
                            </div>

                            <div class="col-12">
                                <label class="form-label small">Warehouse / Office Address</label>
                                <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="Full address for shipping & invoices..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="addSupplierBtn">
                            <i class="fas fa-save me-1"></i> Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-edit text-teal me-1"></i> Edit Supplier (PBF)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSupplierForm" onsubmit="submitEditSupplier(event)">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-bold">PBF Code</label>
                                <input type="text" name="code" id="edit_code" class="form-control form-control-sm font-mono text-uppercase" readonly disabled>
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="form-label small fw-bold">Supplier / PBF Name *</label>
                                <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small">Contact Person</label>
                                <input type="text" name="contact_person" id="edit_contact" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control form-control-sm font-mono">
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label small">Email Address</label>
                                <input type="email" name="email" id="edit_email" class="form-control form-control-sm font-mono">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="is_active" id="edit_is_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small">Warehouse / Office Address</label>
                                <textarea name="address" id="edit_address" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="editSupplierBtn">
                            <i class="fas fa-save me-1"></i> Update Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(sup) {
            document.getElementById('edit_id').value = sup.id;
            document.getElementById('edit_code').value = sup.code;
            document.getElementById('edit_name').value = sup.name || '';
            document.getElementById('edit_contact').value = sup.contact_person || '';
            document.getElementById('edit_phone').value = sup.phone || '';
            document.getElementById('edit_email').value = sup.email || '';
            document.getElementById('edit_address').value = sup.address || '';
            document.getElementById('edit_is_active').value = sup.is_active ? '1' : '0';

            const modal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
            modal.show();
        }

        async function submitAddSupplier(e) {
            e.preventDefault();
            const btn = document.getElementById('addSupplierBtn');
            const alertBox = document.getElementById('supplierAlert');
            const form = document.getElementById('addSupplierForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            try {
                const res = await fetch('/inventory/suppliers', {
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
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Supplier';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Supplier';
            }
        }

        async function submitEditSupplier(e) {
            e.preventDefault();
            const btn = document.getElementById('editSupplierBtn');
            const alertBox = document.getElementById('supplierAlert');
            const form = document.getElementById('editSupplierForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

            try {
                const res = await fetch('/inventory/suppliers/update', {
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
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Supplier';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger">Network error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Supplier';
            }
        }

        async function openDeleteModal(id, name) {
            if (!confirm(`Are you sure you want to remove or deactivate supplier "${name}"?`)) {
                return;
            }

            const alertBox = document.getElementById('supplierAlert');

            try {
                const res = await fetch('/inventory/suppliers/delete', {
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
