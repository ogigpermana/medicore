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
                    <a href="/stock-opname" class="sidebar-menu-link active">
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
                <div id="countAlert"></div>

                <form id="countingSheetForm" onsubmit="submitCountingSheet(event)">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($so['opname_number']) ?></span>
                                <h1 class="h4 fw-bold text-dark mb-0"><?= htmlspecialchars($so['title']) ?></h1>
                            </div>
                            <p class="text-muted small mb-0">
                                Enter physical inventory counts. Discrepancies and variances will be highlighted automatically.
                            </p>
                        </div>

                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="/stock-opname/<?= $so['id'] ?>" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold flex-fill flex-md-grow-0" id="saveCountsBtn">
                                <i class="fas fa-save me-1"></i> Save Physical Counts
                            </button>
                        </div>
                    </div>

                    <!-- Counting Matrix Card -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center justify-content-between">
                            <div><i class="fas fa-boxes-packing text-teal me-1"></i> Medication Items Count Sheet</div>
                            <span class="text-muted small"><?= count($so['items']) ?> items</span>
                        </div>

                        <!-- Mobile Card List (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($so['items'] as $index => $it): ?>
                                <div class="p-3 bg-light rounded border mb-3 so-item-element so-mobile-item" id="so-mob-<?= $it['id'] ?>" data-item-id="<?= $it['id'] ?>">
                                    <input type="hidden" class="so-id" value="<?= $it['id'] ?>">
                                    <input type="hidden" class="so-sys-qty" value="<?= $it['system_qty'] ?>">
                                    <input type="hidden" class="so-buy-price" value="<?= $it['buy_price'] ?>">

                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <div class="text-muted font-mono small"><?= htmlspecialchars($it['sku']) ?></div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted">System: <strong><?= $it['system_qty'] ?></strong></div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <label class="form-label small mb-0 fw-bold">Physical Count *</label>
                                            <input type="number" class="form-control form-control-sm font-mono fs-6 fw-bold so-phys-input" value="<?= $it['physical_qty'] ?>" min="0" oninput="recalcRow(<?= $it['id'] ?>)" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-0 text-muted">Variance</label>
                                            <div class="form-control form-control-sm font-mono fw-bold so-variance-badge">0</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small mb-0 text-muted">Reason for Discrepancy</label>
                                            <select class="form-select form-select-sm so-reason-select">
                                                <option value="matched" <?= $it['adjustment_reason'] === 'matched' ? 'selected' : '' ?>>Matched (Exact Count)</option>
                                                <option value="damaged" <?= $it['adjustment_reason'] === 'damaged' ? 'selected' : '' ?>>Damaged / Broken Packaging</option>
                                                <option value="lost" <?= $it['adjustment_reason'] === 'lost' ? 'selected' : '' ?>>Lost / Unaccounted Shortage</option>
                                                <option value="expired" <?= $it['adjustment_reason'] === 'expired' ? 'selected' : '' ?>>Expired / Past Shelf Life</option>
                                                <option value="count_error" <?= $it['adjustment_reason'] === 'count_error' ? 'selected' : '' ?>>Count Error / Miscount</option>
                                                <option value="bonus_sample" <?= $it['adjustment_reason'] === 'bonus_sample' ? 'selected' : '' ?>>Bonus Sample from Supplier</option>
                                            </select>
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
                                        <th class="text-center" style="width: 110px;">SYSTEM QTY</th>
                                        <th style="width: 140px;">PHYSICAL COUNT *</th>
                                        <th class="text-center" style="width: 110px;">VARIANCE</th>
                                        <th style="width: 220px;">DISCREPANCY REASON</th>
                                        <th>AUDIT NOTES</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($so['items'] as $it): ?>
                                        <tr class="so-item-element so-desktop-row" id="so-desk-<?= $it['id'] ?>" data-item-id="<?= $it['id'] ?>">
                                            <td>
                                                <input type="hidden" class="so-id" value="<?= $it['id'] ?>">
                                                <input type="hidden" class="so-sys-qty" value="<?= $it['system_qty'] ?>">
                                                <input type="hidden" class="so-buy-price" value="<?= $it['buy_price'] ?>">
                                                
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                            </td>
                                            <td class="text-center font-bold">
                                                <strong><?= $it['system_qty'] ?></strong> <?= htmlspecialchars($it['unit_symbol'] ?? '') ?>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm font-mono fs-6 fw-bold text-teal so-phys-input" value="<?= $it['physical_qty'] ?>" min="0" oninput="recalcRow(<?= $it['id'] ?>)" required>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-tag badge-tag-dark font-mono so-variance-badge">0</span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm so-reason-select">
                                                    <option value="matched" <?= $it['adjustment_reason'] === 'matched' ? 'selected' : '' ?>>Matched (Exact Count)</option>
                                                    <option value="damaged" <?= $it['adjustment_reason'] === 'damaged' ? 'selected' : '' ?>>Damaged / Broken</option>
                                                    <option value="lost" <?= $it['adjustment_reason'] === 'lost' ? 'selected' : '' ?>>Lost / Shortage</option>
                                                    <option value="expired" <?= $it['adjustment_reason'] === 'expired' ? 'selected' : '' ?>>Expired / Past Date</option>
                                                    <option value="count_error" <?= $it['adjustment_reason'] === 'count_error' ? 'selected' : '' ?>>Count Error / Miscount</option>
                                                    <option value="bonus_sample" <?= $it['adjustment_reason'] === 'bonus_sample' ? 'selected' : '' ?>>Supplier Bonus Sample</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm so-notes-input" placeholder="Optional remark" value="<?= htmlspecialchars($it['notes'] ?? '') ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Calculation Banner -->
                        <div class="bg-light p-3 p-sm-4 rounded border mt-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Total System Recorded Qty:</div>
                                    <div class="fs-5 fw-bold text-dark font-mono" id="dispTotalSys">0</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Total Physically Counted:</div>
                                    <div class="fs-5 fw-bold text-teal font-mono" id="dispTotalPhys">0</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="text-muted small">Net Discrepancy Variance:</div>
                                    <div class="fs-5 fw-bold font-mono" id="dispTotalVariance">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        function recalcRow(itemId) {
            const isDesktop = window.innerWidth >= 768;
            const container = isDesktop ? document.getElementById(`so-desk-${itemId}`) : document.getElementById(`so-mob-${itemId}`);
            if (!container) return;

            const sys = parseInt(container.querySelector('.so-sys-qty').value || 0);
            const phys = parseInt(container.querySelector('.so-phys-input').value || 0);
            const variance = phys - sys;

            const badge = container.querySelector('.so-variance-badge');
            if (badge) {
                badge.textContent = (variance > 0 ? '+' : '') + variance;
                badge.className = 'so-variance-badge ' + (isDesktop ? 'badge-tag ' : 'form-control form-control-sm font-mono fw-bold ') +
                    (variance < 0 ? 'badge-tag-crimson text-danger' : (variance > 0 ? 'badge-tag-teal text-teal' : 'badge-tag-dark text-dark'));
            }

            const reasonSelect = container.querySelector('.so-reason-select');
            if (reasonSelect) {
                if (variance < 0 && reasonSelect.value === 'matched') {
                    reasonSelect.value = 'damaged';
                } else if (variance === 0) {
                    reasonSelect.value = 'matched';
                }
            }

            updateGrandTotals();
        }

        function updateGrandTotals() {
            let totalSys = 0;
            let totalPhys = 0;
            const isDesktop = window.innerWidth >= 768;
            const elements = isDesktop ? document.querySelectorAll('.so-desktop-row') : document.querySelectorAll('.so-mobile-item');

            elements.forEach(el => {
                const sys = parseInt(el.querySelector('.so-sys-qty').value || 0);
                const phys = parseInt(el.querySelector('.so-phys-input').value || 0);
                totalSys += sys;
                totalPhys += phys;
            });

            const netVariance = totalPhys - totalSys;
            document.getElementById('dispTotalSys').textContent = totalSys.toLocaleString('id-ID');
            document.getElementById('dispTotalPhys').textContent = totalPhys.toLocaleString('id-ID');
            
            const varEl = document.getElementById('dispTotalVariance');
            varEl.textContent = (netVariance > 0 ? '+' : '') + netVariance.toLocaleString('id-ID') + ' units';
            varEl.className = 'fs-5 fw-bold font-mono ' + (netVariance < 0 ? 'text-danger' : (netVariance > 0 ? 'text-teal' : 'text-dark'));
        }

        async function submitCountingSheet(e) {
            e.preventDefault();
            const btn = document.getElementById('saveCountsBtn');
            const alertBox = document.getElementById('countAlert');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving Counts...';

            const isDesktop = window.innerWidth >= 768;
            const elements = isDesktop ? document.querySelectorAll('.so-desktop-row') : document.querySelectorAll('.so-mobile-item');
            const items = [];

            elements.forEach(el => {
                const id = el.querySelector('.so-id').value;
                const sys = el.querySelector('.so-sys-qty').value;
                const phys = el.querySelector('.so-phys-input').value;
                const price = el.querySelector('.so-buy-price').value;
                const reason = el.querySelector('.so-reason-select').value;
                const notes = el.querySelector('.so-notes-input') ? el.querySelector('.so-notes-input').value : '';

                items.push({
                    id: parseInt(id),
                    system_qty: parseInt(sys),
                    physical_qty: parseInt(phys),
                    buy_price: parseFloat(price),
                    adjustment_reason: reason,
                    notes: notes
                });
            });

            const payload = {
                stock_opname_id: <?= (int)$so['id'] ?>,
                items: items
            };

            try {
                const res = await fetch('/stock-opname/save-counts', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '/stock-opname/<?= $so['id'] ?>';
                    }, 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Physical Counts';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Physical Counts';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isDesktop = window.innerWidth >= 768;
            const elements = isDesktop ? document.querySelectorAll('.so-desktop-row') : document.querySelectorAll('.so-mobile-item');
            elements.forEach(el => {
                const id = el.getAttribute('data-item-id');
                recalcRow(id);
            });
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
