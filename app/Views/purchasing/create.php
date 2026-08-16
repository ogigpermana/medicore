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
                <div id="poAlertBox"></div>

                <form id="newPoForm" onsubmit="submitPurchaseOrder(event)">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-tag badge-tag-teal font-mono" id="poNumberBadge"><?= htmlspecialchars($nextPoNumber) ?></span>
                                <h1 class="h4 fw-bold text-dark mb-0">Create BPOM Surat Pesanan (PO)</h1>
                            </div>
                            <p class="text-muted small mb-0">
                                Authorize official procurement orders to pharmaceutical distributors (PBF) under Apoteker Penanggung Jawab SIPA.
                            </p>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="/purchasing" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold flex-fill flex-md-grow-0" id="submitPoBtn">
                                <i class="fas fa-save me-1"></i> Issue Surat Pesanan
                            </button>
                        </div>
                    </div>

                    <!-- Header Form Metadata -->
                    <div class="row g-3 g-md-4 mb-4">
                        <!-- PBF & SP Type Card -->
                        <div class="col-12 col-lg-6">
                            <div class="card-modern p-3 p-sm-4 h-100">
                                <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                                    <i class="fas fa-truck-loading text-teal"></i> 1. Distributor (PBF) & SP Classification
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Distributor PBF *</label>
                                        <select name="supplier_id" id="supplierSelect" class="form-select" required>
                                            <option value="">-- Choose PBF Distributor --</option>
                                            <?php foreach ($suppliers as $sup): ?>
                                                <option value="<?= $sup['id'] ?>" data-phone="<?= htmlspecialchars($sup['phone'] ?? '') ?>">
                                                    <?= htmlspecialchars($sup['name']) ?> (<?= htmlspecialchars($sup['code'] ?? 'PBF') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold">BPOM SP Classification *</label>
                                        <select name="sp_type" id="spTypeSelect" class="form-select" onchange="updateSpNumberPrefix()">
                                            <option value="regular">SP Reguler (Obat Bebas/Keras Non-Prekursor)</option>
                                            <option value="precursor">SP Prekursor Farmasi (Pseudoephedrine, etc.)</option>
                                            <option value="oot">SP Obat-Obat Tertentu (OOT)</option>
                                            <option value="narcotic_psychotropic">SP Narkotika & Psikotropika</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold">Payment Terms</label>
                                        <select name="payment_terms" class="form-select">
                                            <option value="net_30" selected>Net 30 Days (Kredit 30 Hari)</option>
                                            <option value="net_14">Net 14 Days (Kredit 14 Hari)</option>
                                            <option value="net_7">Net 7 Days (Kredit 1 Minggu)</option>
                                            <option value="net_60">Net 60 Days (Kredit 60 Hari)</option>
                                            <option value="cod">Cash on Delivery (COD / Tunai)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logistics & Pharmacist License -->
                        <div class="col-12 col-lg-6">
                            <div class="card-modern p-3 p-sm-4 h-100">
                                <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                                    <i class="fas fa-user-shield text-primary"></i> 2. Pharmacist License & Schedule
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold">Order Date *</label>
                                        <input type="date" name="order_date" class="form-control font-mono" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small">Expected Delivery Date</label>
                                        <input type="date" name="expected_delivery_date" class="form-control font-mono" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Responsible Pharmacist SIPA *</label>
                                        <input type="text" name="pharmacist_sipa" class="form-control font-mono" value="SIPA: 19880415/SIPA_32.73/2022/2001" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items Matrix -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                            <div class="fw-bold text-dark small">
                                <i class="fas fa-boxes text-teal me-1"></i> 3. Ordered Medications & Quantity
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="addPoItemRow()">
                                <i class="fas fa-plus me-1"></i> Add Medication Line
                            </button>
                        </div>

                        <div id="poItemsContainer">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Financial Summary Calculation Bar -->
                        <div class="bg-light p-3 p-sm-4 rounded border mt-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Procurement Notes / PO Remarks</label>
                                    <textarea name="notes" class="form-control small" rows="2" placeholder="e.g. Harap kirimkan bets dengan masa kedaluwarsa (ED) minimal > 18 bulan beserta Certificate of Analysis (CoA)..."></textarea>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Subtotal Items:</span>
                                        <span class="font-mono fw-bold text-dark" id="dispSubtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                        <span>PPN (11% Tax):</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" id="ppnCheck" checked onchange="calculatePoTotals()" class="form-check-input">
                                            <span class="font-mono fw-bold text-dark" id="dispTax">Rp 0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                        <span class="fw-bold text-dark">Estimated Grand Total:</span>
                                        <span class="fs-5 fw-bold text-teal font-mono" id="dispGrandTotal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Product Options Data in JS -->
    <script>
        const productsCatalog = <?= json_encode($products) ?>;
        let itemRowCounter = 0;

        function getProductOptionsHtml() {
            let html = '<option value="">-- Select Medication --</option>';
            productsCatalog.forEach(p => {
                const buyPrice = parseFloat(p.buy_price || 0);
                html += `<option value="${p.id}" data-price="${buyPrice}" data-sku="${p.sku}">
                    ${p.name} (SKU: ${p.sku}) - Rp ${buyPrice.toLocaleString('id-ID')}
                </option>`;
            });
            return html;
        }

        function addPoItemRow() {
            itemRowCounter++;
            const container = document.getElementById('poItemsContainer');
            const rowDiv = document.createElement('div');
            rowDiv.className = 'p-3 bg-light rounded border mb-2 po-item-row rx-form-item-row';
            rowDiv.id = `po-row-${itemRowCounter}`;
            rowDiv.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-muted mb-0">Medication Item *</label>
                            <button type="button" class="btn btn-sm text-danger p-0 d-md-none" onclick="document.getElementById('po-row-${itemRowCounter}').remove(); calculatePoTotals();">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </button>
                        </div>
                        <select class="form-select form-select-sm po-prod-select" onchange="onProductSelected(this, ${itemRowCounter})" required>
                            ${getProductOptionsHtml()}
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Order Qty *</label>
                        <input type="number" class="form-control form-control-sm font-mono po-qty-input" value="10" min="1" oninput="calculatePoTotals()" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Buy Price (Rp) *</label>
                        <input type="number" step="100" class="form-control form-control-sm font-mono po-price-input" value="0" min="0" oninput="calculatePoTotals()" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Line Subtotal</label>
                        <div class="form-control form-control-sm bg-white font-mono fw-bold text-teal po-line-subtotal">Rp 0</div>
                    </div>
                    <div class="col-6 col-md-1 d-none d-md-flex justify-content-end pt-md-3">
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="document.getElementById('po-row-${itemRowCounter}').remove(); calculatePoTotals();" title="Remove line">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(rowDiv);
            calculatePoTotals();
        }

        function onProductSelected(selectEl, rowId) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const price = parseFloat(selectedOpt.getAttribute('data-price') || 0);
            const row = document.getElementById(`po-row-${rowId}`);
            if (row) {
                row.querySelector('.po-price-input').value = price;
            }
            calculatePoTotals();
        }

        function calculatePoTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('.po-item-row');

            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('.po-qty-input').value || 0);
                const price = parseFloat(row.querySelector('.po-price-input').value || 0);
                const lineTotal = qty * price;
                row.querySelector('.po-line-subtotal').textContent = 'Rp ' + lineTotal.toLocaleString('id-ID');
                subtotal += lineTotal;
            });

            const hasPpn = document.getElementById('ppnCheck').checked;
            const tax = hasPpn ? (subtotal * 0.11) : 0;
            const grandTotal = subtotal + tax;

            document.getElementById('dispSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('dispTax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('dispGrandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

            return { subtotal, tax, grandTotal };
        }

        function updateSpNumberPrefix() {
            const spType = document.getElementById('spTypeSelect').value;
            const code = spType === 'precursor' ? 'SP-PRK' : (spType === 'oot' ? 'SP-OOT' : (spType === 'narcotic_psychotropic' ? 'SP-NKT' : 'SP-REG'));
            const dateStr = '<?= date('Ymd') ?>';
            document.getElementById('poNumberBadge').textContent = `${code}-${dateStr}-XXXX`;
        }

        async function submitPurchaseOrder(e) {
            e.preventDefault();
            const btn = document.getElementById('submitPoBtn');
            const alertBox = document.getElementById('poAlertBox');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving PO...';

            const form = document.getElementById('newPoForm');
            const formData = new FormData(form);

            // Collect items
            const rows = document.querySelectorAll('.po-item-row');
            const items = [];
            rows.forEach(r => {
                const prodId = r.querySelector('.po-prod-select').value;
                const qty = r.querySelector('.po-qty-input').value;
                const price = r.querySelector('.po-price-input').value;
                if (prodId && qty > 0) {
                    items.push({
                        product_id: parseInt(prodId),
                        quantity: parseInt(qty),
                        unit_price: parseFloat(price),
                        discount_percent: 0,
                        tax_percent: document.getElementById('ppnCheck').checked ? 11 : 0
                    });
                }
            });

            if (items.length === 0) {
                alertBox.innerHTML = '<div class="alert alert-danger">Please add at least one medication line item.</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Issue Surat Pesanan';
                return;
            }

            const { subtotal, tax, grandTotal } = calculatePoTotals();

            const payload = {
                supplier_id: formData.get('supplier_id'),
                sp_type: formData.get('sp_type'),
                payment_terms: formData.get('payment_terms'),
                order_date: formData.get('order_date'),
                expected_delivery_date: formData.get('expected_delivery_date'),
                pharmacist_sipa: formData.get('pharmacist_sipa'),
                notes: formData.get('notes'),
                subtotal: subtotal,
                tax_amount: tax,
                grand_total: grandTotal,
                items: items
            };

            try {
                const res = await fetch('/purchasing', {
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
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Issue Surat Pesanan';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Issue Surat Pesanan';
            }
        }

        // Initialize with 2 rows
        document.addEventListener('DOMContentLoaded', () => {
            addPoItemRow();
            addPoItemRow();
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
