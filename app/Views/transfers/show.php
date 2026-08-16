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

    $stBadge = match($transfer['status']) {
        'received' => 'badge-tag-emerald',
        'in_transit' => 'badge-tag-blue',
        'pending_approval' => 'badge-tag-amber',
        'cancelled', 'rejected' => 'badge-tag-crimson',
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

                <!-- Transfer Header -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag <?= $stBadge ?> text-uppercase font-mono">
                                <?= htmlspecialchars(str_replace('_', ' ', $transfer['status'])) ?>
                            </span>
                            <h1 class="h4 fw-bold text-dark mb-0 font-mono"><?= htmlspecialchars($transfer['transfer_number']) ?></h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Requested by <strong><?= htmlspecialchars($transfer['requester_name']) ?></strong> on <?= date('d F Y H:i', strtotime($transfer['created_at'])) ?>
                        </p>
                    </div>

                    <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                        <a href="/transfers/<?= $transfer['id'] ?>/print-surat-jalan" target="_blank" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                            <i class="fas fa-print me-1"></i> Print Delivery Note (A4)
                        </a>

                        <?php if (in_array($transfer['status'], ['draft', 'pending_approval'])): ?>
                            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#dispatchModal">
                                <i class="fas fa-truck-fast me-1"></i> Dispatch & Send
                            </button>
                        <?php elseif ($transfer['status'] === 'in_transit'): ?>
                            <button type="button" class="btn btn-success btn-sm px-3 fw-bold flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#receiveModal">
                                <i class="fas fa-clipboard-check me-1"></i> Receive & Verify (Branch)
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Shipment Route Card -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="card-modern p-3 p-sm-4 h-100 border-start border-4 border-dark">
                            <div class="text-muted small text-uppercase font-mono fw-bold mb-1">
                                <i class="fas fa-warehouse text-secondary me-1"></i> Source Location (Sender)
                            </div>
                            <div class="fs-6 fw-bold text-dark font-sans"><?= htmlspecialchars($transfer['source_branch_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($transfer['source_address']) ?></div>
                            <div class="text-muted font-mono mt-1" style="font-size: 0.72rem;">
                                APJ: <?= htmlspecialchars($transfer['source_apj'] ?? '-') ?> • Tel: <?= htmlspecialchars($transfer['source_phone'] ?? '-') ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card-modern p-3 p-sm-4 h-100 border-start border-4 border-teal">
                            <div class="text-muted small text-uppercase font-mono fw-bold mb-1">
                                <i class="fas fa-store text-teal me-1"></i> Destination Branch (Recipient)
                            </div>
                            <div class="fs-6 fw-bold text-teal font-sans"><?= htmlspecialchars($transfer['destination_branch_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($transfer['destination_address']) ?></div>
                            <div class="text-muted font-mono mt-1" style="font-size: 0.72rem;">
                                APJ: <?= htmlspecialchars($transfer['destination_apj'] ?? '-') ?> • Tel: <?= htmlspecialchars($transfer['destination_phone'] ?? '-') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logistics & Courier Info Banner -->
                <?php if ($transfer['driver_name'] || $transfer['shipping_notes']): ?>
                    <div class="p-3 bg-light rounded border mb-4 d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start align-items-md-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box-solid icon-box-blue rounded-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Courier & Delivery Vehicle</div>
                                <div class="text-muted font-mono small">
                                    Driver: <strong><?= htmlspecialchars($transfer['driver_name'] ?? 'Internal Staff') ?></strong> 
                                    (Plate: <strong><?= htmlspecialchars($transfer['vehicle_number'] ?? '-') ?></strong>)
                                </div>
                            </div>
                        </div>
                        <?php if ($transfer['shipping_notes']): ?>
                            <div class="text-muted small font-sans fst-italic">
                                "<?= htmlspecialchars($transfer['shipping_notes']) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Transfer Items Table -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between">
                        <div><i class="fas fa-boxes text-teal me-1"></i> Manifest of Transferred Medications</div>
                        <span class="badge-tag badge-tag-dark"><?= count($transfer['items']) ?> Items</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>MEDICATION & SKU</th>
                                    <th>BATCH / EXPIRY</th>
                                    <th class="text-center">QTY REQUESTED</th>
                                    <th class="text-center">QTY SENT</th>
                                    <th class="text-center">QTY RECEIVED</th>
                                    <th>NOTES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php foreach ($transfer['items'] as $it): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($it['batch_number']): ?>
                                                <span class="badge-tag badge-tag-teal"><?= htmlspecialchars($it['batch_number']) ?></span>
                                                <div class="text-muted" style="font-size: 0.68rem;">Exp: <?= date('d M Y', strtotime($it['expiry_date'])) ?></div>
                                            <?php else: ?>
                                                <span class="text-muted font-sans">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $it['qty_requested'] ?> <?= htmlspecialchars($it['unit_symbol'] ?? '') ?></td>
                                        <td class="text-center fw-bold text-dark"><?= $it['qty_sent'] ?></td>
                                        <td class="text-center fw-bold <?= $it['qty_received'] > 0 ? 'text-teal' : 'text-muted' ?>">
                                            <?= $it['qty_received'] ?>
                                        </td>
                                        <td class="text-muted font-sans" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($it['notes'] ?? '-') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-truck-fast text-teal me-1"></i> Dispatch Stock Transfer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="dispatchForm" onsubmit="submitDispatch(event)">
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Confirming dispatch will deduct <?= $transfer['total_qty_sent'] ?> units of medication from source branch inventory and change status to <strong>In-Transit</strong>.
                        </p>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Driver / Courier Name *</label>
                            <input type="text" name="driver_name" class="form-control form-control-sm" placeholder="e.g. Ahmad Kurir" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Vehicle Registration Plate *</label>
                            <input type="text" name="vehicle_number" class="form-control form-control-sm font-mono text-uppercase" placeholder="e.g. B 1234 FAR" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small text-muted">Additional Logistics Notes</label>
                            <input type="text" name="shipping_notes" class="form-control form-control-sm" placeholder="Optional notes for receiving branch">
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold" id="dispatchBtn">
                            <i class="fas fa-paper-plane me-1"></i> Confirm Dispatch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receive Modal -->
    <div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-clipboard-check text-success me-1"></i> Receive & Verify Transfer Items
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="receiveForm" onsubmit="submitReceive(event)">
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Verify the physical quantities received at destination branch <strong><?= htmlspecialchars($transfer['destination_branch_name']) ?></strong>.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>MEDICATION</th>
                                        <th class="text-center" style="width: 100px;">QTY SENT</th>
                                        <th style="width: 130px;">QTY RECEIVED *</th>
                                        <th>NOTES / CONDITION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transfer['items'] as $it): ?>
                                        <tr class="rec-item-row" data-item-id="<?= $it['id'] ?>">
                                            <td>
                                                <input type="hidden" class="rec-id" value="<?= $it['id'] ?>">
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                            </td>
                                            <td class="text-center font-bold text-dark"><?= $it['qty_sent'] ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm font-mono text-center fs-6 fw-bold text-teal rec-qty-input" value="<?= $it['qty_sent'] ?>" min="0" max="<?= $it['qty_sent'] ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm font-sans rec-notes-input" placeholder="Good condition & sealed" value="Good condition & sealed">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="receiveBtn">
                            <i class="fas fa-check-circle me-1"></i> Accept & Credit Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function submitDispatch(e) {
            e.preventDefault();
            const btn = document.getElementById('dispatchBtn');
            const alertBox = document.getElementById('transferAlert');
            const form = document.getElementById('dispatchForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Dispatching...';

            const payload = {
                transfer_id: <?= (int)$transfer['id'] ?>,
                driver_name: formData.get('driver_name'),
                vehicle_number: formData.get('vehicle_number'),
                shipping_notes: formData.get('shipping_notes')
            };

            try {
                const res = await fetch('/transfers/dispatch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Confirm Dispatch';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Confirm Dispatch';
            }
        }

        async function submitReceive(e) {
            e.preventDefault();
            const btn = document.getElementById('receiveBtn');
            const alertBox = document.getElementById('transferAlert');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing Receipt...';

            const rows = document.querySelectorAll('.rec-item-row');
            const items = [];

            rows.forEach(r => {
                const id = r.querySelector('.rec-id').value;
                const qty = r.querySelector('.rec-qty-input').value;
                const notes = r.querySelector('.rec-notes-input').value;

                items.push({
                    id: parseInt(id),
                    qty_received: parseInt(qty),
                    notes: notes
                });
            });

            const payload = {
                transfer_id: <?= (int)$transfer['id'] ?>,
                items: items
            };

            try {
                const res = await fetch('/transfers/receive', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Accept & Credit Inventory';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Accept & Credit Inventory';
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
