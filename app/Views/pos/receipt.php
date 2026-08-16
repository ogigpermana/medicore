<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Receipt — MediCore' ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .receipt-container { width: 100% !important; max-width: 80mm !important; box-shadow: none !important; margin: 0 auto !important; }
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Courier New', Courier, monospace;
            color: #0f172a;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-container {
            background-color: #ffffff;
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 16px;
            border: 1px dashed #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #94a3b8; }
        .border-bottom { border-bottom: 1px dashed #94a3b8; }
        .my-2 { margin-top: 8px; margin-bottom: 8px; }
        .py-1 { padding-top: 4px; padding-bottom: 4px; }
        .d-flex { display: flex; justify-content: space-between; }
        .small { font-size: 10px; color: #475569; }
        .btn-print {
            background: #0d9488;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="btn-print" onclick="window.print()">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; border: 1px solid #cbd5e1; background: white; border-radius: 6px; cursor: pointer;">
            Close Window
        </button>
    </div>

    <div class="receipt-container">
        <div class="text-center">
            <div class="fw-bold" style="font-size: 15px;">MEDICORE APOTEK</div>
            <div class="small">Sistem Informasi Apoteker Modern</div>
            <div class="small">Jl. Kesehatan Raya No. 10, Jakarta</div>
            <div class="small">Telp: (021) 555-0199 • SIPA: 446/2026</div>
        </div>

        <div class="border-top my-2"></div>

        <div class="d-flex small">
            <span>No: <?= htmlspecialchars($sale['invoice_number']) ?></span>
            <span><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></span>
        </div>
        <div class="d-flex small">
            <span>Kasir: <?= htmlspecialchars($sale['cashier_name'] ?? 'Cashier') ?></span>
            <span>Shift: Regular</span>
        </div>
        <div class="small">
            <span>Pasien: <?= htmlspecialchars($sale['customer_name']) ?></span>
        </div>

        <div class="border-top border-bottom my-2 py-1">
            <?php foreach ($sale['items'] as $item): ?>
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                    <div class="d-flex small">
                        <span><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit_symbol'] ?? 'x') ?> @ Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></span>
                        <span class="fw-bold">Rp <?= number_format($item['total_price'], 0, ',', '.') ?></span>
                    </div>
                    <?php if (!empty($item['batch_number'])): ?>
                        <div class="small" style="font-size: 9px; color: #64748b;">Lot: <?= htmlspecialchars($item['batch_number']) ?> (Exp: <?= htmlspecialchars($item['expiry_date'] ?? '-') ?>)</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex">
            <span>Subtotal:</span>
            <span>Rp <?= number_format($sale['subtotal'], 0, ',', '.') ?></span>
        </div>
        <?php if ($sale['tax_amount'] > 0): ?>
            <div class="d-flex">
                <span>PPN (11%):</span>
                <span>Rp <?= number_format($sale['tax_amount'], 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>
        <?php if ($sale['discount_amount'] > 0): ?>
            <div class="d-flex">
                <span>Diskon:</span>
                <span>-Rp <?= number_format($sale['discount_amount'], 0, ',', '.') ?></span>
            </div>
        <?php endif; ?>

        <div class="border-top my-2"></div>

        <div class="d-flex fw-bold" style="font-size: 13px;">
            <span>TOTAL:</span>
            <span>Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></span>
        </div>
        <div class="d-flex small">
            <span>Bayar (<?= strtoupper($sale['payment_method']) ?>):</span>
            <span>Rp <?= number_format($sale['cash_tendered'], 0, ',', '.') ?></span>
        </div>
        <div class="d-flex small">
            <span>Kembali:</span>
            <span>Rp <?= number_format($sale['cash_change'], 0, ',', '.') ?></span>
        </div>

        <div class="border-top my-2"></div>

        <div class="text-center small">
            <div>Terima kasih atas kunjungan Anda</div>
            <div>Obat yang sudah dibeli tidak dapat ditukar</div>
            <div>Semoga Lekas Sembuh!</div>
        </div>
    </div>

</body>
</html>
