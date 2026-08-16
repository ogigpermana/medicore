<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - <?= htmlspecialchars($transfer['transfer_number']) ?></title>
    
    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 30px 15px;
        }
        .a4-page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .header-rule {
            border-bottom: 2px solid #0f172a;
            margin: 15px 0 20px 0;
        }
        .table-manifest th {
            background-color: #f8fafc !important;
            border-bottom: 2px solid #0f172a !important;
            font-size: 0.78rem;
            text-transform: uppercase;
        }
        .table-manifest td {
            font-size: 0.82rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        .signature-box {
            text-align: center;
            height: 80px;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .a4-page {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                padding: 10mm 15mm !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar (Screen Only) -->
    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-dark btn-sm px-4 fw-bold shadow-sm">
            <i class="fas fa-print me-1"></i> Print Delivery Note (A4)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3 ms-2">
            Close
        </button>
    </div>

    <!-- A4 Document Layout -->
    <div class="a4-page">
        <!-- Pharmacy Header -->
        <div class="row align-items-center">
            <div class="col-8">
                <h4 class="fw-bold text-dark mb-0 text-uppercase tracking-wide">MEDICORE PHARMACY</h4>
                <div class="text-muted small">INTERNAL PHARMACEUTICAL LOGISTICS & DISTRIBUTION</div>
                <div class="small text-secondary mt-1">
                    Central Office: Jl. Farmasi Sehat No. 1, Gambir, Jakarta Pusat • Tel: 021-5550199
                </div>
                <div class="text-muted font-mono" style="font-size: 0.72rem;">
                    SIA: 503/001/SIA/DPMPTSP/2023 • SIPA: 19880415/SIPA_32.73/2022/2001
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="border p-2 bg-light text-center rounded">
                    <div class="text-muted text-uppercase" style="font-size: 0.65rem;">DELIVERY NOTE NO:</div>
                    <div class="fw-bold font-mono text-dark fs-6"><?= htmlspecialchars($transfer['transfer_number']) ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Date: <?= date('d M Y', strtotime($transfer['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <div class="header-rule"></div>

        <!-- Document Title -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-dark text-uppercase mb-0 text-decoration-underline">
                INTER-BRANCH STOCK TRANSFER DELIVERY NOTE
            </h5>
            <div class="text-muted small">OFFICIAL PHARMACEUTICAL TRANSIT MANIFEST</div>
        </div>

        <!-- Routing Information Table -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 bg-light border rounded">
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.68rem;">SENDER (SOURCE LOCATION):</div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($transfer['source_branch_name']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($transfer['source_address']) ?></div>
                    <div class="small text-secondary font-mono mt-1" style="font-size: 0.72rem;">
                        APJ: <?= htmlspecialchars($transfer['source_apj'] ?? '-') ?> • Tel: <?= htmlspecialchars($transfer['source_phone'] ?? '-') ?>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="p-3 bg-light border rounded">
                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.68rem;">RECIPIENT (DESTINATION BRANCH):</div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($transfer['destination_branch_name']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($transfer['destination_address']) ?></div>
                    <div class="small text-secondary font-mono mt-1" style="font-size: 0.72rem;">
                        APJ: <?= htmlspecialchars($transfer['destination_apj'] ?? '-') ?> • Tel: <?= htmlspecialchars($transfer['destination_phone'] ?? '-') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courier & Vehicle Metadata -->
        <div class="row g-2 mb-4 font-mono small">
            <div class="col-4">
                <strong>Courier / Driver:</strong> <?= htmlspecialchars($transfer['driver_name'] ?? 'Internal Courier') ?>
            </div>
            <div class="col-4">
                <strong>Vehicle Reg No:</strong> <?= htmlspecialchars($transfer['vehicle_number'] ?? '-') ?>
            </div>
            <div class="col-4 text-end">
                <strong>Departure Time:</strong> <?= $transfer['departure_date'] ? date('d/m/Y H:i', strtotime($transfer['departure_date'])) : '-' ?>
            </div>
        </div>

        <!-- Medication Manifest Table -->
        <table class="table table-bordered table-manifest mb-4">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">NO</th>
                    <th>MEDICATION / PHARMACEUTICAL PRODUCT</th>
                    <th>SKU CODE</th>
                    <th>BATCH LOT</th>
                    <th class="text-center">EXPIRATION</th>
                    <th class="text-center" style="width: 85px;">QTY SENT</th>
                    <th class="text-center" style="width: 85px;">QTY REC'D</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($transfer['items'] as $it): ?>
                    <tr>
                        <td class="text-center font-mono"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($it['product_name']) ?></div>
                        </td>
                        <td class="font-mono text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($it['sku']) ?></td>
                        <td class="font-mono"><?= htmlspecialchars($it['batch_number'] ?? '-') ?></td>
                        <td class="text-center font-mono" style="font-size: 0.75rem;">
                            <?= $it['expiry_date'] ? date('d/m/Y', strtotime($it['expiry_date'])) : '-' ?>
                        </td>
                        <td class="text-center fw-bold font-mono"><?= $it['qty_sent'] ?></td>
                        <td class="text-center fw-bold font-mono"><?= $it['qty_received'] > 0 ? $it['qty_received'] : '' ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($it['notes'] ?? 'Good condition & sealed') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="bg-light fw-bold font-mono">
                    <td colspan="5" class="text-end">TOTAL MEDICATION UNITS:</td>
                    <td class="text-center"><?= $transfer['total_qty_sent'] ?></td>
                    <td class="text-center"><?= $transfer['total_qty_received'] > 0 ? $transfer['total_qty_received'] : '' ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <?php if ($transfer['shipping_notes']): ?>
            <div class="p-2 border rounded bg-light small mb-4">
                <strong>Shipping Instructions / Remarks:</strong> <?= htmlspecialchars($transfer['shipping_notes']) ?>
            </div>
        <?php endif; ?>

        <!-- Triple Signature Layout -->
        <div class="row justify-content-between text-center mt-5 pt-3">
            <div class="col-4">
                <div class="small text-muted mb-1">Dispatched by (Warehouse / Source):</div>
                <div class="signature-box"></div>
                <div class="fw-bold small text-dark mt-2">( <?= htmlspecialchars($transfer['requester_name']) ?> )</div>
                <div class="text-muted" style="font-size: 0.7rem;">Pharmacy Logistics Officer</div>
            </div>

            <div class="col-4">
                <div class="small text-muted mb-1">Delivered by (Courier / Driver):</div>
                <div class="signature-box"></div>
                <div class="fw-bold small text-dark mt-2">( <?= htmlspecialchars($transfer['driver_name'] ?? 'Internal Courier') ?> )</div>
                <div class="text-muted" style="font-size: 0.7rem;">Plate: <?= htmlspecialchars($transfer['vehicle_number'] ?? '-') ?></div>
            </div>

            <div class="col-4">
                <div class="small text-muted mb-1">Received by (Destination Branch):</div>
                <div class="signature-box"></div>
                <div class="fw-bold small text-dark mt-2">( <?= htmlspecialchars($transfer['receiver_name'] ?? '........................') ?> )</div>
                <div class="text-muted" style="font-size: 0.7rem;">Pharmacist / Branch Staff</div>
            </div>
        </div>
    </div>

</body>
</html>
