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
                    <a href="/purchasing/ap-ledger" class="sidebar-menu-link active">
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

            <!-- Page Content -->
            <main class="app-content">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag badge-tag-crimson"><i class="fas fa-book-medical me-1"></i> AP Ledger</span>
                            <h1 class="h4 fw-bold text-dark mb-0">Accounts Payable & PBF Debt Ledger</h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Monitor PBF distributor invoices, track payment due dates, and record cash/transfer settlements.
                        </p>
                    </div>

                    <a href="/purchasing" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>

                <!-- Financial Summary KPI Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Outstanding Debt -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Outstanding Debt</span>
                                <div class="icon-box-solid icon-box-crimson" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-danger font-mono">
                                Rp <?= number_format($summary['total_outstanding'], 0, ',', '.') ?>
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                <?= $summary['pending_invoices_count'] ?> unpaid/partial invoices
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Debt -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3 border-danger">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-danger small fw-bold">Overdue Invoices (Jatuh Tempo)</span>
                                <div class="icon-box-solid icon-box-crimson" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-danger font-mono">
                                Rp <?= number_format($summary['total_overdue'], 0, ',', '.') ?>
                            </div>
                            <div class="text-danger small font-mono mt-1" style="font-size: 0.72rem;">
                                <?= $summary['overdue_invoices_count'] ?> invoices past due date
                            </div>
                        </div>
                    </div>

                    <!-- Total Paid to PBF -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Invoices Settled</span>
                                <div class="icon-box-solid icon-box-teal" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-teal font-mono">
                                Rp <?= number_format($summary['total_paid'], 0, ',', '.') ?>
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                Invoiced: Rp <?= number_format($summary['total_invoiced'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>

                    <!-- PBF Distributors Count -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card-modern p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small">Total Invoices Logged</span>
                                <div class="icon-box-solid icon-box-dark" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold text-dark font-mono">
                                <?= count($invoices) ?> Invoices
                            </div>
                            <div class="text-muted small font-mono mt-1" style="font-size: 0.72rem;">
                                AP ledger synced with FEFO
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Navigation Bar -->
                <div class="rx-filter-nav">
                    <a href="/purchasing/ap-ledger?payment_status=all" class="btn btn-sm <?= ($currentPaymentStatus === 'all' && !$isOverdueOnly) ? 'btn-dark' : 'btn-outline-dark' ?> text-nowrap">
                        All Invoices
                    </a>
                    <a href="/purchasing/ap-ledger?payment_status=unpaid" class="btn btn-sm <?= ($currentPaymentStatus === 'unpaid') ? 'btn-danger' : 'btn-outline-danger' ?> text-nowrap">
                        <i class="fas fa-clock me-1"></i> Unpaid
                    </a>
                    <a href="/purchasing/ap-ledger?payment_status=partial" class="btn btn-sm <?= ($currentPaymentStatus === 'partial') ? 'btn-warning text-dark' : 'btn-outline-warning' ?> text-nowrap">
                        <i class="fas fa-adjust me-1"></i> Partial Paid
                    </a>
                    <a href="/purchasing/ap-ledger?payment_status=paid" class="btn btn-sm <?= ($currentPaymentStatus === 'paid') ? 'btn-success' : 'btn-outline-success' ?> text-nowrap">
                        <i class="fas fa-check-circle me-1"></i> Fully Paid
                    </a>
                    <a href="/purchasing/ap-ledger?overdue=1" class="btn btn-sm <?= ($isOverdueOnly) ? 'btn-danger text-white' : 'btn-outline-danger' ?> text-nowrap">
                        <i class="fas fa-exclamation-triangle me-1"></i> Overdue (<?= $summary['overdue_invoices_count'] ?>)
                    </a>
                </div>

                <!-- AP Invoices Container -->
                <div class="card-modern p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark small">
                            Showing <strong class="text-teal"><?= count($invoices) ?></strong> PBF invoices
                        </div>
                    </div>

                    <?php if (empty($invoices)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-hand-holding-dollar fs-3 d-block mb-2 text-secondary"></i>
                            No invoices found in this payment filter.
                        </div>
                    <?php else: ?>
                        <!-- 1. Mobile Cards (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($invoices as $inv): ?>
                                <?php 
                                    $pBadge = match($inv['payment_status']) {
                                        'paid' => 'badge-tag-emerald',
                                        'partial' => 'badge-tag-amber',
                                        default => 'badge-tag-crimson'
                                    };
                                    $remaining = (float)$inv['total_amount'] - (float)$inv['amount_paid'];
                                    $isOverdue = ($inv['days_until_due'] < 0 && $inv['payment_status'] !== 'paid');
                                ?>
                                <div class="rx-mobile-card <?= $isOverdue ? 'border-danger' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-bold text-dark font-mono"><?= htmlspecialchars($inv['invoice_number']) ?></div>
                                            <div class="text-muted" style="font-size: 0.68rem;">GRN: <?= htmlspecialchars($inv['grn_number']) ?></div>
                                        </div>
                                        <span class="badge-tag <?= $pBadge ?> text-uppercase font-mono" style="font-size: 0.68rem;">
                                            <?= htmlspecialchars($inv['payment_status']) ?>
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?= htmlspecialchars($inv['supplier_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            Due Date: <strong class="<?= $isOverdue ? 'text-danger' : 'text-dark' ?> font-mono"><?= date('d M Y', strtotime($inv['due_date'])) ?></strong>
                                            <?php if ($isOverdue): ?>
                                                <span class="badge-tag badge-tag-crimson ms-1" style="font-size: 0.6rem;">OVERDUE <?= abs($inv['days_until_due']) ?>d</span>
                                            <?php else: ?>
                                                <span class="text-muted ms-1">(<?= $inv['days_until_due'] ?> days left)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div>
                                            <div class="text-muted" style="font-size: 0.68rem;">Remaining Due</div>
                                            <div class="fw-bold text-danger font-mono">Rp <?= number_format($remaining, 0, ',', '.') ?></div>
                                        </div>
                                        <?php if ($inv['payment_status'] !== 'paid'): ?>
                                            <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="openPaymentModal(<?= htmlspecialchars(json_encode($inv)) ?>)">
                                                <i class="fas fa-money-bill-wave me-1"></i> Pay
                                            </button>
                                        <?php else: ?>
                                            <span class="badge-tag badge-tag-emerald"><i class="fas fa-check me-1"></i> Settled</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 2. Desktop Table (>= 768px) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0 font-mono small">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem;">
                                        <th>INVOICE NO & GRN</th>
                                        <th>DISTRIBUTOR (PBF)</th>
                                        <th>INVOICE DATE</th>
                                        <th>DUE DATE (JATUH TEMPO)</th>
                                        <th>TOTAL INVOICE</th>
                                        <th>PAID</th>
                                        <th>REMAINING</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($invoices as $inv): ?>
                                        <?php 
                                            $pBadge = match($inv['payment_status']) {
                                                'paid' => 'badge-tag-emerald',
                                                'partial' => 'badge-tag-amber',
                                                default => 'badge-tag-crimson'
                                            };
                                            $remaining = (float)$inv['total_amount'] - (float)$inv['amount_paid'];
                                            $isOverdue = ($inv['days_until_due'] < 0 && $inv['payment_status'] !== 'paid');
                                        ?>
                                        <tr class="<?= $isOverdue ? 'table-danger-subtle' : '' ?>">
                                            <td>
                                                <div class="fw-bold text-dark font-mono"><?= htmlspecialchars($inv['invoice_number']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;">GRN: <?= htmlspecialchars($inv['grn_number']) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($inv['supplier_name']) ?></div>
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;"><?= htmlspecialchars($inv['supplier_code'] ?? 'PBF') ?></div>
                                            </td>
                                            <td><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                            <td>
                                                <div class="fw-bold <?= $isOverdue ? 'text-danger' : 'text-dark' ?>"><?= date('d M Y', strtotime($inv['due_date'])) ?></div>
                                                <?php if ($isOverdue): ?>
                                                    <span class="badge-tag badge-tag-crimson" style="font-size: 0.65rem;">OVERDUE <?= abs($inv['days_until_due']) ?> DAYS</span>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 0.7rem;"><?= $inv['days_until_due'] ?> days left</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></td>
                                            <td class="text-success">Rp <?= number_format($inv['amount_paid'], 0, ',', '.') ?></td>
                                            <td><strong class="text-danger">Rp <?= number_format($remaining, 0, ',', '.') ?></strong></td>
                                            <td>
                                                <span class="badge-tag <?= $pBadge ?> text-uppercase">
                                                    <?= htmlspecialchars($inv['payment_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($inv['payment_status'] !== 'paid'): ?>
                                                    <button type="button" class="btn btn-sm btn-primary py-1 px-2" onclick="openPaymentModal(<?= htmlspecialchars(json_encode($inv)) ?>)">
                                                        <i class="fas fa-money-bill-wave me-1"></i> Pay
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge-tag badge-tag-emerald"><i class="fas fa-check me-1"></i> Paid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-teal text-white py-3 px-4">
                    <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-money-bill-wave me-2"></i> Record PBF Debt Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paymentForm" onsubmit="submitPayment(event)">
                    <input type="hidden" name="goods_receipt_id" id="modalGrId">
                    <div class="modal-body p-4">
                        <div id="modalAlert"></div>

                        <div class="p-3 bg-light rounded border mb-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Distributor (PBF):</span>
                                <strong id="modalSupplierName">-</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Invoice Number:</span>
                                <strong class="font-mono" id="modalInvoiceNo">-</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                <span class="text-danger fw-bold">Remaining Balance:</span>
                                <strong class="text-danger font-mono fs-6" id="modalRemainingDue">Rp 0</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Payment Amount (Rp) *</label>
                            <input type="number" step="100" name="amount_paid" id="modalAmountInput" class="form-control font-mono fs-5 text-teal fw-bold" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Payment Method</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="bank_transfer" selected>Bank Transfer (BCA/Mandiri)</option>
                                    <option value="cash">Cash (Tunai Kasir)</option>
                                    <option value="giro">Bilyet Giro</option>
                                    <option value="cheque">Cek Bank</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Reference / Bukti Transfer</label>
                                <input type="text" name="reference_number" class="form-control form-control-sm font-mono" placeholder="TRF-BCA-XXXXX">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small text-muted">Payment Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Pembayaran lunas via rekening BCA Apotek">
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 px-4">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="submitPaymentBtn">
                            <i class="fas fa-save me-1"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let paymentModalInstance;

        function openPaymentModal(inv) {
            document.getElementById('modalGrId').value = inv.id;
            document.getElementById('modalSupplierName').textContent = inv.supplier_name;
            document.getElementById('modalInvoiceNo').textContent = inv.invoice_number;
            const remaining = parseFloat(inv.total_amount) - parseFloat(inv.amount_paid);
            document.getElementById('modalRemainingDue').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
            document.getElementById('modalAmountInput').value = remaining;
            document.getElementById('modalAlert').innerHTML = '';

            if (!paymentModalInstance) {
                paymentModalInstance = new bootstrap.Modal(document.getElementById('paymentModal'));
            }
            paymentModalInstance.show();
        }

        async function submitPayment(e) {
            e.preventDefault();
            const btn = document.getElementById('submitPaymentBtn');
            const alertBox = document.getElementById('modalAlert');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            const form = document.getElementById('paymentForm');
            const formData = new FormData(form);

            const payload = {
                goods_receipt_id: formData.get('goods_receipt_id'),
                amount_paid: formData.get('amount_paid'),
                payment_method: formData.get('payment_method'),
                reference_number: formData.get('reference_number'),
                notes: formData.get('notes')
            };

            try {
                const res = await fetch('/purchasing/pay-invoice', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Payment';
                }
            } catch (err) {
                alertBox.innerHTML = '<div class="alert alert-danger">Network error: ' + err.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Payment';
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
