<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediCore — Modern, high-performance Pharmacy Management System with clinical dispensing, automated FEFO inventory, high-speed POS, and compliance-ready audit trails.">
    <title><?= $title ?? 'MediCore — Intelligent Pharmacy Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="site-navbar">
        <div class="container-xxl px-3 px-md-4">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Brand Logo -->
                <a href="/" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <span class="fs-5 fw-bold text-dark brand-wordmark" style="letter-spacing: -0.03em;">MediCore</span>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="d-none d-lg-flex align-items-center gap-1">
                    <a href="#features" class="nav-link">Key Capabilities</a>
                    <a href="#interactive-demo" class="nav-link">Live Interactive Demo</a>
                    <a href="#solutions" class="nav-link">Solutions by Role</a>
                    <a href="#security" class="nav-link">Security & Privacy</a>
                    <a href="#faq" class="nav-link">FAQ</a>
                </nav>

                <!-- Auth / Actions -->
                <div class="d-flex align-items-center gap-2">
                    <?php if ($isLoggedIn && $currentUser): ?>
                        <a href="/profile" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-user-circle"></i>
                            <span class="d-none d-sm-inline"><?= htmlspecialchars($currentUser['full_name'] ?? 'Account') ?></span>
                        </a>
                        <form method="POST" action="/logout" class="d-inline">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i class="fas fa-sign-out-alt"></i> <span class="d-none d-sm-inline">Logout</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-sign-in-alt"></i> <span>Sign In</span>
                        </a>
                        <a href="/register" class="btn btn-primary btn-sm d-none d-sm-inline-flex">
                            <i class="fas fa-clinic-medical"></i> <span>Register Pharmacy</span>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button class="mobile-nav-toggle d-lg-none" type="button" onclick="toggleMobileMenu()" aria-label="Toggle navigation menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Collapsible Mobile Menu Drawer -->
            <div id="mobile-menu-drawer" class="mobile-menu-drawer d-none d-lg-none mt-2 rounded">
                <div class="d-flex flex-column gap-1">
                    <a href="#features" class="nav-link py-2" onclick="toggleMobileMenu()"><i class="fas fa-cubes text-teal me-2"></i> Key Capabilities</a>
                    <a href="#interactive-demo" class="nav-link py-2" onclick="toggleMobileMenu()"><i class="fas fa-play-circle text-teal me-2"></i> Live Interactive Demo</a>
                    <a href="#solutions" class="nav-link py-2" onclick="toggleMobileMenu()"><i class="fas fa-users-cog text-teal me-2"></i> Solutions by Role</a>
                    <a href="#security" class="nav-link py-2" onclick="toggleMobileMenu()"><i class="fas fa-shield-alt text-teal me-2"></i> Security & Privacy</a>
                    <a href="#faq" class="nav-link py-2" onclick="toggleMobileMenu()"><i class="fas fa-question-circle text-teal me-2"></i> FAQ</a>
                    <div class="pt-2 border-top d-sm-none">
                        <a href="/register" class="btn btn-primary btn-sm w-100"><i class="fas fa-clinic-medical"></i> Register New Pharmacy</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section (Centered Layout) -->
    <section class="section-padding bg-white border-bottom text-center">
        <div class="container-xxl px-3 px-md-4">
            <!-- Badge Pill -->
            <div class="d-flex justify-content-center mb-3">
                <div class="badge-pill-modern">
                    <i class="fas fa-shield-alt text-teal"></i>
                    <span class="text-secondary fw-semibold">Unified Pharmacy & Dispensing Operating System</span>
                </div>
            </div>

            <!-- Hero Title -->
            <h1 class="hero-title text-center mx-auto mb-3" style="max-width: 920px;">
                Clinical Precision. Real-Time Inventory. <span class="text-teal">Zero Expiry Waste.</span>
            </h1>

            <!-- Hero Subtitle -->
            <p class="section-subtitle text-center mx-auto mb-4" style="max-width: 760px;">
                The all-in-one pharmacy management software engineered for licensed pharmacists, retail drugstores, and healthcare clinics. Speed up checkout times, protect prescription safety, and automatically stop expiration write-offs with FEFO batch scheduling.
            </p>

            <!-- Hero CTA Group -->
            <div class="hero-cta-group d-flex flex-wrap justify-content-center align-items-center gap-3 mb-4 mb-lg-5">
                <a href="#interactive-demo" class="btn btn-primary btn-lg px-4 shadow-sm">
                    <i class="fas fa-play-circle me-1"></i> Try Live System Sandbox
                </a>
                <a href="/login" class="btn btn-outline-dark btn-lg px-4">
                    <i class="fas fa-sign-in-alt me-1"></i> Access Pharmacy Portal
                </a>
            </div>

            <!-- Hero Graphic: Centered Live Pharmacy Status Widget -->
            <div class="mx-auto mb-4 mb-lg-5 text-start" style="max-width: 960px;">
                <div class="card-modern p-3 p-md-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-tag badge-tag-emerald"><i class="fas fa-circle" style="font-size: 6px;"></i> Active Shift</span>
                            <span class="text-muted small fw-medium">MediCore Pharmacy Operating System — Main Branch</span>
                        </div>
                        <span class="font-mono small text-secondary bg-light px-2.5 py-1 rounded border">Live Operational Hub</span>
                    </div>

                    <!-- Grid of Live Operational Alerts -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box-solid icon-box-amber" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark">FEFO Expiry Sentinel</div>
                                        <div class="text-muted" style="font-size: 0.78rem;">Amoxicillin 500mg (Batch A102) expires in 18 days</div>
                                    </div>
                                </div>
                                <span class="badge-tag badge-tag-amber flex-shrink-0">Action Needed</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded border h-100 d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box-solid icon-box-teal" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                        <i class="fas fa-prescription"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark">Clinical Prescription Queue</div>
                                        <div class="text-muted" style="font-size: 0.78rem;">Dr. Hendra, Sp.PD — 2 items ready for review</div>
                                    </div>
                                </div>
                                <span class="badge-tag badge-tag-teal flex-shrink-0">Ready to Sign</span>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Operations Summary -->
                    <div class="p-3 bg-dark text-white rounded">
                        <div class="row text-center g-2 align-items-center">
                            <div class="col-4 border-end border-secondary">
                                <div class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.04em;">TODAY REVENUE</div>
                                <div class="fw-bold font-mono fs-5 text-white">Rp 8.450.000</div>
                            </div>
                            <div class="col-4 border-end border-secondary">
                                <div class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.04em;">ORDERS</div>
                                <div class="fw-bold font-mono fs-5 text-white">142 Prescriptions</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.04em;">LOW STOCK</div>
                                <div class="fw-bold font-mono fs-5 text-warning">4 Items</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust Stats Row (Full Width 4-Card Grid on Desktop) -->
            <div class="pt-4 border-top">
                <div class="row g-3 g-lg-4 text-center">
                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-lg-4 h-100 d-flex flex-column justify-content-center">
                            <div class="fs-3 fw-bold font-mono text-teal mb-1">99.98%</div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">Dispense Accuracy</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-lg-4 h-100 d-flex flex-column justify-content-center">
                            <div class="fs-3 fw-bold font-mono text-dark mb-1">&lt; 120ms</div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">Checkout Speed</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-lg-4 h-100 d-flex flex-column justify-content-center">
                            <div class="fs-3 fw-bold font-mono text-emerald mb-1">98%</div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">Less Expiry Waste</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card-modern p-3 p-lg-4 h-100 d-flex flex-column justify-content-center">
                            <div class="fs-3 fw-bold font-mono text-dark mb-1">24/7</div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">Cloud Availability</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Pharmacy Simulation Sandbox -->
    <section id="interactive-demo" class="section-padding bg-dark text-white">
        <div class="container-xxl px-3 px-md-4">
            <div class="text-center mb-4 mb-sm-5">
                <span class="badge-tag badge-tag-dark mb-2">
                    <i class="fas fa-desktop me-1"></i> Interactive Demo Engine
                </span>
                <h2 class="text-white section-title">Experience MediCore in Action</h2>
                <p class="text-light-secondary mx-auto section-subtitle">
                    Interact directly with real pharmacy operations. Select medications to test POS calculations, explore automated FEFO expiry scheduling, verify prescriptions, and view immutable audit events.
                </p>
            </div>

            <!-- Simulation Console Container -->
            <div class="simulation-console">
                <!-- Console Top Toolbar -->
                <div class="console-header flex-column flex-sm-row align-items-stretch align-items-sm-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="console-window-dots">
                            <span class="console-dot dot-red"></span>
                            <span class="console-dot dot-yellow"></span>
                            <span class="console-dot dot-green"></span>
                        </div>
                        <span class="badge-tag badge-tag-dark font-mono d-sm-none" style="font-size: 0.7rem;">
                            <i class="fas fa-shield-alt text-success me-1"></i> Interactive Sandbox
                        </span>
                    </div>

                    <!-- Scrollable Mobile Tabs -->
                    <div class="console-nav-tabs">
                        <button class="console-tab-btn active" onclick="switchSandboxTab(this, 'pos')">
                            <i class="fas fa-cash-register me-1"></i> 1. High-Speed POS
                        </button>
                        <button class="console-tab-btn" onclick="switchSandboxTab(this, 'fefo')">
                            <i class="fas fa-calendar-check me-1"></i> 2. FEFO Expiry Sentinel
                        </button>
                        <button class="console-tab-btn" onclick="switchSandboxTab(this, 'rx')">
                            <i class="fas fa-file-medical me-1"></i> 3. Clinical Rx Safety
                        </button>
                        <button class="console-tab-btn" onclick="switchSandboxTab(this, 'audit')">
                            <i class="fas fa-history me-1"></i> 4. Operational Audit Trail
                        </button>
                    </div>

                    <span class="badge-tag badge-tag-dark font-mono d-none d-sm-inline-flex" style="font-size: 0.75rem;">
                        <i class="fas fa-check-circle text-success me-1"></i> Live Interactive Sandbox
                    </span>
                </div>

                <!-- Tab 1: POS & Cart Simulator -->
                <div id="tab-pos" class="console-body">
                    <div class="row g-3 g-lg-4">
                        <!-- Product Selection Catalog -->
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center justify-content-between mb-2 mb-sm-3">
                                <span class="fw-bold text-white small">Tap Medication to Add to POS Cart:</span>
                                <span class="badge-tag badge-tag-teal" style="font-size: 0.7rem;">Barcode Ready</span>
                            </div>

                            <div class="row g-2 g-sm-3">
                                <!-- Drug 1 -->
                                <div class="col-12 col-sm-6">
                                    <div class="console-card h-100 d-flex flex-row flex-sm-column justify-content-between align-items-center align-items-sm-stretch gap-2">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                                <div class="fw-bold small">Amoxicillin 500mg</div>
                                                <span class="badge-tag badge-tag-dark font-mono" style="font-size: 0.68rem;">AMX-500</span>
                                            </div>
                                            <div class="text-light-secondary mb-1" style="font-size: 0.75rem;">Antibiotic • 10 Caps/Strip</div>
                                            <div class="font-mono fw-bold" style="color: #5eead4; font-size: 0.875rem;">Rp 18.500</div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mt-sm-2 flex-shrink-0" onclick="addToSandboxCart('Amoxicillin 500mg', 18500)">
                                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Drug 2 -->
                                <div class="col-12 col-sm-6">
                                    <div class="console-card h-100 d-flex flex-row flex-sm-column justify-content-between align-items-center align-items-sm-stretch gap-2">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                                <div class="fw-bold small">Paracetamol 650mg</div>
                                                <span class="badge-tag badge-tag-dark font-mono" style="font-size: 0.68rem;">PCT-650</span>
                                            </div>
                                            <div class="text-light-secondary mb-1" style="font-size: 0.75rem;">Analgesic • Blister</div>
                                            <div class="font-mono fw-bold" style="color: #5eead4; font-size: 0.875rem;">Rp 12.000</div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mt-sm-2 flex-shrink-0" onclick="addToSandboxCart('Paracetamol 650mg', 12000)">
                                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Drug 3 -->
                                <div class="col-12 col-sm-6">
                                    <div class="console-card h-100 d-flex flex-row flex-sm-column justify-content-between align-items-center align-items-sm-stretch gap-2">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                                <div class="fw-bold small">Omeprazole 20mg</div>
                                                <span class="badge-tag badge-tag-dark font-mono" style="font-size: 0.68rem;">OMP-020</span>
                                            </div>
                                            <div class="text-light-secondary mb-1" style="font-size: 0.75rem;">Proton Pump • Box 30</div>
                                            <div class="font-mono fw-bold" style="color: #5eead4; font-size: 0.875rem;">Rp 45.000</div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mt-sm-2 flex-shrink-0" onclick="addToSandboxCart('Omeprazole 20mg', 45000)">
                                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Drug 4 -->
                                <div class="col-12 col-sm-6">
                                    <div class="console-card h-100 d-flex flex-row flex-sm-column justify-content-between align-items-center align-items-sm-stretch gap-2">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                                <div class="fw-bold small">Cetirizine 10mg</div>
                                                <span class="badge-tag badge-tag-dark font-mono" style="font-size: 0.68rem;">CTZ-010</span>
                                            </div>
                                            <div class="text-light-secondary mb-1" style="font-size: 0.75rem;">Antihistamine • 10 Tabs</div>
                                            <div class="font-mono fw-bold" style="color: #5eead4; font-size: 0.875rem;">Rp 15.000</div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary mt-sm-2 flex-shrink-0" onclick="addToSandboxCart('Cetirizine 10mg', 15000)">
                                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live POS Register & Receipt Preview -->
                        <div class="col-lg-5">
                            <div class="console-card">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom border-secondary">
                                    <div class="fw-bold small"><i class="fas fa-receipt me-1"></i> Active Receipt Calculation</div>
                                    <button class="btn btn-sm btn-outline-dark text-light-secondary p-1 px-2" onclick="clearSandboxCart()">
                                        <i class="fas fa-trash-alt"></i> <span style="font-size: 0.75rem;">Clear</span>
                                    </button>
                                </div>

                                <!-- Cart Items List -->
                                <div id="sandbox-cart-list" class="mb-2" style="min-height: 100px; max-height: 160px; overflow-y: auto;">
                                    <div class="text-center text-muted py-3 small">
                                        Cart is empty. Tap items on the left to add medications.
                                    </div>
                                </div>

                                <!-- Financial Breakdown -->
                                <div class="pt-2 border-top border-secondary small font-mono">
                                    <div class="d-flex justify-content-between text-light-secondary mb-1">
                                        <span>Subtotal</span>
                                        <span id="sandbox-subtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-light-secondary mb-1">
                                        <span>PPN (11% VAT)</span>
                                        <span id="sandbox-tax">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-white fw-bold fs-6 mt-2 pt-2 border-top border-secondary">
                                        <span>Total Due:</span>
                                        <span id="sandbox-grandtotal" style="color: #5eead4;">Rp 0</span>
                                    </div>
                                </div>

                                <!-- Complete Sale Button -->
                                <button id="sandbox-checkout-btn" class="btn btn-primary w-100 mt-2.5" onclick="simulateSandboxCheckout()" disabled>
                                    <i class="fas fa-check-circle"></i> Complete Checkout & Print Receipt
                                </button>
                                <div id="sandbox-checkout-msg" class="mt-2 text-center text-success small font-mono d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: FEFO Expiry Sentinel -->
                <div id="tab-fefo" class="console-body d-none">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                        <div>
                            <h5 class="text-white mb-0 fs-6">First-Expired, First-Out (FEFO) Automated Priority</h5>
                            <span class="text-light-secondary small" style="font-size: 0.75rem;">System automatically dispatches earliest expiring batches first</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-dark active" onclick="filterFefo(this, 'all')">All</button>
                            <button class="btn btn-sm btn-outline-dark" onclick="filterFefo(this, 'critical')">&lt;30 Days</button>
                            <button class="btn btn-sm btn-outline-dark" onclick="filterFefo(this, 'warning')">&lt;60 Days</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0 font-mono small">
                            <thead>
                                <tr class="text-muted border-secondary" style="font-size: 0.75rem;">
                                    <th>BATCH LOT</th>
                                    <th>MEDICATION</th>
                                    <th>STOCK QTY</th>
                                    <th>EXPIRY DATE</th>
                                    <th>AUTOMATED STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="fefo-table-body" style="font-size: 0.8rem;">
                                <tr data-level="critical">
                                    <td><code>LOT-2024-C81</code></td>
                                    <td class="fw-bold">Amoxicillin 500mg</td>
                                    <td>45 Strips</td>
                                    <td class="text-danger">2026-09-04</td>
                                    <td><span class="badge-tag badge-tag-crimson">19 Days Remaining (Prioritize)</span></td>
                                </tr>
                                <tr data-level="warning">
                                    <td><code>LOT-2025-A12</code></td>
                                    <td class="fw-bold">Paracetamol Drop</td>
                                    <td>80 Bottles</td>
                                    <td class="text-warning">2026-10-12</td>
                                    <td><span class="badge-tag badge-tag-amber">57 Days Remaining (Active)</span></td>
                                </tr>
                                <tr data-level="safe">
                                    <td><code>LOT-2025-F99</code></td>
                                    <td class="fw-bold">Omeprazole 20mg</td>
                                    <td>320 Boxes</td>
                                    <td class="text-success">2027-08-20</td>
                                    <td><span class="badge-tag badge-tag-emerald">369 Days (Safe Reserve)</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Clinical Rx Verification -->
                <div id="tab-rx" class="console-body d-none">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="console-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-white small"><i class="fas fa-file-prescription me-1"></i> Digital Prescription Record</span>
                                    <span class="badge-tag badge-tag-teal font-mono" style="font-size: 0.7rem;">RX-2026-8890</span>
                                </div>
                                <div class="p-2.5 bg-dark rounded border border-secondary mb-2 small" style="font-size: 0.8rem;">
                                    <div><span class="text-muted">Patient:</span> <strong class="text-white">Budi Santoso (42 yo)</strong></div>
                                    <div><span class="text-muted">Prescribing Physician:</span> <strong class="text-white">Dr. Sarah Sp.PD</strong></div>
                                    <div><span class="text-muted">Physician License:</span> <code class="text-light-secondary">SIP.440/102/2024</code></div>
                                </div>
                                <div class="small">
                                    <div class="text-light-secondary mb-1" style="font-size: 0.75rem;">Prescribed Regimen:</div>
                                    <div class="p-2 bg-dark rounded border border-secondary mb-1 d-flex justify-content-between">
                                        <span>1. Ciprofloxacin 500mg (2x daily)</span>
                                        <span class="text-teal font-mono">20 Tabs</span>
                                    </div>
                                    <div class="p-2 bg-dark rounded border border-secondary d-flex justify-content-between">
                                        <span>2. Ibuprofen 400mg (3x daily prn)</span>
                                        <span class="text-teal font-mono">15 Tabs</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="console-card">
                                <div class="fw-bold text-white mb-2 small"><i class="fas fa-user-md me-1"></i> Pharmacist Safety Verification Checklist</div>
                                <div class="d-flex flex-column gap-1.5 mb-3 small" style="font-size: 0.78rem;">
                                    <div class="p-2 rounded bg-dark border border-secondary d-flex align-items-center gap-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>1. Physician Registry & SIP Validated</span>
                                    </div>
                                    <div class="p-2 rounded bg-dark border border-secondary d-flex align-items-center gap-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>2. Zero Contraindications & Safe Interactions</span>
                                    </div>
                                    <div class="p-2 rounded bg-dark border border-secondary d-flex align-items-center gap-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>3. Dosage & Frequency Parameters Confirmed</span>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100 btn-sm" onclick="alert('Dispensing Approved by Pharmacist! Prescription marked as DISPENSED in audit ledger.')">
                                    <i class="fas fa-stamp"></i> Approve & Sign Dispense Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Audit Trail Stream -->
                <div id="tab-audit" class="console-body d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="text-white mb-0 fs-6">Operational Event & Audit Log Stream</h5>
                        <span class="badge-tag badge-tag-emerald" style="font-size: 0.7rem;"><i class="fas fa-lock me-1"></i> Tamper-Evident</span>
                    </div>

                    <div class="console-card font-mono small" style="max-height: 200px; overflow-y: auto; font-size: 0.75rem;">
                        <div class="p-1.5 border-bottom border-secondary text-light-secondary">
                            <span class="text-muted">[10:14:02]</span> 
                            <span class="badge-tag badge-tag-teal">POS_CHECKOUT</span> 
                            Cashier Hendra processed Invoice <strong class="text-white">#INV-8910</strong> (Payment: Cash + QRIS)
                        </div>
                        <div class="p-1.5 border-bottom border-secondary text-light-secondary">
                            <span class="text-muted">[10:28:15]</span> 
                            <span class="badge-tag badge-tag-amber">FEFO_SCHEDULE</span> 
                            Batch <strong class="text-white">LOT-2024-C81</strong> auto-prioritized for front dispensing shelf
                        </div>
                        <div class="p-1.5 border-bottom border-secondary text-light-secondary">
                            <span class="text-muted">[10:45:30]</span> 
                            <span class="badge-tag badge-tag-blue">RX_DISPENSED</span> 
                            Pharmacist Sarah signed off Prescription <strong class="text-white">#RX-8890</strong>
                        </div>
                        <div class="p-1.5 text-light-secondary">
                            <span class="text-muted">[11:02:11]</span> 
                            <span class="badge-tag badge-tag-emerald">STOCK_RECEIPT</span> 
                            Goods Receipt Note <strong class="text-white">#GRN-442</strong> matched against Supplier Invoice
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Functional Capabilities Grid -->
    <section id="features" class="section-padding bg-white border-bottom">
        <div class="container-xxl px-3 px-md-4">
            <div class="text-center mb-4 mb-sm-5">
                <span class="badge-tag badge-tag-teal mb-2">
                    <i class="fas fa-cubes me-1"></i> Core Capabilities
                </span>
                <h2 class="section-title">Engineered for Safer, Faster Pharmacy Operations</h2>
                <p class="section-subtitle mx-auto">
                    Replace manual spreadsheets and disjointed tools with an integrated platform built specifically for pharmacy workflows.
                </p>
            </div>

            <div class="row g-3 g-sm-4">
                <!-- Feature 1 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-teal mb-3">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3 class="h5 mb-2">Automated FEFO & FIFO Control</h3>
                            <p class="text-secondary small mb-3">
                                Prevent expired stock losses by automatically routing medications with the earliest expiration dates to the front shelf. Get 30, 60, and 90-day early warning alerts.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal">Batch Lot Tracking</span>
                            <span class="badge-tag badge-tag-dark">Expiry Prevention</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-blue mb-3">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <h3 class="h5 mb-2">High-Throughput Point of Sale</h3>
                            <p class="text-secondary small mb-3">
                                Rapid barcode scanning, automated Indonesian 11% PPN tax calculations, flexible split payment (Cash, Transfer, QRIS), and thermal receipt printing with transaction hold/resume.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-blue">&lt; 120ms Checkout</span>
                            <span class="badge-tag badge-tag-dark">Queue Hold & Resume</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-emerald mb-3">
                                <i class="fas fa-notes-medical"></i>
                            </div>
                            <h3 class="h5 mb-2">Clinical Prescription Verification</h3>
                            <p class="text-secondary small mb-3">
                                Review and dispense prescriptions with doctor registry validation, dosage cross-checks, interaction alerts, and digital pharmacist authorization trails.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-emerald">Physician Registry</span>
                            <span class="badge-tag badge-tag-dark">Dosage Safety</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-amber mb-3">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                            <h3 class="h5 mb-2">Smart Procurement & Auto-Reorder</h3>
                            <p class="text-secondary small mb-3">
                                Trigger Purchase Orders when stock dips below safe thresholds. Match Goods Receipt Notes (GRN) directly against supplier shipments to stop shrinkage.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-amber">Auto Reorder</span>
                            <span class="badge-tag badge-tag-dark">Supplier Invoicing</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-dark mb-3">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3 class="h5 mb-2">Shift Security & Role Authorization</h3>
                            <p class="text-secondary small mb-3">
                                Maintain strict separation of duties across owners, pharmacists, cashiers, and warehouse clerks. Enforce cash drawer reconciliation and audit accountability.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-dark">Role Access Control</span>
                            <span class="badge-tag badge-tag-teal">Shift Reconciliation</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-modern p-3.5 p-sm-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box-solid icon-box-teal mb-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="h5 mb-2">Financial & Profit Intelligence</h3>
                            <p class="text-secondary small mb-3">
                                Track gross margins per drug SKU, identify slow vs. fast-moving products, detect dead inventory, and export financial summaries to Excel and PDF with one click.
                            </p>
                        </div>
                        <div class="border-top pt-2.5 d-flex gap-1 flex-wrap">
                            <span class="badge-tag badge-tag-teal">Gross Margin per SKU</span>
                            <span class="badge-tag badge-tag-dark">Excel / PDF Reports</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Solutions Section -->
    <section id="solutions" class="section-padding bg-light border-bottom">
        <div class="container-xxl px-3 px-md-4">
            <div class="text-center mb-4 mb-sm-5">
                <span class="badge-tag badge-tag-dark mb-2">
                    <i class="fas fa-users-cog me-1"></i> Tailored Workflows
                </span>
                <h2 class="section-title">Built for Every Member of Your Pharmacy Team</h2>
                <p class="section-subtitle mx-auto">
                    Dedicated tools and views optimized for speed, precision, and operational clarity.
                </p>
            </div>

            <div class="row g-3 g-sm-4">
                <!-- Role 1: Pharmacy Owner -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-modern p-3.5 p-sm-4 h-100">
                        <span class="badge-tag badge-tag-dark mb-2.5">Executive</span>
                        <h3 class="h5">Pharmacy Owner</h3>
                        <p class="text-secondary small mb-3">
                            Full business visibility, profit margin tracking per manufacturer, multi-branch oversight, and loss prevention intelligence.
                        </p>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Real-time gross margin</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Revenue trends</li>
                            <li><i class="fas fa-check text-success me-2"></i> Multi-branch analytics</li>
                        </ul>
                    </div>
                </div>

                <!-- Role 2: Licensed Pharmacist -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-modern p-3.5 p-sm-4 h-100">
                        <span class="badge-tag badge-tag-teal mb-2.5">Clinical</span>
                        <h3 class="h5">Apoteker / Pharmacist</h3>
                        <p class="text-secondary small mb-3">
                            Fast prescription validation, clinical drug interaction verification, patient medication history, and official dispensing logs.
                        </p>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Clinical review queue</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Doctor verification</li>
                            <li><i class="fas fa-check text-success me-2"></i> Dispense audit trails</li>
                        </ul>
                    </div>
                </div>

                <!-- Role 3: Cashier / Front Desk -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-modern p-3.5 p-sm-4 h-100">
                        <span class="badge-tag badge-tag-blue mb-2.5">Retail POS</span>
                        <h3 class="h5">Cashier / Front Desk</h3>
                        <p class="text-secondary small mb-3">
                            High-speed barcode scanning, multiple payment split options, instant change calculation, and seamless queue hold during rush hours.
                        </p>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Fast barcode scan</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> QRIS & Cash split</li>
                            <li><i class="fas fa-check text-success me-2"></i> Shift cash reconciliation</li>
                        </ul>
                    </div>
                </div>

                <!-- Role 4: Warehouse Officer -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card-modern p-3.5 p-sm-4 h-100">
                        <span class="badge-tag badge-tag-amber mb-2.5">Supply Chain</span>
                        <h3 class="h5">Gudang / Inventory</h3>
                        <p class="text-secondary small mb-3">
                            Supplier shipment receiving, Goods Receipt Note (GRN) verification, periodic stock opname, and FEFO expiry lot arrangement.
                        </p>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> GRN invoice matching</li>
                            <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Batch lot arrangement</li>
                            <li><i class="fas fa-check text-success me-2"></i> Stock opname auditing</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security, Privacy & Compliance Section -->
    <section id="security" class="section-padding bg-dark text-white">
        <div class="container-xxl px-3 px-md-4">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <span class="badge-tag badge-tag-dark mb-2"><i class="fas fa-shield-alt me-1"></i> Data Security & Governance</span>
                    <h2 class="text-white section-title mb-3">Enterprise-Grade Security & Healthcare Privacy</h2>
                    <p class="text-light-secondary mb-4 small">
                        Protecting your pharmacy data and patient prescription records with strict regulatory compliance, tamper-evident audit logs, and continuous data protection.
                    </p>

                    <div class="d-flex flex-column gap-2.5 small">
                        <div class="d-flex align-items-center gap-3 p-3 bg-dark-card border border-secondary rounded">
                            <i class="fas fa-lock text-teal fs-5" style="color: #5eead4;"></i>
                            <div>
                                <strong class="text-white">End-to-End Encryption & Session Guard</strong>
                                <div class="text-muted" style="font-size: 0.78rem;">All sensitive patient and transaction data encrypted in transit and at rest with auto session-lock.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-dark-card border border-secondary rounded">
                            <i class="fas fa-cloud-upload-alt text-teal fs-5" style="color: #5eead4;"></i>
                            <div>
                                <strong class="text-white">Automated Cloud Backups & Recovery</strong>
                                <div class="text-muted" style="font-size: 0.78rem;">Continuous daily backups ensure zero data loss during power outages or local hardware failures.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-dark-card border border-secondary rounded">
                            <i class="fas fa-clipboard-check text-teal fs-5" style="color: #5eead4;"></i>
                            <div>
                                <strong class="text-white">Regulatory Audit Compliance</strong>
                                <div class="text-muted" style="font-size: 0.78rem;">Tamper-evident logs of every stock movement, price override, and prescription dispensing for inspection readiness.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="console-card p-4">
                        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom border-secondary">
                            <span class="text-white fw-bold"><i class="fas fa-award me-1"></i> Compliance Highlights</span>
                            <span class="badge-tag badge-tag-emerald">VERIFIED</span>
                        </div>
                        <div class="small text-light-secondary" style="font-size: 0.85rem;">
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-teal"></i>
                                <div><strong>BPOM & Ministry of Health Standards</strong> — Full lot and expiry traceability.</div>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-teal"></i>
                                <div><strong>Indonesian Tax Ready</strong> — Built-in 11% PPN calculation and compliant invoicing.</div>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-teal"></i>
                                <div><strong>Role Segregation</strong> — Prevents unauthorized stock write-offs and discounts.</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-teal"></i>
                                <div><strong>Hardware Agnostic</strong> — Plug-and-play with any standard barcode scanner and thermal printer.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions (FAQ) Section -->
    <section id="faq" class="section-padding bg-white border-bottom">
        <div class="container-xl px-3 px-md-4" style="max-width: 980px;">
            <div class="text-center mb-4 mb-sm-5">
                <span class="badge-tag badge-tag-teal mb-2">
                    <i class="fas fa-question-circle me-1"></i> Questions & Answers
                </span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle mx-auto">
                    Common questions about deploying MediCore in your pharmacy, drugstore, or clinical dispensary.
                </p>
            </div>

            <div class="accordion" id="faqAccordion">
                <!-- FAQ 1 -->
                <div class="card-modern mb-3 border">
                    <h3 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button collapsed bg-white text-dark fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                            Can I use my existing barcode scanners and thermal printers?
                        </button>
                    </h3>
                    <div id="faqCollapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary pt-0 small">
                            Yes. MediCore is fully compatible with all standard USB and Bluetooth barcode scanners (EAN-13, QR codes) and 58mm / 80mm ESC/POS thermal receipt printers without requiring custom drivers.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="card-modern mb-3 border">
                    <h3 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed bg-white text-dark fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                            How does the FEFO (First-Expired, First-Out) system prevent drug losses?
                        </button>
                    </h3>
                    <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary pt-0 small">
                            Every time new stock is received, its batch number and expiration date are recorded. When cashiers or pharmacists scan an item, MediCore automatically allocates stock from the earliest expiring batch, sending early warning alerts at 90, 60, and 30 days before expiration.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="card-modern mb-3 border">
                    <h3 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed bg-white text-dark fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                            Can I manage multiple pharmacy branches under one account?
                        </button>
                    </h3>
                    <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary pt-0 small">
                            Yes. MediCore supports multi-branch management. Pharmacy owners can view consolidated revenue, compare sales between branches, and transfer inventory between stores seamlessly.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="card-modern mb-3 border">
                    <h3 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed bg-white text-dark fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4">
                            How does clinical prescription verification protect patient safety?
                        </button>
                    </h3>
                    <div id="faqCollapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary pt-0 small">
                            Prescriptions undergo a structured digital checklist where the prescribing physician's license is verified, potential drug-drug interactions are flagged, and dosage frequency is confirmed before the licensed pharmacist stamps and approves the dispense.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="card-modern mb-3 border">
                    <h3 class="accordion-header" id="faqHeading5">
                        <button class="accordion-button collapsed bg-white text-dark fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5">
                            Is patient data and transaction history secure?
                        </button>
                    </h3>
                    <div id="faqCollapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary pt-0 small">
                            All patient records and financial transactions are encrypted with enterprise-grade standards. Automatic daily backups ensure your records are always safe and available whenever regulatory health audits occur.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <section class="section-padding bg-white">
        <div class="container-xxl px-3 px-md-4">
            <div class="card-dark p-4 p-sm-5 text-center">
                <h2 class="text-white section-title mb-3">Ready to Modernize Your Pharmacy?</h2>
                <p class="text-light-secondary mx-auto mb-4" style="max-width: 600px;">
                    Start managing your drug inventory with clinical accuracy, zero expiration waste, and lightning-fast checkout.
                </p>
                <div class="d-flex justify-content-center gap-2.5 flex-column flex-sm-row">
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Access Pharmacy Portal
                    </a>
                    <a href="/register" class="btn btn-outline-dark btn-lg text-white border-secondary">
                        <i class="fas fa-plus-circle"></i> Register New Pharmacy
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container-xxl px-3 px-md-4">
            <div class="row g-4 mb-4 mb-sm-5">
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="icon-box-solid icon-box-teal" style="width: 34px; height: 34px; font-size: 1rem;">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                        <span class="fs-5 fw-bold text-white">MediCore</span>
                    </div>
                    <p class="small text-light-secondary mb-3">
                        Enterprise-grade pharmacy management platform engineered for clinical dispensing precision, FEFO inventory tracking, and regulatory audit compliance.
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-tag badge-tag-emerald"><i class="fas fa-check-circle me-1"></i> Cloud Services Operational</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2 offset-lg-1">
                    <div class="footer-heading">Platform</div>
                    <a href="#features" class="footer-link">Core Features</a>
                    <a href="#interactive-demo" class="footer-link">POS Sandbox</a>
                    <a href="#solutions" class="footer-link">Role Solutions</a>
                    <a href="#security" class="footer-link">Security & Privacy</a>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="footer-heading">Access</div>
                    <a href="/login" class="footer-link">Sign In to Portal</a>
                    <a href="/register" class="footer-link">Register Pharmacy</a>
                    <a href="/forgot-password" class="footer-link">Reset Password</a>
                    <a href="#faq" class="footer-link">Help & FAQ</a>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="footer-heading">Compliance & Standards</div>
                    <div class="compliance-item"><i class="fas fa-check-square text-teal me-1"></i> BPOM & Ministry of Health Standards</div>
                    <div class="compliance-item"><i class="fas fa-check-square text-teal me-1"></i> Automated FEFO Expiry Scheduling</div>
                    <div class="compliance-item"><i class="fas fa-check-square text-teal me-1"></i> Tamper-Evident Audit Trails</div>
                </div>
            </div>

            <div class="pt-3 border-top border-secondary d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small footer-bottom text-center text-sm-start">
                <div class="text-light-secondary">&copy; 2026 MediCore Systems Inc. All rights reserved.</div>
                <div class="text-light-secondary">The Intelligent Standard in Pharmacy Management Systems</div>
            </div>
        </div>
    </footer>

    <!-- Interactive Simulator JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Mobile Menu Drawer
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-menu-drawer');
            if (drawer) {
                drawer.classList.toggle('d-none');
            }
        }

        // Interactive Sandbox Tab Switching
        function switchSandboxTab(btnElement, tabName) {
            document.querySelectorAll('.console-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.console-body').forEach(body => body.classList.add('d-none'));

            if (btnElement) {
                btnElement.classList.add('active');
            }
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.classList.remove('d-none');
            }
        }

        // Live POS Cart Simulation State
        const sandboxCart = [];

        function formatIDR(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function addToSandboxCart(name, price) {
            const existing = sandboxCart.find(item => item.name === name);
            if (existing) {
                existing.qty += 1;
            } else {
                sandboxCart.push({ name, price, qty: 1 });
            }
            renderSandboxCart();
        }

        function updateSandboxQty(index, change) {
            if (sandboxCart[index]) {
                sandboxCart[index].qty += change;
                if (sandboxCart[index].qty <= 0) {
                    sandboxCart.splice(index, 1);
                }
            }
            renderSandboxCart();
        }

        function clearSandboxCart() {
            sandboxCart.length = 0;
            renderSandboxCart();
            const msg = document.getElementById('sandbox-checkout-msg');
            if (msg) msg.classList.add('d-none');
        }

        function renderSandboxCart() {
            const list = document.getElementById('sandbox-cart-list');
            const subtotalEl = document.getElementById('sandbox-subtotal');
            const taxEl = document.getElementById('sandbox-tax');
            const grandtotalEl = document.getElementById('sandbox-grandtotal');
            const checkoutBtn = document.getElementById('sandbox-checkout-btn');

            if (sandboxCart.length === 0) {
                list.innerHTML = `<div class="text-center text-muted py-3 small">Cart is empty. Tap items on the left to add medications.</div>`;
                subtotalEl.textContent = 'Rp 0';
                taxEl.textContent = 'Rp 0';
                grandtotalEl.textContent = 'Rp 0';
                checkoutBtn.disabled = true;
                return;
            }

            let subtotal = 0;
            list.innerHTML = sandboxCart.map((item, idx) => {
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;
                return `
                    <div class="d-flex justify-content-between align-items-center p-2 mb-1 bg-dark rounded border border-secondary font-mono small">
                        <div style="max-width: 55%;">
                            <div class="text-white fw-bold text-truncate" style="font-size: 0.8rem;">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">${formatIDR(item.price)} × ${item.qty}</div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button class="qty-stepper-btn" onclick="updateSandboxQty(${idx}, -1)">-</button>
                            <span class="text-white px-1.5 font-mono" style="min-width: 20px; text-align: center;">${item.qty}</span>
                            <button class="qty-stepper-btn" onclick="updateSandboxQty(${idx}, 1)">+</button>
                            <span class="text-teal ms-1 fw-bold" style="color: #5eead4; font-size: 0.8rem;">${formatIDR(itemTotal)}</span>
                        </div>
                    </div>
                `;
            }).join('');

            const tax = Math.round(subtotal * 0.11);
            const grandTotal = subtotal + tax;

            subtotalEl.textContent = formatIDR(subtotal);
            taxEl.textContent = formatIDR(tax);
            grandtotalEl.textContent = formatIDR(grandTotal);
            checkoutBtn.disabled = false;
        }

        function simulateSandboxCheckout() {
            const msg = document.getElementById('sandbox-checkout-msg');
            msg.innerHTML = `<i class="fas fa-check-circle"></i> Trx #TRX-${Math.floor(1000 + Math.random() * 9000)} Completed! Struk Printed.`;
            msg.classList.remove('d-none');
            setTimeout(() => {
                clearSandboxCart();
            }, 3500);
        }

        function filterFefo(btnElement, level) {
            if (btnElement) {
                btnElement.parentElement.querySelectorAll('button').forEach(b => {
                    b.classList.remove('active', 'btn-dark');
                    b.classList.add('btn-outline-dark');
                });
                btnElement.classList.remove('btn-outline-dark');
                btnElement.classList.add('active', 'btn-dark');
            }

            const rows = document.querySelectorAll('#fefo-table-body tr');
            rows.forEach(row => {
                if (level === 'all' || row.getAttribute('data-level') === level) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
