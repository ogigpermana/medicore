<?php
    $currentRole = $user['role_name'] ?? 'superadmin';
    $currentName = $user['full_name'] ?? 'Super Administrator';
    $roleBadgeClass = match($currentRole) {
        'superadmin' => 'badge-tag-dark',
        'owner' => 'badge-tag-dark',
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

                <!-- Reports & Audit -->
                <?php if (in_array($currentRole, ['superadmin', 'pharmacist', 'owner', 'warehouse'])): ?>
                    <div class="sidebar-section-label mt-2">Reports & Audit</div>
                    <a href="/reports" class="sidebar-menu-link">
                        <i class="fas fa-chart-line"></i> <span>Financial Reports</span>
                    </a>
                    <a href="/stock-opname" class="sidebar-menu-link">
                        <i class="fas fa-clipboard-check"></i> <span>Stock Opname</span>
                    </a>
                    <?php if (in_array($currentRole, ['superadmin', 'owner'])): ?>
                        <a href="/audit-logs" class="sidebar-menu-link active">
                            <i class="fas fa-shield-alt"></i> <span>Security Audit Logs</span>
                        </a>
                    <?php endif; ?>
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
                                <div class="mt-1">
                                    <span class="badge-tag <?= $roleBadgeClass ?> text-uppercase font-mono" style="font-size: 0.65rem;">
                                        Role: <?= htmlspecialchars($currentRole) ?>
                                    </span>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-th-large text-muted"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="/audit-logs"><i class="fas fa-shield-alt text-muted"></i> Audit Logs</a></li>
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
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-dark"><i class="fas fa-shield-alt me-1"></i> Compliance & Security</span>
                            <h1 class="h4 fw-bold text-dark mb-0">System Audit Trail & Access Logs</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Immutable audit trail of pharmacy staff authentication, clinical prescription approvals, POS transactions, and inventory stock modifications.
                        </p>
                    </div>

                    <a href="/audit-logs/export?<?= http_build_query($_GET ?? []) ?>" class="btn btn-dark btn-sm w-100 w-md-auto fw-bold">
                        <i class="fas fa-file-export me-1"></i> Export Audit Trail (CSV)
                    </a>
                </div>

                <!-- Metric Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Recorded Events</span>
                                <div class="icon-box-solid icon-box-dark" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-database"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['total_events']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Complete system history</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Activity Today</span>
                                <div class="icon-box-solid icon-box-teal" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-teal font-mono"><?= number_format($stats['today_events']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Events in past 24 hours</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Auth & Login Events</span>
                                <div class="icon-box-solid icon-box-blue" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['auth_events']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Sign-in & password attempts</div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="card-modern p-3 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Audited Staff Users</span>
                                <div class="icon-box-solid icon-box-amber" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark font-mono"><?= number_format($stats['active_users_audited']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Unique actors logged</div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="card-modern p-3 mb-4">
                    <form method="GET" action="/audit-logs" class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search action, description, IP, user..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="action_type" class="form-select form-select-sm">
                                <option value="all" <?= (($filters['action_type'] ?? 'all') === 'all') ? 'selected' : '' ?>>All Actions</option>
                                <option value="login" <?= (($filters['action_type'] ?? '') === 'login') ? 'selected' : '' ?>>Authentication</option>
                                <option value="sale" <?= (($filters['action_type'] ?? '') === 'sale') ? 'selected' : '' ?>>POS / Sales</option>
                                <option value="stock" <?= (($filters['action_type'] ?? '') === 'stock') ? 'selected' : '' ?>>Stock / Inventory</option>
                                <option value="prescription" <?= (($filters['action_type'] ?? '') === 'prescription') ? 'selected' : '' ?>>Prescriptions</option>
                                <option value="transfer" <?= (($filters['action_type'] ?? '') === 'transfer') ? 'selected' : '' ?>>Branch Transfers</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">All Staff</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= (($filters['user_id'] ?? '') == $u['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <input type="date" name="start_date" class="form-control form-control-sm font-mono" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>" placeholder="Start Date">
                        </div>

                        <div class="col-6 col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-dark btn-sm flex-fill">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <?php if (!empty($filters['search']) || $filters['action_type'] !== 'all' || !empty($filters['user_id']) || !empty($filters['start_date'])): ?>
                                <a href="/audit-logs" class="btn btn-outline-secondary btn-sm" title="Reset">
                                    <i class="fas fa-redo"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Audit Trail Table -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Showing <span class="text-teal font-mono"><?= $pagination['from'] ?>–<?= $pagination['to'] ?></span> of <span class="fw-bold text-dark font-mono"><?= number_format($pagination['total']) ?></span> audit events
                        </div>
                        <span class="badge-tag badge-tag-dark font-mono">Page <?= $pagination['page'] ?> of <?= max(1, $pagination['total_pages']) ?></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.75rem;">
                                    <th>TIMESTAMP</th>
                                    <th>USER / ACTOR</th>
                                    <th>ACTION EVENT</th>
                                    <th>TARGET ENTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th>IP & CLIENT</th>
                                    <th class="text-center">PAYLOAD</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.825rem;">
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-shield-halved fs-3 d-block mb-2 text-secondary"></i>
                                            No audit log entries found matching your filter criteria.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <?php
                                            $action = strtolower($log['action']);
                                            $badgeClass = match(true) {
                                                str_contains($action, 'delete') || str_contains($action, 'failed') || str_contains($action, 'cancel') => 'badge-tag-crimson',
                                                str_contains($action, 'create') || str_contains($action, 'store') || str_contains($action, 'approve') => 'badge-tag-emerald',
                                                str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'adjust') => 'badge-tag-amber',
                                                str_contains($action, 'login') || str_contains($action, 'logout') => 'badge-tag-blue',
                                                default => 'badge-tag-teal'
                                            };
                                        ?>
                                        <tr>
                                            <td class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
                                                <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($log['user_name'])): ?>
                                                    <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($log['user_name']) ?></div>
                                                    <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($log['user_email']) ?></div>
                                                <?php else: ?>
                                                    <span class="text-muted font-sans italic">System Guest</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge-tag <?= $badgeClass ?> font-mono">
                                                    <?= htmlspecialchars($log['action']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-tag badge-tag-dark font-mono">
                                                    <?= htmlspecialchars($log['entity_type'] ?? 'System') ?> #<?= htmlspecialchars((string)($log['entity_id'] ?? '-')) ?>
                                                </span>
                                            </td>
                                            <td class="font-sans text-secondary" style="font-size: 0.8rem; max-width: 280px;">
                                                <?= htmlspecialchars($log['description'] ?? '-') ?>
                                            </td>
                                            <td style="font-size: 0.75rem;">
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></div>
                                                <div class="text-muted text-truncate" style="max-width: 140px;" title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                                    <?= htmlspecialchars($log['user_agent'] ?? 'Web Browser') ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($log['metadata']) && $log['metadata'] !== '[]' && $log['metadata'] !== 'null'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-teal py-0.5 px-2 font-sans" onclick='openPayloadModal(<?= (int)$log['id'] ?>, <?= json_encode($log['metadata']) ?>)' title="View Raw Metadata">
                                                        <i class="fas fa-code"></i> Data
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                                <?php endif; ?>
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
                                return '/audit-logs?' . http_build_query($params);
                            };
                            $curPage = $pagination['page'];
                            $totPages = $pagination['total_pages'];
                        ?>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-3 mt-3 border-top">
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span>Rows per page:</span>
                                <select class="form-select form-select-sm" style="width: 75px;" onchange="location.href='/audit-logs?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)), per_page: this.value, page: 1}).toString()">
                                    <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $pagination['per_page'] == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>

                            <nav aria-label="Audit log pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $curPage > 1 ? $buildUrl($curPage - 1) : '#' ?>">
                                            <i class="fas fa-chevron-left me-1"></i> Prev
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totPages; $i++): ?>
                                        <li class="page-item <?= ($i === $curPage) ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

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

    <!-- Payload JSON Modal -->
    <div class="modal fade" id="payloadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark font-mono">
                        <i class="fas fa-code text-teal me-1"></i> Audit Event Payload #<span id="payloadLogId"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <pre class="bg-dark text-light p-3 rounded font-mono small mb-0" id="payloadCode" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openPayloadModal(logId, rawMetadata) {
            document.getElementById('payloadLogId').textContent = logId;
            let formatted = '';
            try {
                const parsed = typeof rawMetadata === 'string' ? JSON.parse(rawMetadata) : rawMetadata;
                formatted = JSON.stringify(parsed, null, 2);
            } catch (e) {
                formatted = String(rawMetadata);
            }
            document.getElementById('payloadCode').textContent = formatted;

            const modal = new bootstrap.Modal(document.getElementById('payloadModal'));
            modal.show();
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
