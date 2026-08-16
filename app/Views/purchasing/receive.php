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
                    <a href="/purchasing" class="sidebar-menu-link active">
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
                <!-- Alert Box -->
                <div id="grAlertBox"></div>

                <form id="receiveGoodsForm" onsubmit="submitGoodsReceipt(event)">
                    <input type="hidden" name="purchase_order_id" value="<?= (int)$po['id'] ?>">
                    <input type="hidden" name="supplier_id" value="<?= (int)$po['supplier_id'] ?>">

                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($nextGrnNumber) ?></span>
                                <h1 class="h4 fw-bold text-dark mb-0">Goods Receipt Note (GRN) Verification</h1>
                            </div>
                            <p class="text-muted small mb-0">
                                Verify physical goods received against PO <strong><?= htmlspecialchars($po['po_number']) ?></strong> from <strong><?= htmlspecialchars($po['supplier_name']) ?></strong>.
                            </p>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="/purchasing/<?= $po['id'] ?>" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold flex-fill flex-md-grow-0" id="submitGrBtn">
                                <i class="fas fa-check-circle me-1"></i> Confirm & Update FEFO Stock
                            </button>
                        </div>
                    </div>

                    <!-- Invoice & Delivery Metadata Card -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice text-teal"></i> 1. PBF Invoice & Delivery Order Details
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold">Nomor Faktur PBF / Invoice No *</label>
                                <input type="text" name="invoice_number" class="form-control font-mono" placeholder="e.g. FAK-KF/202608/0942" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">Faktur Date *</label>
                                <input type="date" name="invoice_date" class="form-control font-mono" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">Payment Due Date (Jatuh Tempo) *</label>
                                <input type="date" name="due_date" class="form-control font-mono" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Receiving Items Matrix -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom">
                            <i class="fas fa-boxes text-teal me-1"></i> 2. Batch Number & Expiry Date Physical Check
                        </div>

                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Incoming items will automatically generate new <strong>FEFO Batches</strong> and increase inventory total stock levels.
                        </div>

                        <!-- Mobile Card List (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($po['items'] as $index => $it): ?>
                                <?php $remaining = max(0, $it['quantity_ordered'] - $it['quantity_received']); ?>
                                <div class="p-3 bg-light rounded border mb-3 gr-item-card" data-index="<?= $index ?>">
                                    <input type="hidden" class="gr-prod-id" value="<?= $it['product_id'] ?>">
                                    
                                    <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($it['product_name']) ?></div>
                                    <div class="text-muted font-mono small mb-2">SKU: <?= htmlspecialchars($it['sku']) ?> • Ordered: <?= $it['quantity_ordered'] ?> • Left: <?= $remaining ?></div>

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label small mb-0 text-muted">Batch Number (Nomor Bets) *</label>
                                            <input type="text" class="form-control form-control-sm font-mono gr-batch-input" placeholder="e.g. KF-2608-A" value="BT-<?= date('ymd') ?>-<?= $index + 1 ?>" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-0 text-muted">Expiry Date *</label>
                                            <input type="date" class="form-control form-control-sm font-mono gr-expiry-input" value="<?= date('Y-m-d', strtotime('+24 months')) ?>" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-0 text-muted">Qty Received *</label>
                                            <input type="number" class="form-control form-control-sm font-mono gr-qty-input" value="<?= $remaining ?>" min="0" max="<?= $remaining ?>" oninput="calculateGrnTotals()" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small mb-0 text-muted">Buy Price (Rp)</label>
                                            <input type="number" step="100" class="form-control form-control-sm font-mono gr-price-input" value="<?= (float)$it['unit_price'] ?>" oninput="calculateGrnTotals()" required>
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
                                        <th>MEDICATION & SKU</th>
                                        <th>ORDERED / LEFT</th>
                                        <th style="width: 170px;">BATCH NUMBER (BETS) *</th>
                                        <th style="width: 150px;">EXPIRY DATE *</th>
                                        <th style="width: 110px;">RECEIVE QTY *</th>
                                        <th style="width: 130px;">BUY PRICE (RP)</th>
                                        <th style="width: 130px;">LINE TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($po['items'] as $index => $it): ?>
                                        <?php $remaining = max(0, $it['quantity_ordered'] - $it['quantity_received']); ?>
                                        <tr class="gr-item-row" data-index="<?= $index ?>">
                                            <td>
                                                <input type="hidden" class="gr-prod-id" value="<?= $it['product_id'] ?>">
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                            </td>
                                            <td>
                                                <?= $it['quantity_ordered'] ?> / <span class="text-danger fw-bold"><?= $remaining ?></span>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm font-mono gr-batch-input" placeholder="KF-2608-A" value="BT-<?= date('ymd') ?>-<?= $index + 1 ?>" required>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm font-mono gr-expiry-input" value="<?= date('Y-m-d', strtotime('+24 months')) ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm font-mono gr-qty-input" value="<?= $remaining ?>" min="0" max="<?= $remaining ?>" oninput="calculateGrnTotals()" required>
                                            </td>
                                            <td>
                                                <input type="number" step="100" class="form-control form-control-sm font-mono gr-price-input" value="<?= (float)$it['unit_price'] ?>" oninput="calculateGrnTotals()" required>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-teal gr-line-subtotal">Rp 0</div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total & Notes Bar -->
                        <div class="bg-light p-3 p-sm-4 rounded border mt-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Receiving Notes / Condition Remarks</label>
                                    <textarea name="notes" class="form-control small" rows="2" placeholder="e.g. Segel utuh, suhu penyimpanan cold-chain sesuai standar (2-8°C)..."></textarea>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Subtotal Receiving:</span>
                                        <span class="font-mono fw-bold text-dark" id="dispGrSubtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                        <span>PPN (11% Tax):</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" id="grPpnCheck" checked onchange="calculateGrnTotals()" class="form-check-input">
                                            <span class="font-mono fw-bold text-dark" id="dispGrTax">Rp 0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2 fs-6">
                                        <span class="fw-bold text-dark">Invoice Total Due (AP):</span>
                                        <span class="fs-5 fw-bold text-teal font-mono" id="dispGrGrandTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        function calculateGrnTotals() {
            let subtotal = 0;
            // Support both desktop rows and mobile cards
            const elements = window.innerWidth >= 768 ? document.querySelectorAll('.gr-item-row') : document.querySelectorAll('.gr-item-card');

            elements.forEach(el => {
                const qty = parseFloat(el.querySelector('.gr-qty-input').value || 0);
                const price = parseFloat(el.querySelector('.gr-price-input').value || 0);
                const lineTotal = qty * price;
                const subtotalEl = el.querySelector('.gr-line-subtotal');
                if (subtotalEl) {
                    subtotalEl.textContent = 'Rp ' + lineTotal.toLocaleString('id-ID');
                }
                subtotal += lineTotal;
            });

            const hasPpn = document.getElementById('grPpnCheck').checked;
            const tax = hasPpn ? (subtotal * 0.11) : 0;
            const grandTotal = subtotal + tax;

            document.getElementById('dispGrSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('dispGrTax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('dispGrGrandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            return { subtotal, tax, grandTotal };
        }

        async function submitGoodsReceipt(e) {
            e.preventDefault();
            const btn = document.getElementById('submitGrBtn');
            const alertBox = document.getElementById('grAlertBox');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Stocking Batches...';

            const form = document.getElementById('receiveGoodsForm');
            const formData = new FormData(form);

            const isDesktop = window.innerWidth >= 768;
            const elements = isDesktop ? document.querySelectorAll('.gr-item-row') : document.querySelectorAll('.gr-item-card');
            const items = [];

            elements.forEach(el => {
                const prodId = el.querySelector('.gr-prod-id').value;
                const batchNo = el.querySelector('.gr-batch-input').value;
                const expiry = el.querySelector('.gr-expiry-input').value;
                const qty = el.querySelector('.gr-qty-input').value;
                const price = el.querySelector('.gr-price-input').value;

                if (prodId && qty > 0) {
                    items.push({
                        product_id: parseInt(prodId),
                        batch_number: batchNo.trim(),
                        expiry_date: expiry,
                        quantity_received: parseInt(qty),
                        buy_price: parseFloat(price)
                    });
                }
            });

            if (items.length === 0) {
                alertBox.innerHTML = '<div class="alert alert-danger">Please receive at least one quantity for the medication.</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm & Update FEFO Stock';
                return;
            }

            const { subtotal, tax, grandTotal } = calculateGrnTotals();

            const payload = {
                purchase_order_id: formData.get('purchase_order_id'),
                supplier_id: formData.get('supplier_id'),
                invoice_number: formData.get('invoice_number'),
                invoice_date: formData.get('invoice_date'),
                due_date: formData.get('due_date'),
                notes: formData.get('notes'),
                subtotal: subtotal,
                tax_amount: tax,
                total_amount: grandTotal,
                items: items
            };

            try {
                const res = await fetch('/purchasing/receive', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '/purchasing';
                    }, 800);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm & Update FEFO Stock';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm & Update FEFO Stock';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            calculateGrnTotals();
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
