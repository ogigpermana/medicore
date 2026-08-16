<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Print Medication Etiket — MediCore' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
        }
        .etiket-card {
            background: #fff;
            border: 2px solid #0f172a;
            border-radius: 4px;
            padding: 12px 14px;
            width: 320px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
            page-break-inside: avoid;
        }
        .etiket-white {
            border-color: #0f172a;
        }
        .etiket-blue {
            border: 2px solid #2563eb;
            background-color: #eff6ff;
        }
        .etiket-blue .etiket-header {
            color: #1e40af;
        }
        .etiket-header {
            text-align: center;
            border-bottom: 1px dashed #64748b;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .pharmacy-name {
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: -0.02em;
        }
        .pharmacy-sipa {
            font-size: 0.65rem;
            color: #475569;
            font-family: monospace;
        }
        .etiket-signa-box {
            border: 1.5px solid #0f172a;
            border-radius: 3px;
            padding: 6px;
            text-align: center;
            font-weight: 800;
            font-size: 0.9rem;
            margin: 8px 0;
            background: #fff;
        }
        .etiket-blue .etiket-signa-box {
            border-color: #2563eb;
            color: #1e3a8a;
        }
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .etiket-card {
                box-shadow: none !important;
                margin: 10px auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="container py-4">
        <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0">Print Medication Labels (Etiket Obat)</h5>
                <span class="text-muted small">Prescription: <strong class="font-mono"><?= htmlspecialchars($prescription['prescription_number']) ?></strong> • Patient: <?= htmlspecialchars($prescription['patient_name']) ?></span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm px-3" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print All Labels
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.close()">
                    Close
                </button>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-4 justify-content-center">
            <!-- Labels for Finished Medications -->
            <?php foreach ($prescription['items'] as $it): ?>
                <div class="etiket-card etiket-white">
                    <div class="etiket-header">
                        <div class="pharmacy-name">APOTEK MEDICORE FARMA</div>
                        <div class="pharmacy-sipa">SIPA: 503/446/SIPA-APT/2024 • Telp: (021) 788-9900</div>
                        <div class="pharmacy-sipa">Jl. Kesehatan Raya No. 42, Jakarta</div>
                    </div>

                    <div class="d-flex justify-content-between small text-muted font-mono" style="font-size: 0.68rem;">
                        <span>No: <strong><?= htmlspecialchars($prescription['prescription_number']) ?></strong></span>
                        <span><?= date('d/m/Y') ?></span>
                    </div>

                    <div class="my-1">
                        <div class="small text-muted" style="font-size: 0.7rem;">Pasien:</div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($prescription['patient_name']) ?></div>
                    </div>

                    <div class="my-1 pb-1 border-bottom">
                        <div class="fw-bold text-dark small"><?= htmlspecialchars($it['product_name']) ?></div>
                        <div class="text-muted font-mono" style="font-size: 0.68rem;">Qty: <?= $it['quantity'] ?> <?= htmlspecialchars($it['unit_symbol'] ?? 'unit') ?></div>
                    </div>

                    <div class="etiket-signa-box">
                        <?= htmlspecialchars($it['dosage_instructions']) ?>
                    </div>

                    <?php if (!empty($it['usage_time'])): ?>
                        <div class="text-center small text-muted font-sans" style="font-size: 0.72rem;">
                            <?= htmlspecialchars($it['usage_time']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2 pt-1 border-top d-flex justify-content-between text-muted" style="font-size: 0.65rem;">
                        <span>Dr: <?= htmlspecialchars($prescription['doctor_name']) ?></span>
                        <span class="fw-bold text-danger">OBAT DALAM</span>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Labels for Compounded Mixtures -->
            <?php foreach ($prescription['compounds'] as $cmp): ?>
                <div class="etiket-card <?= ($cmp['packaging_type'] === 'ointment') ? 'etiket-blue' : 'etiket-white' ?>">
                    <div class="etiket-header">
                        <div class="pharmacy-name">APOTEK MEDICORE FARMA</div>
                        <div class="pharmacy-sipa">SIPA: 503/446/SIPA-APT/2024 • Telp: (021) 788-9900</div>
                        <div class="pharmacy-sipa">Jl. Kesehatan Raya No. 42, Jakarta</div>
                    </div>

                    <div class="d-flex justify-content-between small text-muted font-mono" style="font-size: 0.68rem;">
                        <span>No: <strong><?= htmlspecialchars($prescription['prescription_number']) ?></strong></span>
                        <span><?= date('d/m/Y') ?></span>
                    </div>

                    <div class="my-1">
                        <div class="small text-muted" style="font-size: 0.7rem;">Pasien:</div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($prescription['patient_name']) ?></div>
                    </div>

                    <div class="my-1 pb-1 border-bottom">
                        <div class="fw-bold text-dark small"><i class="fas fa-mortar-pestle me-1"></i> <?= htmlspecialchars($cmp['compound_name']) ?></div>
                        <div class="text-muted font-mono" style="font-size: 0.68rem;">Bentuk: <?= strtoupper($cmp['packaging_type']) ?> (<?= $cmp['quantity_pack'] ?> bungkus/sachet)</div>
                    </div>

                    <div class="etiket-signa-box">
                        <?= htmlspecialchars($cmp['dosage_instructions']) ?>
                    </div>

                    <div class="mt-2 pt-1 border-top d-flex justify-content-between text-muted" style="font-size: 0.65rem;">
                        <span>Dr: <?= htmlspecialchars($prescription['doctor_name']) ?></span>
                        <span class="fw-bold <?= ($cmp['packaging_type'] === 'ointment') ? 'text-primary' : 'text-danger' ?>">
                            <?= ($cmp['packaging_type'] === 'ointment') ? 'OBAT LUAR (TIDAK DITELAN)' : 'OBAT DALAM' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
