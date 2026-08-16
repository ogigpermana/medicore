<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'POS Cashier Register — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
    <style>
        .pos-grid-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 991.98px) {
            .pos-grid-container {
                display: block;
            }
            .pos-cart-panel {
                margin-top: 0;
            }
        }
        .pos-cart-panel {
            position: sticky;
            top: 80px;
        }
        .product-card-pos {
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid var(--border-light);
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: 0.75rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
        }
        .product-card-pos:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
            transform: translateY(-2px);
        }
        .product-card-pos:active {
            transform: scale(0.98);
        }
        .quick-pay-btn {
            font-family: var(--font-mono);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 0.65rem;
            border: 1px solid var(--border-medium);
            background: #ffffff;
            border-radius: var(--radius-md);
            color: var(--text-primary);
        }
        .quick-pay-btn:hover {
            background-color: var(--primary-subtle);
            border-color: var(--primary-border);
            color: var(--primary-dark);
        }
        .thermal-receipt-box {
            font-family: var(--font-mono);
            background: #fafafa;
            border: 1px dashed #cbd5e1;
            padding: 1.25rem;
            font-size: 0.825rem;
            line-height: 1.4;
            max-width: 320px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-page">

<?php
    $currentRole = strtolower($role ?? $user['role_name'] ?? $user['role'] ?? 'cashier');
    $currentName = $user['full_name'] ?? $user['name'] ?? 'Staff User';
?>
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
                <a href="/pos" class="sidebar-menu-link active">
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
                    <a href="/inventory/products" class="sidebar-menu-link">
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

        <!-- Main Content -->
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
                            <li><a class="dropdown-item" href="/pos/history"><i class="fas fa-receipt text-muted"></i> Sales History</a></li>
                            <li><a class="dropdown-item" href="/inventory/products"><i class="fas fa-boxes text-muted"></i> Medications</a></li>
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

            <!-- POS Body Content -->
            <main class="app-content">
                <!-- Mobile Navigation Switcher (Catalog vs Cart) -->
                <div class="mobile-pos-nav" id="mobilePosNav">
                    <button type="button" class="mobile-pos-nav-btn active" id="tabBtnCatalog" onclick="switchMobilePosTab('catalog')">
                        <i class="fas fa-boxes me-1"></i> Catalog Items
                    </button>
                    <button type="button" class="mobile-pos-nav-btn" id="tabBtnCart" onclick="switchMobilePosTab('cart')">
                        <i class="fas fa-shopping-cart me-1"></i> Cart (<span id="mobileTabCartCount">0</span>)
                    </button>
                </div>

                <div class="pos-grid-container">
                    <!-- Left Section: Barcode & Product Catalog -->
                    <div id="posCatalogSection">
                        <!-- Barcode Scanner & Search Bar -->
                        <div class="card-modern p-3 mb-3">
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-barcode"></i></span>
                                <input type="text" id="barcodeSearchInput" class="form-control border-start-0 ps-0 font-mono" 
                                       placeholder="Scan barcode or type medication name / SKU..." autofocus>
                                <button class="btn btn-dark btn-sm px-3" type="button" onclick="handleManualSearch()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- Touch-Scrollable Category Filter Chips -->
                            <div class="category-scroll-chips" id="categoryChips">
                                <button type="button" class="btn btn-sm btn-dark py-1 px-2.5 text-nowrap cat-filter-btn active" onclick="filterCategory('', this)">All Items</button>
                                <?php foreach ($categories as $cat): ?>
                                    <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2.5 text-nowrap cat-filter-btn" onclick="filterCategory('<?= $cat['id'] ?>', this)">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Product Cards Grid -->
                        <div class="row g-2 g-sm-3" id="posProductGrid">
                            <?php foreach ($products as $p): ?>
                                <div class="col-6 col-sm-6 col-md-4 col-xl-3 product-item-col" data-cat="<?= $p['category_id'] ?>" data-name="<?= strtolower($p['name'] . ' ' . $p['sku'] . ' ' . ($p['barcode'] ?? '')) ?>">
                                    <div class="product-card-pos" onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start mb-1 gap-1">
                                                <span class="badge-tag badge-tag-dark font-mono text-truncate" style="font-size: 0.62rem; max-width: 90px;"><?= htmlspecialchars($p['sku']) ?></span>
                                                <?php if ($p['requires_prescription']): ?>
                                                    <span class="badge-tag badge-tag-crimson flex-shrink-0" style="font-size: 0.6rem;">Rx</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="fw-bold text-dark font-sans small mb-1 text-truncate" title="<?= htmlspecialchars($p['name']) ?>">
                                                <?= htmlspecialchars($p['name']) ?>
                                            </div>
                                            <div class="text-muted font-mono" style="font-size: 0.7rem;">
                                                Stok: <strong class="<?= ($p['stock_quantity'] <= $p['min_stock']) ? 'text-danger' : 'text-teal' ?>"><?= $p['stock_quantity'] ?> <?= htmlspecialchars($p['unit_symbol'] ?? '') ?></strong>
                                            </div>
                                        </div>
                                        <div class="pt-2 border-top mt-2 d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-teal font-mono" style="font-size: 0.8rem;">Rp <?= number_format($p['sell_price'], 0, ',', '.') ?></span>
                                            <span class="btn btn-sm btn-outline-primary py-0 px-2 flex-shrink-0" style="font-size: 0.75rem;"><i class="fas fa-plus"></i></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Right Section: Shopping Cart & Checkout Panel -->
                    <div class="pos-cart-panel" id="posCartSection">
                        <div class="card-modern p-3 p-sm-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                                <div class="fw-bold text-dark">
                                    <i class="fas fa-shopping-bag text-teal me-1"></i> Current Order
                                </div>
                                <button type="button" class="btn btn-sm text-danger py-0 px-1" onclick="clearCart()" title="Empty Cart">
                                    <i class="fas fa-trash-alt"></i> Clear
                                </button>
                            </div>

                            <!-- Cart Line Items Container -->
                            <div id="cartItemsList" class="overflow-y-auto mb-3" style="max-height: 280px; min-height: 140px;">
                                <div class="text-center py-4 text-muted small" id="emptyCartMessage">
                                    <i class="fas fa-shopping-cart fs-4 d-block mb-1 text-secondary"></i>
                                    Cart is empty. Scan barcode or tap items to add.
                                </div>
                            </div>

                            <!-- Calculations Breakdown -->
                            <div class="p-3 bg-light rounded border mb-3 font-mono small">
                                <div class="d-flex justify-content-between mb-1 text-secondary">
                                    <span>Subtotal:</span>
                                    <span id="cartSubtotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1 text-secondary">
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" id="taxToggle" onchange="calculateTotals()">
                                        <label class="form-check-label" for="taxToggle" style="font-size: 0.75rem;">PPN 11%</label>
                                    </div>
                                    <span id="cartTax">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1 text-secondary">
                                    <span>Discount:</span>
                                    <div class="d-flex align-items-center gap-1" style="max-width: 110px;">
                                        <input type="number" id="discountInput" class="form-control form-control-sm text-end p-1 font-mono" value="0" min="0" oninput="calculateTotals()">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top fw-bold text-dark fs-6">
                                    <span>Total Due:</span>
                                    <span class="text-teal" id="cartGrandTotal">Rp 0</span>
                                </div>
                            </div>

                            <!-- Customer Info (Optional) -->
                            <div class="mb-3">
                                <input type="text" id="customerNameInput" class="form-control form-control-sm mb-1" placeholder="Patient Name (Optional)">
                                <input type="text" id="customerPhoneInput" class="form-control form-control-sm font-mono" placeholder="WhatsApp / Phone (Optional)">
                            </div>

                            <!-- Charge Button -->
                            <button type="button" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-between" id="chargeBtn" onclick="openPaymentModal()" disabled>
                                <span><i class="fas fa-credit-card me-1"></i> PAY NOW</span>
                                <span id="chargeBtnAmount">Rp 0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Floating Mobile Bottom Cart Bar (Shows when items are in cart on mobile) -->
    <div class="mobile-floating-cart-bar" id="mobileFloatingBar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted" style="font-size: 0.68rem; font-weight: 600;">TOTAL ORDER</div>
                <div class="fw-bold text-teal font-mono fs-6" id="floatingCartTotal">Rp 0</div>
            </div>
            <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold" id="floatingPayBtn" onclick="handleMobilePayAction()">
                <i class="fas fa-shopping-cart me-1"></i> Review & Pay (<span id="floatingCartCount">0</span>)
            </button>
        </div>
    </div>

    <!-- Modal: Payment Processing -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center p-3 bg-light rounded border mb-3">
                        <div class="text-muted small">TOTAL AMOUNT DUE</div>
                        <div class="fs-3 fw-bold text-teal font-mono" id="modalPayDue">Rp 0</div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <label class="form-label small fw-bold">Select Payment Method</label>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payMethodCash" value="cash" checked onchange="switchPayMethod('cash')">
                            <label class="btn btn-outline-dark btn-sm w-100 py-2" for="payMethodCash">
                                <i class="fas fa-money-bill-wave d-block mb-1"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payMethodQris" value="qris" onchange="switchPayMethod('qris')">
                            <label class="btn btn-outline-dark btn-sm w-100 py-2" for="payMethodQris">
                                <i class="fas fa-qrcode d-block mb-1"></i> QRIS
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payMethodTransfer" value="transfer" onchange="switchPayMethod('transfer')">
                            <label class="btn btn-outline-dark btn-sm w-100 py-2" for="payMethodTransfer">
                                <i class="fas fa-university d-block mb-1"></i> Transfer
                            </label>
                        </div>
                    </div>

                    <!-- Cash Input Section -->
                    <div id="cashInputSection">
                        <label class="form-label small fw-bold">Cash Tendered (Rp)</label>
                        <input type="number" id="cashTenderedInput" class="form-control form-control-lg font-mono mb-2" placeholder="0" oninput="calculateChange()">
                        
                        <!-- Quick Cash Shortcuts -->
                        <div class="d-flex gap-1.5 flex-wrap mb-3" id="quickCashButtons"></div>

                        <div class="p-3 bg-subtle rounded border">
                            <div class="d-flex justify-content-between font-mono">
                                <span class="text-secondary">Cash Change:</span>
                                <span class="fw-bold text-dark fs-6" id="cashChangeDisplay">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- QRIS Mockup Section -->
                    <div id="qrisSection" class="text-center p-3 bg-light rounded border d-none">
                        <div class="fw-bold mb-2">Scan QRIS to Complete Payment</div>
                        <div class="bg-white p-3 d-inline-block rounded border shadow-sm mb-2">
                            <i class="fas fa-qrcode text-dark" style="font-size: 90px !important;"></i>
                        </div>
                        <div class="text-muted small font-mono">NMID: ID10293847562 • Apotek Central</div>
                    </div>

                    <!-- Transfer Section -->
                    <div id="transferSection" class="p-3 bg-light rounded border d-none">
                        <div class="small fw-bold mb-1">Direct Bank Account:</div>
                        <div class="font-mono small text-dark mb-1">BCA: <strong>8830-192-881</strong> (PT MediCore Farma)</div>
                        <div class="font-mono small text-dark">Mandiri: <strong>123-00-9988-1122</strong></div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" id="confirmCheckoutBtn" onclick="submitCheckout()">
                        <i class="fas fa-check-circle me-1"></i> Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Thermal Receipt & Success -->
    <div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark">Transaction Completed!</h5>
                    </div>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="thermal-receipt-box mb-3 text-start" id="thermalReceiptContent">
                        <!-- Filled by JS -->
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="printReceipt()"><i class="fas fa-print me-1"></i> Print Receipt</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="resetAfterSale()"><i class="fas fa-plus me-1"></i> New Transaction</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Open Shift -->
    <div class="modal fade" id="openShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">Open Cashier Shift Drawer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Opening Cash Drawer Balance (Rp) *</label>
                        <input type="number" id="openShiftCashInput" class="form-control font-mono" placeholder="e.g. 200000" value="200000" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <input type="text" id="openShiftNotesInput" class="form-control" placeholder="Morning shift drawer setup">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="submitOpenShift()"><i class="fas fa-key me-1"></i> Start Shift</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Close Shift -->
    <div class="modal fade" id="closeShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">End Cashier Shift & Balance Drawer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Physical Cash Count in Drawer (Rp) *</label>
                        <input type="number" id="closeShiftCashInput" class="form-control font-mono" placeholder="Count physical bills & coins" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Handover Notes</label>
                        <input type="text" id="closeShiftNotesInput" class="form-control" placeholder="Drawer balanced for handover">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="submitCloseShift()"><i class="fas fa-lock me-1"></i> Close & Reconcile</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Import Digital Prescription (Rx) -->
    <div class="modal fade" id="importRxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-xl);">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-file-medical text-teal me-2"></i>Import Prescription (Rx)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Enter the Doctor Prescription number to automatically load patient identity, prescribed medications, and compounding fees directly into the cart:
                    </p>
                    <div class="input-group mb-3">
                        <input type="text" id="rxLookupInput" class="form-control font-mono" placeholder="e.g. RX-<?= date('Ymd') ?>-0001">
                        <button type="button" class="btn btn-primary" onclick="lookupAndLoadRx(document.getElementById('rxLookupInput').value)">
                            <i class="fas fa-search me-1"></i> Load Rx
                        </button>
                    </div>
                    <div id="rxLookupStatusBox"></div>
                </div>
                <div class="modal-footer border-top p-3 bg-light">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
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

        let cart = [];
        let grandTotal = 0;
        let lastSaleData = null;
        let currentMobileTab = 'catalog';

        function formatIDR(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        // Mobile Tab Switcher Logic
        function switchMobilePosTab(tab) {
            currentMobileTab = tab;
            const catalogSec = document.getElementById('posCatalogSection');
            const cartSec = document.getElementById('posCartSection');
            const btnCat = document.getElementById('tabBtnCatalog');
            const btnCart = document.getElementById('tabBtnCart');

            if (window.innerWidth < 992) {
                if (tab === 'cart') {
                    catalogSec.style.display = 'none';
                    cartSec.style.display = 'block';
                    btnCat.classList.remove('active');
                    btnCart.classList.add('active');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    catalogSec.style.display = 'block';
                    cartSec.style.display = 'none';
                    btnCat.classList.add('active');
                    btnCart.classList.remove('active');
                }
            } else {
                catalogSec.style.display = 'block';
                cartSec.style.display = 'block';
            }
        }

        // Auto adjust on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                document.getElementById('posCatalogSection').style.display = 'block';
                document.getElementById('posCartSection').style.display = 'block';
            } else {
                switchMobilePosTab(currentMobileTab);
            }
        });

        // Initialize mobile state on load
        if (window.innerWidth < 992) {
            switchMobilePosTab('catalog');
        }

        function handleMobilePayAction() {
            if (window.innerWidth < 992) {
                switchMobilePosTab('cart');
            }
            openPaymentModal();
        }

        // Add Product to Cart
        function addToCart(product) {
            const existingIndex = cart.findIndex(item => item.product_id === product.id);

            if (existingIndex > -1) {
                if (cart[existingIndex].quantity + 1 > product.stock_quantity) {
                    alert(`Cannot add more. Stock limit for ${product.name} is ${product.stock_quantity}.`);
                    return;
                }
                cart[existingIndex].quantity += 1;
            } else {
                if (product.stock_quantity <= 0) {
                    alert(`${product.name} is currently out of stock.`);
                    return;
                }
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    unit_price: Number(product.sell_price),
                    quantity: 1,
                    max_stock: product.stock_quantity,
                    unit_symbol: product.unit_symbol || 'unit'
                });
            }
            renderCart();
        }

        function updateCartQty(index, delta) {
            const item = cart[index];
            const newQty = item.quantity + delta;

            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > item.max_stock) {
                alert(`Maximum available stock is ${item.max_stock}.`);
            } else {
                item.quantity = newQty;
            }
            renderCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function renderCart() {
            const list = document.getElementById('cartItemsList');
            const chargeBtn = document.getElementById('chargeBtn');
            const floatingBar = document.getElementById('mobileFloatingBar');
            const totalItemsCount = cart.reduce((sum, i) => sum + i.quantity, 0);

            document.getElementById('mobileTabCartCount').textContent = totalItemsCount;
            document.getElementById('floatingCartCount').textContent = totalItemsCount;

            if (cart.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-4 text-muted small" id="emptyCartMessage">
                        <i class="fas fa-shopping-cart fs-4 d-block mb-1 text-secondary"></i>
                        Cart is empty. Scan barcode or tap items to add.
                    </div>
                `;
                chargeBtn.disabled = true;
                if (floatingBar) floatingBar.style.display = 'none';
                document.body.classList.remove('has-mobile-cart');
                calculateTotals();
                return;
            }

            chargeBtn.disabled = false;
            if (floatingBar && window.innerWidth < 992) {
                floatingBar.style.display = 'block';
                document.body.classList.add('has-mobile-cart');
            }

            let html = cart.map((item, index) => `
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="text-truncate me-2" style="max-width: 140px;">
                        <div class="fw-bold text-dark font-sans small text-truncate">${item.name}</div>
                        <div class="text-muted font-mono" style="font-size: 0.7rem;">${formatIDR(item.unit_price)} / ${item.unit_symbol}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-dark py-0 px-2" style="min-height: 28px;" onclick="updateCartQty(${index}, -1)">-</button>
                            <span class="btn btn-light py-0 px-1.5 font-mono fw-bold disabled" style="font-size: 0.75rem; min-width: 24px;">${item.quantity}</span>
                            <button type="button" class="btn btn-outline-dark py-0 px-2" style="min-height: 28px;" onclick="updateCartQty(${index}, 1)">+</button>
                        </div>
                        <div class="fw-bold text-teal font-mono small text-end" style="min-width: 65px; font-size: 0.75rem;">
                            ${formatIDR(item.unit_price * item.quantity)}
                        </div>
                        <button type="button" class="btn btn-sm text-danger p-0 ms-1" onclick="removeFromCart(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            list.innerHTML = html;
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
            let discount = Number(document.getElementById('discountInput').value) || 0;
            let taxable = Math.max(0, subtotal - discount);
            let tax = document.getElementById('taxToggle').checked ? (taxable * 0.11) : 0;
            grandTotal = taxable + tax;

            document.getElementById('cartSubtotal').textContent = formatIDR(subtotal);
            document.getElementById('cartTax').textContent = formatIDR(tax);
            document.getElementById('cartGrandTotal').textContent = formatIDR(grandTotal);
            document.getElementById('chargeBtnAmount').textContent = formatIDR(grandTotal);
            document.getElementById('floatingCartTotal').textContent = formatIDR(grandTotal);
        }

        // Category Filter with active button class
        function filterCategory(catId, btnEl) {
            if (btnEl) {
                document.querySelectorAll('.cat-filter-btn').forEach(b => {
                    b.classList.remove('btn-dark', 'active');
                    b.classList.add('btn-outline-dark');
                });
                btnEl.classList.remove('btn-outline-dark');
                btnEl.classList.add('btn-dark', 'active');
            }

            const cols = document.querySelectorAll('.product-item-col');
            cols.forEach(col => {
                if (!catId || col.getAttribute('data-cat') === catId) {
                    col.classList.remove('d-none');
                } else {
                    col.classList.add('d-none');
                }
            });
        }

        // Search Input Filter
        document.getElementById('barcodeSearchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cols = document.querySelectorAll('.product-item-col');
            cols.forEach(col => {
                const name = col.getAttribute('data-name');
                if (!query || name.includes(query)) {
                    col.classList.remove('d-none');
                } else {
                    col.classList.add('d-none');
                }
            });
        });

        // Fast Barcode Lookup via Enter
        document.getElementById('barcodeSearchInput').addEventListener('keydown', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (!code) return;

                try {
                    const res = await fetch(`/api/inventory/lookup?code=${encodeURIComponent(code)}`);
                    const result = await res.json();
                    if (result.success && result.product) {
                        addToCart(result.product);
                        this.value = '';
                    } else {
                        handleManualSearch();
                    }
                } catch (err) {
                    handleManualSearch();
                }
            }
        });

        function handleManualSearch() {
            const query = document.getElementById('barcodeSearchInput').value.toLowerCase().trim();
            const cols = document.querySelectorAll('.product-item-col');
            let firstMatch = null;
            cols.forEach(col => {
                const name = col.getAttribute('data-name');
                if (query && name.includes(query) && !firstMatch) {
                    firstMatch = col;
                }
            });
            if (firstMatch) {
                firstMatch.querySelector('.product-card-pos').click();
                document.getElementById('barcodeSearchInput').value = '';
            }
        }

        // Payment Modal & Calculations
        function openPaymentModal() {
            if (cart.length === 0) return;
            document.getElementById('modalPayDue').textContent = formatIDR(grandTotal);
            document.getElementById('cashTenderedInput').value = grandTotal;
            generateQuickCashButtons(grandTotal);
            calculateChange();
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        function switchPayMethod(method) {
            document.getElementById('cashInputSection').classList.toggle('d-none', method !== 'cash');
            document.getElementById('qrisSection').classList.toggle('d-none', method !== 'qris');
            document.getElementById('transferSection').classList.toggle('d-none', method !== 'transfer');
        }

        function generateQuickCashButtons(total) {
            const container = document.getElementById('quickCashButtons');
            const denoms = [total, 20000, 50000, 100000, 200000, 500000].filter((v, i, a) => v >= total && a.indexOf(v) === i).sort((a, b) => a - b);
            
            container.innerHTML = denoms.map(d => `
                <button type="button" class="quick-pay-btn" onclick="setCashTendered(${d})">${(d === total) ? 'Exact' : formatIDR(d)}</button>
            `).join('');
        }

        function setCashTendered(amount) {
            document.getElementById('cashTenderedInput').value = amount;
            calculateChange();
        }

        function calculateChange() {
            const tendered = Number(document.getElementById('cashTenderedInput').value) || 0;
            const change = Math.max(0, tendered - grandTotal);
            document.getElementById('cashChangeDisplay').textContent = formatIDR(change);
        }

        // Submit POS Checkout
        async function submitCheckout() {
            const btn = document.getElementById('confirmCheckoutBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

            const method = document.querySelector('input[name="payment_method"]:checked').value;
            const tendered = Number(document.getElementById('cashTenderedInput').value) || grandTotal;
            const discount = Number(document.getElementById('discountInput').value) || 0;
            const includeTax = document.getElementById('taxToggle').checked;

            const payload = {
                customer_name: document.getElementById('customerNameInput').value,
                customer_phone: document.getElementById('customerPhoneInput').value,
                discount_amount: discount,
                include_tax: includeTax ? 1 : 0,
                payment_method: method,
                cash_tendered: (method === 'cash') ? tendered : grandTotal,
                items: cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }))
            };

            try {
                const res = await fetch('/pos/checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    lastSaleData = result.data;
                    renderThermalReceipt(result.data);
                    new bootstrap.Modal(document.getElementById('receiptModal')).show();
                } else {
                    alert(`Checkout error: ${result.message}`);
                }
            } catch (err) {
                alert(`Network error: ${err.message}`);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Complete Sale';
            }
        }

        function renderThermalReceipt(sale) {
            let itemLines = sale.items.map(i => `
                <div class="d-flex justify-content-between">
                    <span>${i.quantity}x ${i.product_name}</span>
                    <span>${formatIDR(i.total)}</span>
                </div>
                <div class="text-muted" style="font-size: 0.7rem;">Lot: ${i.batch_number}</div>
            `).join('');

            document.getElementById('thermalReceiptContent').innerHTML = `
                <div class="text-center border-bottom pb-2 mb-2">
                    <div class="fw-bold fs-6">MEDICORE PHARMACY</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Jl. Kesehatan Raya No. 10, Jakarta</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Telp: (021) 555-0199 • SIPA: 446/2026</div>
                </div>
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.72rem;">
                    <span>Inv: ${sale.invoice_number}</span>
                    <span>${sale.created_at}</span>
                </div>
                <div class="mb-2" style="font-size: 0.72rem;">Customer: ${sale.customer_name}</div>
                <div class="border-top border-bottom py-2 my-2">
                    ${itemLines}
                </div>
                <div class="d-flex justify-content-between"><span>Subtotal:</span><span>${formatIDR(sale.subtotal)}</span></div>
                ${sale.tax_amount > 0 ? `<div class="d-flex justify-content-between"><span>PPN (11%):</span><span>${formatIDR(sale.tax_amount)}</span></div>` : ''}
                ${sale.discount_amount > 0 ? `<div class="d-flex justify-content-between"><span>Discount:</span><span>-${formatIDR(sale.discount_amount)}</span></div>` : ''}
                <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-1 mt-1"><span>TOTAL:</span><span>${formatIDR(sale.total_amount)}</span></div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;"><span>Payment (${sale.payment_method.toUpperCase()}):</span><span>${formatIDR(sale.cash_tendered)}</span></div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;"><span>Change:</span><span>${formatIDR(sale.cash_change)}</span></div>
                <div class="text-center pt-3 border-top mt-2 text-muted" style="font-size: 0.7rem;">
                    <div>Thank you for choosing MediCore.</div>
                    <div>Semoga Lekas Sembuh!</div>
                </div>
            `;
        }

        function printReceipt() {
            if (lastSaleData && lastSaleData.sale_id) {
                window.open(`/pos/receipt/${lastSaleData.sale_id}`, '_blank');
            } else {
                window.print();
            }
        }

        function resetAfterSale() {
            bootstrap.Modal.getInstance(document.getElementById('receiptModal')).hide();
            clearCart();
            window.location.reload();
        }

        // Shift Modals
        function showOpenShiftModal() { new bootstrap.Modal(document.getElementById('openShiftModal')).show(); }
        function showCloseShiftModal() { new bootstrap.Modal(document.getElementById('closeShiftModal')).show(); }

        async function submitOpenShift() {
            const opening = document.getElementById('openShiftCashInput').value;
            const notes = document.getElementById('openShiftNotesInput').value;
            const res = await fetch('/pos/shift/open', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ opening_cash: opening, notes })
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        }

        async function submitCloseShift() {
            const closing = document.getElementById('closeShiftCashInput').value;
            const notes = document.getElementById('closeShiftNotesInput').value;
            const res = await fetch('/pos/shift/close', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ closing_cash: closing, notes })
            });
            const result = await res.json();
            if (result.success) window.location.reload();
            else alert(result.message);
        }

        // Digital Prescription (Rx) Import Handlers
        function showImportRxModal() {
            new bootstrap.Modal(document.getElementById('importRxModal')).show();
        }

        async function lookupAndLoadRx(rxCode) {
            if (!rxCode || !rxCode.trim()) return;
            const statusBox = document.getElementById('rxLookupStatusBox');
            statusBox.innerHTML = '<div class="text-muted small"><i class="fas fa-spinner fa-spin me-1"></i> Searching prescription in queue...</div>';

            try {
                const res = await fetch(`/api/prescriptions/lookup?code=${encodeURIComponent(rxCode.trim())}`);
                const data = await res.json();

                if (!data.success || !data.prescription) {
                    statusBox.innerHTML = `<div class="alert alert-danger py-2 small mb-0">${data.message || 'Prescription not found'}</div>`;
                    return;
                }

                const rx = data.prescription;
                
                // Clear existing cart and populate patient name
                cart = [];
                customerNameInput.value = `${rx.patient_name} (Rx: ${rx.prescription_number})`;
                if (customerPhoneInput) customerPhoneInput.value = rx.patient_age ? `${rx.patient_age} yrs (${rx.doctor_name})` : rx.doctor_name;

                // Add Finished Drugs to cart
                if (rx.items && rx.items.length > 0) {
                    rx.items.forEach(it => {
                        cart.push({
                            product_id: it.product_id,
                            name: `${it.product_name} [${it.dosage_instructions}]`,
                            sku: it.sku,
                            unit_price: Number(it.unit_price),
                            quantity: Number(it.quantity),
                            max_stock: 999
                        });
                    });
                }

                // Add Compounded Medicines (Racikan)
                if (rx.compounds && rx.compounds.length > 0) {
                    rx.compounds.forEach(cmp => {
                        cart.push({
                            product_id: 1, // virtual line
                            name: `[RACIKAN] ${cmp.compound_name} (${cmp.quantity_pack} pk) - ${cmp.dosage_instructions}`,
                            sku: 'RACIKAN',
                            unit_price: Number(cmp.total_price),
                            quantity: 1,
                            max_stock: 999
                        });
                    });
                }

                renderCart();
                statusBox.innerHTML = `<div class="alert alert-success py-2 small mb-0"><i class="fas fa-check-circle me-1"></i> Rx ${rx.prescription_number} loaded into cart!</div>`;
                
                setTimeout(() => {
                    const modalEl = document.getElementById('importRxModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    if (window.innerWidth < 992) {
                        switchMobilePosTab('cart');
                    }
                }, 800);

            } catch (err) {
                statusBox.innerHTML = `<div class="alert alert-danger py-2 small mb-0">Error: ${err.message}</div>`;
            }
        }

        // Check if Rx was passed in URL query param: e.g. /pos?rx=RX-20260816-0001
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const rxParam = urlParams.get('rx');
            if (rxParam) {
                lookupAndLoadRx(rxParam);
            }
        });
    </script>
</body>
</html>
