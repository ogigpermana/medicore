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
                <div id="soAlert"></div>

                <div class="card-modern p-4 p-md-5 max-w-700 mx-auto">
                    <div class="pb-3 mb-4 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($nextNumber) ?></span>
                            <h1 class="h5 fw-bold text-dark mb-0">Initialize Stock Opname Session</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Snapshots current ERP inventory quantities and creates a physical counting sheet.
                        </p>
                    </div>

                    <form id="startOpnameForm" onsubmit="submitStartOpname(event)">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Session Title / Scope *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Stock Opname Bulanan Etalase & Gudang Utama" value="Stock Opname <?= date('F Y') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Target Category Filter</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- All Categories (Full Pharmacy Audit) --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small">Select a specific category if performing a partial/rolling cycle count.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">Audit Notes / Location Instructions</label>
                            <textarea name="notes" class="form-control small" rows="3" placeholder="e.g. Pastikan obat sirup dan tablet di rak A1-A5 dihitung teliti..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/stock-opname" class="btn btn-outline-dark btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="startBtn">
                                <i class="fas fa-play me-1"></i> Start Counting Session
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        async function submitStartOpname(e) {
            e.preventDefault();
            const btn = document.getElementById('startBtn');
            const alertBox = document.getElementById('soAlert');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Initializing...';

            const form = document.getElementById('startOpnameForm');
            const formData = new FormData(form);

            const payload = {
                title: formData.get('title'),
                category_id: formData.get('category_id'),
                notes: formData.get('notes')
            };

            try {
                const res = await fetch('/stock-opname', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.href = result.redirect_url || '/stock-opname';
                    }, 600);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-play me-1"></i> Start Counting Session';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-play me-1"></i> Start Counting Session';
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
