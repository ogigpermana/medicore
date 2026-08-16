<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Clinical Prescription Review — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">

<?php
    $currentRole = strtolower($role ?? $user['role_name'] ?? $user['role'] ?? 'pharmacist');
    $currentName = $user['full_name'] ?? $user['name'] ?? 'Staff User';
    $statusBadge = match($prescription['status']) {
        'pending' => 'badge-tag-crimson',
        'reviewed' => 'badge-tag-blue',
        'compounding' => 'badge-tag-amber',
        'ready' => 'badge-tag-emerald',
        'dispensed' => 'badge-tag-dark',
        default => 'badge-tag-secondary'
    };
?>
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
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

                <div class="sidebar-section-label mt-2">Clinical Pharmacy</div>
                <a href="/prescriptions" class="sidebar-menu-link active">
                    <i class="fas fa-file-medical"></i> <span>Prescription Queue</span>
                </a>

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

                <div class="sidebar-section-label mt-2">Inventory & FEFO</div>
                <a href="/inventory/products" class="sidebar-menu-link">
                    <i class="fas fa-boxes"></i> <span>Medications</span>
                </a>
                <a href="/inventory/fefo" class="sidebar-menu-link">
                    <i class="fas fa-calendar-alt"></i> <span>FEFO Sentinel</span>
                </a>

                <div class="sidebar-section-label mt-2">Account</div>
                <a href="/profile" class="sidebar-menu-link">
                    <i class="fas fa-user-circle"></i> <span>My Profile</span>
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
                <!-- Action / Flash Alert -->
                <div id="rxActionAlert"></div>

                <!-- Header Title Bar -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge-tag <?= $statusBadge ?> text-uppercase font-mono">
                                <?= htmlspecialchars($prescription['status']) ?>
                            </span>
                            <h1 class="h4 fw-bold text-dark mb-0 font-mono"><?= htmlspecialchars($prescription['prescription_number']) ?></h1>
                        </div>
                        <p class="text-muted small mb-0">
                            Registered on <?= date('d F Y, H:i', strtotime($prescription['created_at'])) ?>
                        </p>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="/pos?rx=<?= urlencode($prescription['prescription_number']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cash-register me-1"></i> Dispatch to POS
                        </a>
                    </div>
                </div>

                <div class="row g-3 g-md-4 mb-4">
                    <!-- Patient Profile Card -->
                    <div class="col-12 col-md-6">
                        <div class="card-modern p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                                <span class="fw-bold text-dark small"><i class="fas fa-user-injured text-teal me-1"></i> Patient Record</span>
                                <span class="badge-tag badge-tag-teal"><?= ucfirst($prescription['patient_gender']) ?></span>
                            </div>
                            <div class="fs-5 fw-bold text-dark mb-2"><?= htmlspecialchars($prescription['patient_name']) ?></div>
                            <div class="row g-2 text-muted small">
                                <div class="col-6">
                                    Age: <strong class="text-dark font-mono"><?= $prescription['patient_age'] ? $prescription['patient_age'] . ' yrs' : '-' ?></strong>
                                </div>
                                <div class="col-6">
                                    Weight: <strong class="text-dark font-mono"><?= $prescription['patient_weight'] ? $prescription['patient_weight'] . ' kg' : '-' ?></strong>
                                </div>
                                <div class="col-12 mt-2">
                                    Diagnosis: <strong class="text-dark"><?= htmlspecialchars($prescription['diagnosis'] ?? 'Not specified') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescribing Doctor Card -->
                    <div class="col-12 col-md-6">
                        <div class="card-modern p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                                <span class="fw-bold text-dark small"><i class="fas fa-user-md text-primary me-1"></i> Prescribing Physician</span>
                                <span class="badge-tag badge-tag-blue font-mono">SIP Verified</span>
                            </div>
                            <div class="fs-5 fw-bold text-dark mb-2"><?= htmlspecialchars($prescription['doctor_name']) ?></div>
                            <div class="row g-2 text-muted small">
                                <div class="col-12">
                                    SIP: <strong class="text-dark font-mono"><?= htmlspecialchars($prescription['doctor_sip'] ?? 'SIP not recorded') ?></strong>
                                </div>
                                <div class="col-12">
                                    Clinic / Origin: <strong class="text-dark"><?= htmlspecialchars($prescription['doctor_clinic'] ?? 'General Practice') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clinical Remarks -->
                <?php if (!empty($prescription['clinical_notes'])): ?>
                    <div class="card-modern p-3 p-sm-4 mb-4 border-start border-4 border-amber">
                        <div class="fw-bold text-dark small mb-1"><i class="fas fa-comment-medical text-amber me-1"></i> Doctor Clinical Instructions:</div>
                        <div class="small text-secondary"><?= nl2br(htmlspecialchars($prescription['clinical_notes'])) ?></div>
                    </div>
                <?php endif; ?>

                <!-- Prescribed Items Container -->
                <div class="card-modern p-3 p-sm-4 mb-4">
                    <div class="fw-bold text-dark small pb-2 mb-3 border-bottom">
                        <i class="fas fa-pills text-teal me-1"></i> Finished Medications (Obat Non-Racikan)
                    </div>

                    <?php if (empty($prescription['items'])): ?>
                        <div class="text-muted small py-2">No individual finished drugs in this prescription order.</div>
                    <?php else: ?>
                        <!-- Mobile View for Finished Items (< 768px) -->
                        <div class="d-md-none">
                            <?php foreach ($prescription['items'] as $it): ?>
                                <div class="p-2.5 bg-light rounded border mb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($it['product_name']) ?></div>
                                            <div class="text-muted font-mono" style="font-size: 0.68rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-teal font-mono" style="font-size: 0.85rem;">Rp <?= number_format($it['total_price'], 0, ',', '.') ?></div>
                                            <div class="text-muted font-mono" style="font-size: 0.68rem;"><?= $it['quantity'] ?> <?= htmlspecialchars($it['unit_symbol'] ?? 'unit') ?></div>
                                        </div>
                                    </div>
                                    <div class="pt-1.5 border-top d-flex justify-content-between align-items-center">
                                        <span class="badge-tag badge-tag-teal font-sans"><?= htmlspecialchars($it['dosage_instructions']) ?></span>
                                        <?php if (!empty($it['usage_time'])): ?>
                                            <span class="text-muted small" style="font-size: 0.7rem;"><?= htmlspecialchars($it['usage_time']) ?></span>
                                        <?php endif; ?>
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
                                        <th>DOSAGE INSTRUCTION (SIGNA)</th>
                                        <th>USAGE TIME</th>
                                        <th>QTY</th>
                                        <th>PRICE</th>
                                        <th>SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 0.825rem;">
                                    <?php foreach ($prescription['items'] as $it): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark font-sans"><?= htmlspecialchars($it['product_name']) ?></div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($it['sku']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge-tag badge-tag-teal font-sans"><?= htmlspecialchars($it['dosage_instructions']) ?></span>
                                            </td>
                                            <td class="font-sans"><?= htmlspecialchars($it['usage_time'] ?? '-') ?></td>
                                            <td><?= $it['quantity'] ?> <?= htmlspecialchars($it['unit_symbol'] ?? '') ?></td>
                                            <td>Rp <?= number_format($it['unit_price'], 0, ',', '.') ?></td>
                                            <td><strong class="text-teal">Rp <?= number_format($it['total_price'], 0, ',', '.') ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Compounds / Racikan Breakdown -->
                <?php if (!empty($prescription['compounds'])): ?>
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="fw-bold text-dark small pb-2 mb-3 border-bottom">
                            <i class="fas fa-mortar-pestle text-amber me-1"></i> Compounded Prescription Mixtures (Obat Racikan)
                        </div>

                        <?php foreach ($prescription['compounds'] as $cmp): ?>
                            <div class="p-3 bg-light rounded border mb-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom flex-wrap gap-2">
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($cmp['compound_name']) ?></div>
                                        <div class="text-muted small">
                                            Form: <span class="badge-tag badge-tag-amber text-uppercase"><?= htmlspecialchars($cmp['packaging_type']) ?></span> • 
                                            Total: <strong><?= $cmp['quantity_pack'] ?> packs/sachets</strong>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Compounded Total</div>
                                        <div class="fw-bold text-teal font-mono">Rp <?= number_format($cmp['total_price'], 0, ',', '.') ?></div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <span class="text-muted small">Signa:</span> 
                                    <span class="badge-tag badge-tag-dark ms-1"><?= htmlspecialchars($cmp['dosage_instructions']) ?></span>
                                </div>

                                <!-- Ingredients -->
                                <div class="bg-white p-2.5 rounded border">
                                    <div class="text-muted small fw-bold mb-1" style="font-size: 0.72rem;">ACTIVE INGREDIENTS FORMULA:</div>
                                    <ul class="mb-0 ps-3 small text-secondary font-mono" style="font-size: 0.78rem;">
                                        <?php foreach ($cmp['ingredients'] as $ing): ?>
                                            <li>
                                                <strong><?= htmlspecialchars($ing['product_name']) ?></strong> 
                                                <?= $ing['dose_per_pack'] ? '(' . htmlspecialchars($ing['dose_per_pack']) . ')' : '' ?> — 
                                                Used: <?= $ing['quantity_used'] ?> units (Rp <?= number_format($ing['subtotal'], 0, ',', '.') ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Pharmacist Clinical Review & Sign-Off Workflow -->
                <div class="card-modern p-3 p-sm-4 border-2 border-teal mb-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="fw-bold text-dark">
                            <i class="fas fa-stethoscope text-teal me-1"></i> Pharmacist Clinical Screening & Digital Sign-Off
                        </div>
                        <?php if ($prescription['reviewed_at']): ?>
                            <span class="badge-tag badge-tag-emerald font-mono">
                                <i class="fas fa-stamp me-1"></i> Digitally Signed by <?= htmlspecialchars($prescription['pharmacist_name'] ?? 'Pharmacist') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold">Pharmacist Clinical Validation & Interaction Screening Notes</label>
                            <textarea id="pharmacistNotesInput" class="form-control small" rows="2" placeholder="Record dosage verification, SIP validation, allergy check, or patient counseling notes..."><?= htmlspecialchars($prescription['pharmacist_notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 col-md-4 d-flex flex-column justify-content-end gap-2">
                            <button type="button" class="btn btn-primary btn-sm fw-bold w-100 py-2" onclick="submitPharmacistReview('reviewed')">
                                <i class="fas fa-signature me-1"></i> Sign-Off & Approve
                            </button>
                            <button type="button" class="btn btn-outline-warning text-dark btn-sm fw-bold w-100 py-2" onclick="submitPharmacistReview('compounding')">
                                <i class="fas fa-mortar-pestle me-1"></i> Start Compounding
                            </button>
                        </div>
                    </div>

                    <!-- Status Workflow Step Buttons -->
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4 pt-3 border-top">
                        <span class="text-muted small align-self-sm-center me-sm-2 mb-1 mb-sm-0">Progress Status:</span>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="updateRxStatus('ready')">
                            <i class="fas fa-box-check me-1"></i> Mark Ready for Pickup
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="updateRxStatus('dispensed')">
                            <i class="fas fa-hand-holding-medical me-1"></i> Mark Dispensed
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-sm-auto" onclick="updateRxStatus('cancelled')">
                            <i class="fas fa-ban me-1"></i> Cancel Order
                        </button>
                    </div>
                </div>
            </main>
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

        const rxId = <?= (int)$prescription['id'] ?>;

        async function submitPharmacistReview(nextStatus) {
            const notes = document.getElementById('pharmacistNotesInput').value;
            const alertBox = document.getElementById('rxActionAlert');

            try {
                const res = await fetch('/prescriptions/review', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ id: rxId, pharmacist_notes: notes, next_status: nextStatus })
                });
                const data = await res.json();

                if (data.success) {
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small mb-3"><i class="fas fa-check-circle me-1"></i> ${data.message}</div>`;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">${data.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">Error: ${err.message}</div>`;
            }
        }

        async function updateRxStatus(status) {
            if (!confirm(`Are you sure you want to mark this prescription as ${status.toUpperCase()}?`)) return;
            const alertBox = document.getElementById('rxActionAlert');

            try {
                const res = await fetch('/prescriptions/status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ id: rxId, status: status })
                });
                const data = await res.json();

                if (data.success) {
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small mb-3"><i class="fas fa-check-circle me-1"></i> ${data.message}</div>`;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">${data.message}</div>`;
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">Error: ${err.message}</div>`;
            }
        }
    </script>
</body>
</html>
