<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Enter Digital Prescription — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">

<?php
    $currentRole = strtolower($role ?? $user['role_name'] ?? $user['role'] ?? 'pharmacist');
    $currentName = $user['full_name'] ?? $user['name'] ?? 'Staff User';
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
                <!-- Alert Box -->
                <div id="rxAlertBox"></div>

                <form id="newPrescriptionForm">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge-tag badge-tag-teal font-mono"><?= htmlspecialchars($nextRxNumber) ?></span>
                                <h1 class="h4 fw-bold text-dark mb-0">Enter Digital Doctor Prescription</h1>
                            </div>
                            <p class="text-muted small mb-0">
                                Record clinical details, patient demographics, finished prescription drugs, and custom compounding recipes (puyer/kapsul).
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" id="submitRxBtn">
                            <i class="fas fa-save me-1"></i> Save & Queue Prescription
                        </button>
                    </div>

                    <div class="row g-3 g-md-4 mb-4">
                        <!-- Patient Demographic Card -->
                        <div class="col-12 col-lg-6">
                            <div class="card-modern p-4 h-100">
                                <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                                    <i class="fas fa-user-injured text-teal"></i> 1. Patient Demographics
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Patient Full Name *</label>
                                        <input type="text" name="patient_name" class="form-control" placeholder="e.g. Ananda Reyhan Pratama" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">Age (Years)</label>
                                        <input type="number" name="patient_age" class="form-control font-mono" placeholder="7" min="0">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">Gender</label>
                                        <select name="patient_gender" class="form-select">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small">Weight (kg)</label>
                                        <input type="number" step="0.1" name="patient_weight" class="form-control font-mono" placeholder="22.5">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prescribing Doctor Card -->
                        <div class="col-12 col-lg-6">
                            <div class="card-modern p-4 h-100">
                                <div class="fw-bold text-dark small pb-2 mb-3 border-bottom d-flex align-items-center gap-2">
                                    <i class="fas fa-user-md text-primary"></i> 2. Prescribing Doctor & SIP
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold">Doctor Name *</label>
                                        <input type="text" name="doctor_name" class="form-control" placeholder="e.g. dr. Sarah Sp.A" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small">Doctor SIP Number</label>
                                        <input type="text" name="doctor_sip" class="form-control font-mono" placeholder="e.g. SIP: 503/446/SIP-D/2024">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small">Clinic / Hospital Origin</label>
                                        <input type="text" name="doctor_clinic" class="form-control" placeholder="e.g. Klinik Pratama Sehat">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small">Diagnosis / Indication</label>
                                        <input type="text" name="diagnosis" class="form-control" placeholder="e.g. Acute Bronchitis">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Notes -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <label class="form-label small fw-bold text-dark"><i class="fas fa-sticky-note text-amber me-1"></i> Clinical Remarks & Doctor Instructions</label>
                        <textarea name="clinical_notes" class="form-control small" rows="2" placeholder="e.g. Patient allergic to penicillin, substitute with macrolide if needed. Compounding 10 puyer sachets..."></textarea>
                    </div>

                    <!-- Section 3: Finished Non-Compounded Medications (Obat Non-Racikan) -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                            <div class="fw-bold text-dark small">
                                <i class="fas fa-pills text-teal me-1"></i> 3. Finished Medications (Obat Jadi / Non-Racikan)
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="addFinishedItemRow()">
                                <i class="fas fa-plus me-1"></i> Add Medication Line
                            </button>
                        </div>

                        <div id="finishedItemsContainer">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Section 4: Compounding Builder (Obat Racikan / Puyer / Kapsul) -->
                    <div class="card-modern p-3 p-sm-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom flex-wrap gap-2">
                            <div class="fw-bold text-dark small">
                                <i class="fas fa-mortar-pestle text-amber me-1"></i> 4. Compounded Prescription Mixtures (Obat Racikan / Puyer)
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="addCompoundBlock()">
                                <i class="fas fa-plus me-1"></i> Add Compound Recipe
                            </button>
                        </div>

                        <div id="compoundsContainer">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Total Cost Summary & Submit -->
                    <div class="card-modern p-4 bg-light">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="text-muted small">ESTIMATED PRESCRIPTION CHARGE</div>
                                <div class="fs-3 fw-bold text-teal font-mono" id="rxGrandTotalDisplay">Rp 0</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Includes medication cost, tuslah compounding fee, and packaging</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold" id="submitRxBtnBottom">
                                <i class="fas fa-check-circle me-1"></i> Submit Prescription
                            </button>
                        </div>
                    </div>
                </form>
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

        const availableProducts = <?= json_encode($products ?? []) ?>;

        let finishedRowCounter = 0;
        let compoundBlockCounter = 0;

        function formatIDR(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        function getProductOptions() {
            return `<option value="">-- Select Prescribed Drug --</option>` + 
                   availableProducts.map(p => `<option value="${p.id}" data-price="${p.sell_price}">${p.name} (${p.sku}) - Rp ${Number(p.sell_price).toLocaleString('id-ID')}</option>`).join('');
        }

        // Add Finished Medication Row
        function addFinishedItemRow() {
            finishedRowCounter++;
            const container = document.getElementById('finishedItemsContainer');
            const rowDiv = document.createElement('div');
            rowDiv.className = 'p-3 bg-light rounded border mb-2 finished-item-row rx-form-item-row';
            rowDiv.id = `finished-row-${finishedRowCounter}`;
            rowDiv.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-muted mb-0">Medication *</label>
                            <button type="button" class="btn btn-sm text-danger p-0 d-md-none" onclick="document.getElementById('finished-row-${finishedRowCounter}').remove(); calculateRxTotals();">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </button>
                        </div>
                        <select class="form-select form-select-sm finished-prod-select" onchange="calculateRxTotals()" required>
                            ${getProductOptions()}
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Quantity</label>
                        <input type="number" class="form-control form-control-sm font-mono finished-qty-input" value="1" min="1" oninput="calculateRxTotals()">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small text-muted mb-1">Dosage / Signa *</label>
                        <input type="text" class="form-control form-control-sm finished-signa-input" placeholder="e.g. 3x1 sehari sesudah makan" value="3x1 sehari sesudah makan" required>
                    </div>
                    <div class="col-12 col-md-1 d-none d-md-flex justify-content-end pt-md-3">
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2" onclick="document.getElementById('finished-row-${finishedRowCounter}').remove(); calculateRxTotals();" title="Remove line">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(rowDiv);
            calculateRxTotals();
        }

        // Add Compound (Racikan) Block
        function addCompoundBlock() {
            compoundBlockCounter++;
            const container = document.getElementById('compoundsContainer');
            const blockDiv = document.createElement('div');
            blockDiv.className = 'p-3 p-sm-4 bg-light rounded border mb-3 compound-block rx-form-item-row';
            blockDiv.id = `compound-block-${compoundBlockCounter}`;
            blockDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                    <div class="fw-bold text-dark small"><i class="fas fa-flask text-amber me-1"></i> Compound Recipe #${compoundBlockCounter}</div>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="document.getElementById('compound-block-${compoundBlockCounter}').remove(); calculateRxTotals();">
                        <i class="fas fa-trash-alt me-1"></i> Remove Compound
                    </button>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-muted mb-1">Compound Name *</label>
                        <input type="text" class="form-control form-control-sm compound-name-input" placeholder="e.g. Puyer Batuk Pilek No. X" value="Puyer Batuk No. X" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Packaging Form</label>
                        <select class="form-select form-select-sm compound-pack-type">
                            <option value="puyer">Puyer / Pulveres (Sachet)</option>
                            <option value="capsule">Capsule Shell (Kapsul)</option>
                            <option value="syrup_mixture">Sirup Campuran</option>
                            <option value="ointment">Salep / Cream Racik</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Total Packs</label>
                        <input type="number" class="form-control form-control-sm font-mono compound-packs-input" value="10" min="1">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small text-muted mb-1">Dosage Signa *</label>
                        <input type="text" class="form-control form-control-sm compound-signa-input" value="3x1 bungkus sesudah makan" required>
                    </div>
                </div>

                <div class="p-2.5 bg-white rounded border mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-secondary">Active Drug Ingredients:</span>
                        <button type="button" class="btn btn-sm btn-outline-dark py-0 px-2" onclick="addIngredientRow(${compoundBlockCounter})" style="font-size: 0.75rem;">
                            <i class="fas fa-plus me-1"></i> Add Drug Ingredient
                        </button>
                    </div>
                    <div id="ingredients-container-${compoundBlockCounter}">
                        <!-- Ingredients rows -->
                    </div>
                </div>
            `;
            container.appendChild(blockDiv);
            addIngredientRow(compoundBlockCounter);
        }

        // Add Ingredient to specific compound
        function addIngredientRow(blockId) {
            const container = document.getElementById(`ingredients-container-${blockId}`);
            const rowDiv = document.createElement('div');
            rowDiv.className = 'row g-2 align-items-center mb-2 ingredient-row rx-ingredient-row';
            rowDiv.innerHTML = `
                <div class="col-12 col-md-5">
                    <select class="form-select form-select-sm ingredient-prod-select" onchange="calculateRxTotals()" required>
                        ${getProductOptions()}
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <input type="text" class="form-control form-control-sm ingredient-dose-input" placeholder="Dose (e.g. 250mg / tab)">
                </div>
                <div class="col-5 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Qty</span>
                        <input type="number" class="form-control font-mono ingredient-qty-input" value="5" min="1" oninput="calculateRxTotals()">
                    </div>
                </div>
                <div class="col-1 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm text-danger p-0" onclick="this.closest('.ingredient-row').remove(); calculateRxTotals();">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(rowDiv);
            calculateRxTotals();
        }

        // Calculate Totals Live
        function calculateRxTotals() {
            let grandTotal = 0;

            // Finished lines
            document.querySelectorAll('.finished-item-row').forEach(row => {
                const select = row.querySelector('.finished-prod-select');
                const qtyInput = row.querySelector('.finished-qty-input');
                const selected = select.options[select.selectedIndex];
                if (selected && selected.value) {
                    const price = Number(selected.getAttribute('data-price')) || 0;
                    const qty = Number(qtyInput.value) || 1;
                    grandTotal += (price * qty);
                }
            });

            // Compound lines
            document.querySelectorAll('.compound-block').forEach(block => {
                let compFee = 5000;
                let packFee = 2000;
                let ingTotal = 0;

                block.querySelectorAll('.ingredient-row').forEach(ingRow => {
                    const select = ingRow.querySelector('.ingredient-prod-select');
                    const qtyInput = ingRow.querySelector('.ingredient-qty-input');
                    const selected = select.options[select.selectedIndex];
                    if (selected && selected.value) {
                        const price = Number(selected.getAttribute('data-price')) || 0;
                        const qty = Number(qtyInput.value) || 1;
                        ingTotal += (price * qty);
                    }
                });

                grandTotal += (ingTotal + compFee + packFee);
            });

            document.getElementById('rxGrandTotalDisplay').textContent = formatIDR(grandTotal);
        }

        // Add 1 default finished medication line on load
        addFinishedItemRow();

        // Submit Prescription Form
        document.getElementById('newPrescriptionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitRxBtn');
            const alertBox = document.getElementById('rxAlertBox');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

            const formData = new FormData(this);
            const payload = {
                patient_name: formData.get('patient_name'),
                patient_age: formData.get('patient_age'),
                patient_gender: formData.get('patient_gender'),
                patient_weight: formData.get('patient_weight'),
                doctor_name: formData.get('doctor_name'),
                doctor_sip: formData.get('doctor_sip'),
                doctor_clinic: formData.get('doctor_clinic'),
                diagnosis: formData.get('diagnosis'),
                clinical_notes: formData.get('clinical_notes'),
                items: [],
                compounds: []
            };

            // Collect Finished items
            document.querySelectorAll('.finished-item-row').forEach(row => {
                const select = row.querySelector('.finished-prod-select');
                const qtyInput = row.querySelector('.finished-qty-input');
                const signaInput = row.querySelector('.finished-signa-input');
                if (select.value) {
                    payload.items.push({
                        product_id: select.value,
                        quantity: Number(qtyInput.value) || 1,
                        dosage_instructions: signaInput.value
                    });
                }
            });

            // Collect Compounds
            document.querySelectorAll('.compound-block').forEach(block => {
                const nameInput = block.querySelector('.compound-name-input');
                const packType = block.querySelector('.compound-pack-type');
                const packsInput = block.querySelector('.compound-packs-input');
                const signaInput = block.querySelector('.compound-signa-input');
                
                const compoundObj = {
                    compound_name: nameInput.value,
                    packaging_type: packType.value,
                    quantity_pack: Number(packsInput.value) || 10,
                    dosage_instructions: signaInput.value,
                    compounding_fee: 5000,
                    packaging_fee: 2000,
                    ingredients: []
                };

                block.querySelectorAll('.ingredient-row').forEach(ingRow => {
                    const select = ingRow.querySelector('.ingredient-prod-select');
                    const doseInput = ingRow.querySelector('.ingredient-dose-input');
                    const qtyInput = ingRow.querySelector('.ingredient-qty-input');
                    if (select.value) {
                        compoundObj.ingredients.push({
                            product_id: select.value,
                            dose_per_pack: doseInput.value,
                            quantity_used: Number(qtyInput.value) || 1
                        });
                    }
                });

                if (compoundObj.ingredients.length > 0) {
                    payload.compounds.push(compoundObj);
                }
            });

            try {
                const res = await fetch('/prescriptions', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alertBox.innerHTML = `<div class="alert alert-success py-2 small mb-3"><i class="fas fa-check-circle me-1"></i> ${result.message}</div>`;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    setTimeout(() => {
                        window.location.href = `/prescriptions/${result.prescription_id}`;
                    }, 1200);
                } else {
                    alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">${result.message}</div>`;
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Save & Queue Prescription';
                }
            } catch (err) {
                alertBox.innerHTML = `<div class="alert alert-danger py-2 small mb-3">Error: ${err.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Save & Queue Prescription';
            }
        });
    </script>
</body>
</html>
