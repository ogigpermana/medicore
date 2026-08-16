<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Medication & Inventory Catalog — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">

<?php
    $currentRole = strtolower($role ?? $user['role_name'] ?? $user['role'] ?? 'pharmacist');
    $currentName = $user['full_name'] ?? $user['name'] ?? 'Staff User';
?>
    <!-- Mobile Sidebar Backdrop -->
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
                    <a href="/inventory/products" class="sidebar-menu-link active">
                        <i class="fas fa-boxes"></i> <span>Medications</span>
                    </a>
                    <a href="/inventory/fefo" class="sidebar-menu-link">
                        <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                    </a>
                <?php endif; ?>

                <!-- Master Data (Visible to superadmin, warehouse, owner) -->
                <?php if (in_array($currentRole, ['superadmin', 'warehouse', 'owner'])): ?>
                    <div class="sidebar-section-label mt-2">Master Data</div>
                    <a href="/inventory/categories" class="sidebar-menu-link">
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

            <!-- Catalog Content -->
            <main class="app-content">
                <!-- Flash Alert Box -->
                <div id="catalogFlashAlert"></div>

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal"><i class="fas fa-pills me-1"></i> Module 2</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Medication & Inventory Catalog</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Manage active drugs, packaging units, retail pricing, minimum thresholds, and batch expiry allocations.
                        </p>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto flex-wrap">
                        <button type="button" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                            <i class="fas fa-file-import text-teal me-1"></i> Import CSV
                        </button>
                        <a href="/inventory/fefo" class="btn btn-outline-dark btn-sm flex-fill flex-md-grow-0">
                            <i class="fas fa-clock text-warning me-1"></i> FEFO Alerts
                        </a>
                        <button type="button" class="btn btn-primary btn-sm flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus-circle me-1"></i> Add Medication
                        </button>
                    </div>
                </div>

                <!-- Search & Filter Toolbar -->
                <div class="card-modern p-3 p-sm-4 mb-4">
                    <form method="GET" action="/inventory/products" class="row g-2 g-sm-3 align-items-center">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Search by name, SKU, generic, or barcode..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (($filters['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?> (<?= $cat['product_count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark btn-sm flex-fill">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <?php if (!empty($filters['search']) || !empty($filters['category_id'])): ?>
                                <a href="/inventory/products" class="btn btn-outline-dark btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Products Catalog Table -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Showing <span class="text-teal font-mono"><?= $pagination['from'] ?>–<?= $pagination['to'] ?></span> of <span class="fw-bold text-dark font-mono"><?= number_format($pagination['total']) ?></span> registered medications
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-tag badge-tag-teal font-mono">Page <?= $pagination['page'] ?> of <?= max(1, $pagination['total_pages']) ?></span>
                            <span class="badge-tag badge-tag-dark font-mono">Total SKUs: <?= number_format($pagination['total']) ?></span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>SKU / CODE</th>
                                    <th>MEDICATION & DOSAGE</th>
                                    <th>CATEGORY</th>
                                    <th>STOCK LEVEL</th>
                                    <th>RETAIL PRICE</th>
                                    <th>EARLIEST EXPIRY</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-search fs-4 d-block mb-2 text-secondary"></i>
                                            No medications found matching your criteria.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['sku']) ?></div>
                                                <?php if (!empty($p['barcode'])): ?>
                                                    <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-barcode me-1"></i><?= htmlspecialchars($p['barcode']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($p['name']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.75rem;">
                                                    <?= htmlspecialchars($p['generic_name'] ?? '-') ?> • <?= htmlspecialchars($p['dosage'] ?? '-') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-tag badge-tag-dark font-sans">
                                                    <?= htmlspecialchars($p['category_name'] ?? 'General') ?>
                                                </span>
                                                <?php if ($p['requires_prescription']): ?>
                                                    <span class="badge-tag badge-tag-crimson ms-1 font-sans">Rx</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($p['stock_quantity'] <= $p['min_stock']): ?>
                                                    <span class="badge-tag badge-tag-amber fw-bold">
                                                        <i class="fas fa-exclamation-triangle"></i> <?= $p['stock_quantity'] ?> <?= htmlspecialchars($p['unit_symbol'] ?? 'unit') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-tag badge-tag-emerald">
                                                        <i class="fas fa-check"></i> <?= $p['stock_quantity'] ?> <?= htmlspecialchars($p['unit_symbol'] ?? 'unit') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-teal">Rp <?= number_format($p['sell_price'], 0, ',', '.') ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Buy: Rp <?= number_format($p['buy_price'], 0, ',', '.') ?></div>
                                            </td>
                                            <td>
                                                <?php if (!empty($p['nearest_expiry'])): ?>
                                                    <?php 
                                                        $daysLeft = (int)((strtotime($p['nearest_expiry']) - time()) / 86400);
                                                        $badgeClass = ($daysLeft <= 30) ? 'badge-tag-crimson' : (($daysLeft <= 60) ? 'badge-tag-amber' : 'badge-tag-emerald');
                                                    ?>
                                                    <span class="badge-tag <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($p['nearest_expiry']) ?> (<?= $daysLeft ?>d)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted small">No Active Batch</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-dark py-1 px-2" onclick="viewBatches(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')">
                                                    <i class="fas fa-layer-group me-1"></i> Batches
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Server-Side Pagination Bar -->
                    <?php if ($pagination['total_pages'] > 1 || $pagination['total'] > 10): ?>
                        <?php
                            $queryParams = $_GET ?? [];
                            $buildUrl = function($pageNum) use ($queryParams) {
                                $params = array_merge($queryParams, ['page' => $pageNum]);
                                return '/inventory/products?' . http_build_query($params);
                            };
                            $curPage = $pagination['page'];
                            $totPages = $pagination['total_pages'];
                        ?>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 mt-3 border-top">
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span>Rows per page:</span>
                                <select class="form-select form-select-sm" style="width: 75px;" onchange="location.href='/inventory/products?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)), per_page: this.value, page: 1}).toString()">
                                    <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $pagination['per_page'] == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>

                            <nav aria-label="Medication pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $curPage > 1 ? $buildUrl($curPage - 1) : '#' ?>">
                                            <i class="fas fa-chevron-left me-1"></i> Prev
                                        </a>
                                    </li>

                                    <?php
                                        $startPage = max(1, $curPage - 2);
                                        $endPage = min($totPages, $curPage + 2);
                                        if ($startPage > 1): ?>
                                            <li class="page-item"><a class="page-link" href="<?= $buildUrl(1) ?>">1</a></li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif;

                                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= ($i === $curPage) ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor;

                                        if ($endPage < $totPages): ?>
                                            <?php if ($endPage < $totPages - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item"><a class="page-link" href="<?= $buildUrl($totPages) ?>"><?= $totPages ?></a></li>
                                        <?php endif;
                                    ?>

                                    <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $curPage < $totPages ? $buildUrl($curPage + 1) : '#' ?>">
                                            Next <i class="fas fa-chevron-right ms-1"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal: Add Product -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px; font-size: 1rem;">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark" id="addProductModalLabel">Register New Medication</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addProductForm">
                    <div class="modal-body p-4">
                        <div id="modal-alert-container"></div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Medication Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Cefixime 100mg Capsule" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Generic / Active Chemical *</label>
                                <input type="text" name="generic_name" class="form-control" placeholder="e.g. Cefixime Trihydrate">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">SKU Code *</label>
                                <input type="text" name="sku" class="form-control font-mono" placeholder="e.g. MED-CFX-100" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Barcode (EAN-13)</label>
                                <input type="text" name="barcode" class="form-control font-mono" placeholder="e.g. 8991234567890">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Packaging Unit</label>
                                <select name="unit_id" class="form-select">
                                    <option value="">Select Unit</option>
                                    <?php foreach ($units as $u): ?>
                                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['symbol'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Buy Price (HPP) Rp</label>
                                <input type="number" name="buy_price" class="form-control font-mono" placeholder="0" min="0">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Retail Selling Price Rp *</label>
                                <input type="number" name="sell_price" class="form-control font-mono" placeholder="0" min="0" required>
                            </div>

                            <div class="col-12 pt-2 border-top">
                                <span class="badge-tag badge-tag-teal mb-2"><i class="fas fa-layer-group me-1"></i> Initial Batch & FEFO Expiry (Optional)</span>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Batch / Lot Number</label>
                                <input type="text" name="batch_number" class="form-control font-mono" placeholder="e.g. LOT-2026-X01">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Expiration Date</label>
                                <input type="date" name="expiry_date" class="form-control font-mono">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Initial Stock Quantity</label>
                                <input type="number" name="initial_stock" class="form-control font-mono" placeholder="0" min="0">
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requires_prescription" value="1" id="reqRx">
                                    <label class="form-check-label small text-dark" for="reqRx">
                                        Requires Doctor Prescription (Ethical / Rx Drug)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="saveProductBtn">
                            <i class="fas fa-save me-1"></i> Save Medication
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Bulk CSV Import -->
    <div class="modal fade" id="importCsvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px; font-size: 1rem;">
                            <i class="fas fa-file-csv"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Bulk Import Medications via CSV</h5>
                            <div class="text-muted small">Import thousands of pharmaceutical SKUs instantly</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="importCsvForm" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div id="importAlertBox"></div>

                        <div class="p-3 bg-light rounded border mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-bold text-dark small"><i class="fas fa-info-circle text-teal me-1"></i> Need the standard format template?</div>
                                <div class="text-muted small">Download our pre-formatted CSV template with example columns.</div>
                            </div>
                            <a href="/inventory/products/template" class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-download me-1"></i> Download Template (.CSV)
                            </a>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Choose CSV File</label>
                            <input type="file" name="csv_file" id="csvFileInput" class="form-control" accept=".csv,text/csv,text/plain">
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small">Or Paste Raw CSV Data</label>
                            <textarea name="csv_data" id="csvDataInput" class="form-control font-mono small" rows="5" 
                                      placeholder="SKU,Barcode,Name,GenericName,CategorySlug,UnitSymbol,Dosage,Manufacturer,BuyPrice,SellPrice,MinStock,StockQuantity,RequiresPrescription,BatchNumber,ExpiryDate&#10;MED-PRC-500,8991234567,Paracetamol 500mg,Paracetamol,analgesics,str,500mg,Kalbe,8000,12000,20,150,0,LOT-2026-A1,2027-12-31"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3 bg-light">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="submitImportBtn">
                            <i class="fas fa-upload me-1"></i> Upload & Import Items
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: View FEFO Batches -->
    <div class="modal fade" id="batchesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px; font-size: 1rem;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="batchModalTitle">FEFO Batch Lot Allocations</h5>
                            <div class="text-muted small" id="batchModalSubtitle">Earliest Expiring Dispatched First</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="batchesModalBody">
                    <div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Loading batches...</div>
                </div>
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

        // Add Product Form
        document.getElementById('addProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveProductBtn');
            const alertBox = document.getElementById('modal-alert-container');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const res = await fetch('/inventory/products', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small">${result.message}</div>`;
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small">${result.message || 'Error creating medication.'}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Medication';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small">Network error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Medication';
            }
        });

        // Bulk CSV Import
        document.getElementById('importCsvForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitImportBtn');
            const alertBox = document.getElementById('importAlertBox');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Importing...';

            const formData = new FormData(this);

            try {
                const res = await fetch('/inventory/products/import', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    let errHtml = '';
                    if (result.errors && result.errors.length > 0) {
                        errHtml = `<div class="mt-2 small text-warning"><strong>Warnings:</strong><br>${result.errors.join('<br>')}</div>`;
                    }
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small">${result.message}${errHtml}</div>`;
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small">${result.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small">Error: ${err.message}</div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload & Import Items';
            }
        });

        // View Batches via API
        async function viewBatches(productId, productName) {
            document.getElementById('batchModalTitle').textContent = productName;
            const modalBody = document.getElementById('batchesModalBody');
            modalBody.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Loading FEFO batches...</div>';
            
            const modal = new bootstrap.Modal(document.getElementById('batchesModal'));
            modal.show();

            try {
                const res = await fetch(`/api/inventory/batches?product_id=${productId}`);
                const result = await res.json();

                if (result.success && result.batches.length > 0) {
                    let rows = result.batches.map(b => {
                        let badgeClass = (b.days_until_expiry <= 30) ? 'badge-tag-crimson' : ((b.days_until_expiry <= 60) ? 'badge-tag-amber' : 'badge-tag-emerald');
                        return `
                            <tr>
                                <td><code>${b.batch_number}</code></td>
                                <td>${b.current_quantity} / ${b.initial_quantity}</td>
                                <td><span class="badge-tag ${badgeClass}">${b.expiry_date} (${b.days_until_expiry}d)</span></td>
                                <td>${b.supplier_name || 'Direct'}</td>
                                <td>${b.received_date}</td>
                            </tr>
                        `;
                    }).join('');

                    modalBody.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>BATCH LOT</th>
                                        <th>REMAINING / INIT</th>
                                        <th>EXPIRY DATE</th>
                                        <th>SUPPLIER</th>
                                        <th>RECEIVED</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.8rem;">
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    modalBody.innerHTML = `<div class="text-center py-4 text-muted">No active batches recorded for this medication.</div>`;
                }
            } catch (err) {
                modalBody.innerHTML = `<div class="alert alert-danger small">Failed to load batches: ${err.message}</div>`;
            }
        }
    </script>
</body>
</html>
