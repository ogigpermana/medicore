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
                <div id="transferAlert"></div>

                <form id="createTransferForm" onsubmit="submitTransfer(event)">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($nextNumber) ?></span>
                                <h1 class="h4 fw-bold text-dark mb-0">Create Inter-Branch Stock Transfer</h1>
                            </div>
                            <p class="text-muted small mb-0">
                                Dispatch medication inventory from central distribution warehouse to retail branches.
                            </p>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="/transfers" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold flex-fill flex-md-grow-0" id="submitTransferBtn">
                                <i class="fas fa-paper-plane me-1"></i> Submit Transfer Request
                            </button>
                        </div>
                    </div>

                    <!-- Branch Routing Selection Card -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                            <i class="fas fa-map-location-dot text-teal"></i> Transfer Routing & Scope
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Source Location (Sender) *</label>
                                <select name="source_branch_id" id="sourceBranch" class="form-select" required>
                                    <?php foreach ($branches as $b): ?>
                                        <option value="<?= $b['id'] ?>" <?= ($b['type'] === 'warehouse' || $b['code'] === 'CB-PST') ? 'selected' : '' ?>>
                                            [<?= htmlspecialchars($b['code']) ?>] <?= htmlspecialchars($b['name']) ?> (<?= ucfirst(str_replace('_', ' ', $b['type'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Destination Branch (Recipient) *</label>
                                <select name="destination_branch_id" id="destBranch" class="form-select" required>
                                    <?php foreach ($branches as $b): ?>
                                        <option value="<?= $b['id'] ?>" <?= ($b['code'] === 'CB-BRT') ? 'selected' : '' ?>>
                                            [<?= htmlspecialchars($b['code']) ?>] <?= htmlspecialchars($b['name']) ?> (<?= ucfirst(str_replace('_', ' ', $b['type'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small text-muted">Logistics / Shipping Instructions</label>
                                <input type="text" name="shipping_notes" class="form-control form-control-sm" placeholder="e.g. Urgent morning delivery via refrigerated logistics vehicle...">
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Line Items Card -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between">
                            <div><i class="fas fa-boxes text-teal me-1"></i> Medication Items to Transfer</div>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="addTransferRow()">
                                <i class="fas fa-plus me-1"></i> Add Medication Line
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 font-mono small" id="transferItemsTable">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th style="min-width: 250px;">MEDICATION PRODUCT *</th>
                                        <th style="width: 140px;" class="text-center">QTY TO TRANSFER *</th>
                                        <th>NOTES / REMARK</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="transferItemsBody" style="font-size: 0.825rem;">
                                    <!-- Dynamic rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Product list JSON for JavaScript -->
    <script>
        const catalogProducts = <?= json_encode($products) ?>;

        function addTransferRow() {
            const tbody = document.getElementById('transferItemsBody');
            const rowId = 'trf-row-' + Date.now() + Math.random().toString(36).substring(2, 5);

            let optionsHtml = '<option value="">-- Select Medication --</option>';
            catalogProducts.forEach(p => {
                optionsHtml += `<option value="${p.id}" data-price="${p.buy_price || 0}" data-stock="${p.stock_quantity || 0}">[${p.sku}] ${p.name} (Stock: ${p.stock_quantity || 0})</option>`;
            });

            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = 'trf-item-row';
            tr.innerHTML = `
                <td>
                    <select class="form-select form-select-sm font-sans trf-prod-select" onchange="updateRowProduct('${rowId}')" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm font-mono text-center fs-6 fw-bold text-teal trf-qty-input" min="1" value="10" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm font-sans trf-notes-input" placeholder="e.g. Original seal intact">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2" onclick="removeTransferRow('${rowId}')" title="Remove line">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        }

        function removeTransferRow(rowId) {
            const tr = document.getElementById(rowId);
            if (tr) {
                tr.remove();
                if (document.querySelectorAll('.trf-item-row').length === 0) {
                    addTransferRow();
                }
            }
        }

        function updateRowProduct(rowId) {
            // Optional: update stock info display
        }

        async function submitTransfer(e) {
            e.preventDefault();
            const btn = document.getElementById('submitTransferBtn');
            const alertBox = document.getElementById('transferAlert');

            const sourceBranch = document.getElementById('sourceBranch').value;
            const destBranch = document.getElementById('destBranch').value;
            const notes = document.querySelector('input[name="shipping_notes"]').value;

            if (sourceBranch === destBranch) {
                alertBox.innerHTML = '<div class="alert alert-danger">Source and destination branches cannot be the same!</div>';
                return;
            }

            const rows = document.querySelectorAll('.trf-item-row');
            const items = [];

            rows.forEach(r => {
                const prodSelect = r.querySelector('.trf-prod-select');
                const qtyInput = r.querySelector('.trf-qty-input');
                const notesInput = r.querySelector('.trf-notes-input');

                if (prodSelect.value) {
                    const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
                    const price = selectedOpt.getAttribute('data-price') || 0;

                    items.push({
                        product_id: parseInt(prodSelect.value),
                        qty_requested: parseInt(qtyInput.value || 1),
                        unit_buy_price: parseFloat(price),
                        notes: notesInput.value || null
                    });
                }
            });

            if (items.length === 0) {
                alertBox.innerHTML = '<div class="alert alert-danger">Please select at least one medication product to transfer.</div>';
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

            const payload = {
                source_branch_id: parseInt(sourceBranch),
                destination_branch_id: parseInt(destBranch),
                shipping_notes: notes,
                items: items
            };

            try {
                const res = await fetch('/transfers', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '/transfers';
                    }, 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Transfer Request';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Transfer Request';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            addTransferRow();
            if (catalogProducts.length > 1) {
                addTransferRow();
            }
        });

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
