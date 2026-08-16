<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Medication Categories — MediCore' ?></title>
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
                    <a href="/inventory/fefo" class="sidebar-menu-link">
                        <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                    </a>
                <?php endif; ?>

                <!-- Master Data (Visible to superadmin, warehouse, owner) -->
                <?php if (in_array($currentRole, ['superadmin', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Master Data</div>
                    <a href="/inventory/categories" class="sidebar-menu-link active">
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

        <div class="app-main">
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
                <!-- Page Flash Alert Container -->
                <div id="flashAlertContainer"></div>

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-tags me-1"></i> Classification</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Medication Classifications & Categories</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Create, update, and manage drug classifications, therapeutic tags, and prescription restrictions.
                        </p>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddCategoryModal()">
                        <i class="fas fa-plus-circle me-1"></i> Add Category
                    </button>
                </div>

                <div class="row g-3 g-md-4">
                    <?php foreach ($categories as $cat): ?>
                        <div class="col-12 col-md-6 col-lg-4" id="cat-card-<?= $cat['id'] ?>">
                            <div class="card-modern p-4 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge-tag badge-tag-dark font-mono"><?= htmlspecialchars($cat['slug']) ?></span>
                                        <?php if ($cat['requires_prescription']): ?>
                                            <span class="badge-tag badge-tag-crimson"><i class="fas fa-file-medical me-1"></i> Rx Required</span>
                                        <?php else: ?>
                                            <span class="badge-tag badge-tag-emerald"><i class="fas fa-check-circle me-1"></i> OTC / Non-Rx</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="h5 fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h3>
                                    <p class="text-secondary small mb-3"><?= htmlspecialchars($cat['description'] ?? 'No description provided.') ?></p>
                                </div>
                                <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><strong><?= $cat['product_count'] ?></strong> items</span>
                                    <div class="d-flex gap-1.5">
                                        <button class="btn btn-outline-dark btn-sm py-1 px-2" style="font-size: 0.75rem;" 
                                                onclick="showEditCategoryModal(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size: 0.75rem;" 
                                                onclick="deleteCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>', <?= $cat['product_count'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal: Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body p-4">
                        <div id="addCatModalAlert"></div>
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Dermatologicals & Topical" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Therapeutic purpose and indications..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_prescription" value="1" id="addCatRx">
                            <label class="form-check-label small" for="addCatRx">
                                Requires Doctor Prescription (Ethical / Rx Classification)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveCatBtn">
                            <i class="fas fa-save me-1"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm">
                    <input type="hidden" name="id" id="editCatId">
                    <div class="modal-body p-4">
                        <div id="editCatModalAlert"></div>
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="name" id="editCatName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editCatDescription" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requires_prescription" value="1" id="editCatRx">
                            <label class="form-check-label small" for="editCatRx">
                                Requires Doctor Prescription (Ethical / Rx Classification)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="updateCatBtn">
                            <i class="fas fa-save me-1"></i> Update Changes
                        </button>
                    </div>
                </form>
            </div>
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

        function showFlash(type, message) {
            const container = document.getElementById('flashAlertContainer');
            container.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show mb-4 small" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-1"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showAddCategoryModal() {
            document.getElementById('addCategoryForm').reset();
            document.getElementById('addCatModalAlert').innerHTML = '';
            new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
        }

        function showEditCategoryModal(cat) {
            document.getElementById('editCatId').value = cat.id;
            document.getElementById('editCatName').value = cat.name;
            document.getElementById('editCatDescription').value = cat.description || '';
            document.getElementById('editCatRx').checked = (cat.requires_prescription == 1);
            document.getElementById('editCatModalAlert').innerHTML = '';
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }

        // Add Category Submit
        document.getElementById('addCategoryForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveCatBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const res = await fetch('/inventory/categories', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                    showFlash('success', result.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    document.getElementById('addCatModalAlert').innerHTML = `<div class="alert alert-danger py-2 small">${result.message}</div>`;
                }
            } catch (err) {
                document.getElementById('addCatModalAlert').innerHTML = `<div class="alert alert-danger py-2 small">Error: ${err.message}</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Category';
            }
        });

        // Edit Category Submit
        document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('updateCatBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const res = await fetch('/inventory/categories/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
                    showFlash('success', result.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    document.getElementById('editCatModalAlert').innerHTML = `<div class="alert alert-danger py-2 small">${result.message}</div>`;
                }
            } catch (err) {
                document.getElementById('editCatModalAlert').innerHTML = `<div class="alert alert-danger py-2 small">Error: ${err.message}</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Changes';
            }
        });

        // Delete Category
        async function deleteCategory(id, name, count) {
            if (count > 0) {
                alert(`Cannot delete '${name}' because it contains ${count} active medications. Please reassign the medications first.`);
                return;
            }

            if (!confirm(`Are you sure you want to delete category '${name}'?`)) return;

            try {
                const res = await fetch('/inventory/categories/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ id: id })
                });
                const result = await res.json();

                if (result.success) {
                    showFlash('success', result.message);
                    const el = document.getElementById(`cat-card-${id}`);
                    if (el) el.remove();
                } else {
                    showFlash('danger', result.message);
                }
            } catch (err) {
                showFlash('danger', `Network error: ${err.message}`);
            }
        }
    </script>
</body>
</html>
