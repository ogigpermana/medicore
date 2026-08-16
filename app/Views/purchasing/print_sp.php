<?php
    $spTitle = match($po['sp_type']) {
        'precursor' => 'SURAT PESANAN OBAT MENGANDUNG PREKURSOR FARMASI',
        'oot' => 'SURAT PESANAN OBAT-OBAT TERTENTU (OOT)',
        'narcotic_psychotropic' => 'SURAT PESANAN NARKOTIKA / PSIKOTROPIKA',
        default => 'SURAT PESANAN OBAT REGULER'
    };

    $spRegulation = match($po['sp_type']) {
        'precursor' => 'Berdasarkan Peraturan Badan POM No. 10 Tahun 2019 tentang Pedoman Pengelolaan Obat-Obat Tertentu dan Prekursor Farmasi',
        'oot' => 'Berdasarkan Peraturan Badan POM No. 10 Tahun 2019 tentang Kriteria dan Tata Laksana Penyaluran Obat-Obat Tertentu (OOT)',
        'narcotic_psychotropic' => 'Berdasarkan UU No. 35 Tahun 2009 tentang Narkotika dan Permenkes No. 3 Tahun 2015',
        default => 'Sesuai dengan Peraturan Menteri Kesehatan RI tentang Standar Pelayanan Kefarmasian di Apotek'
    };
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pesanan - <?= htmlspecialchars($po['po_number']) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            padding: 2rem 1rem;
        }

        .sp-paper {
            background: #ffffff;
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 3.5rem;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            border-radius: 4px;
        }

        .pharmacy-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .sp-title-box {
            text-align: center;
            margin-bottom: 2rem;
        }

        .sp-table th {
            background-color: #f8fafc;
            border: 1px solid #94a3b8;
            font-size: 0.8rem;
            text-align: center;
            padding: 6px;
        }

        .sp-table td {
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        .signature-box {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }

        .sign-col {
            text-align: center;
            width: 250px;
        }

        .sign-space {
            height: 70px;
        }

        .no-print {
            max-width: 800px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .sp-paper {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="/purchasing/<?= $po['id'] ?>" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to PO Details
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
            <i class="fas fa-print me-1"></i> Print Surat Pesanan (A4)
        </button>
    </div>

    <div class="sp-paper">
        <!-- Pharmacy Header (Kop Apotek) -->
        <div class="pharmacy-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-0 text-uppercase tracking-wide">APOTEK MEDICORE FARMA</h3>
                <div class="small fw-semibold text-secondary">SIA: 503/0124/SIA-DPMPTSP/2024 • SIPA: 19880415/SIPA_32.73/2022/2001</div>
                <div class="small text-muted">Jl. Kesehatan Farmasi No. 45, Bandung, Jawa Barat 40132 • Telp: (022) 7234-8899</div>
            </div>
            <div class="text-end">
                <div class="badge bg-dark text-uppercase px-2.5 py-1.5 font-mono" style="font-size: 0.75rem;">
                    <?= htmlspecialchars($po['sp_type']) ?>
                </div>
            </div>
        </div>

        <!-- SP Title -->
        <div class="sp-title-box">
            <h5 class="fw-bold mb-1 text-decoration-underline"><?= $spTitle ?></h5>
            <div class="font-mono small fw-bold">NOMOR: <?= htmlspecialchars($po['po_number']) ?></div>
            <div class="text-muted" style="font-size: 0.72rem; margin-top: 4px;"><?= $spRegulation ?></div>
        </div>

        <!-- Statement Body -->
        <div class="small mb-3" style="line-height: 1.6;">
            Yang bertanda tangan di bawah ini:<br>
            <table class="table table-borderless table-sm mb-2" style="font-size: 0.85rem;">
                <tr>
                    <td style="width: 170px;" class="text-muted">Nama Apoteker</td>
                    <td style="width: 10px;">:</td>
                    <td><strong>apt. MediCore Head Pharmacist, S.Farm.</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Nomor SIPA</td>
                    <td>:</td>
                    <td><strong class="font-mono"><?= htmlspecialchars($po['pharmacist_sipa'] ?? 'SIPA: 19880415/SIPA_32.73/2022/2001') ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Jabatan</td>
                    <td>:</td>
                    <td>Apoteker Penanggung Jawab (APJ)</td>
                </tr>
                <tr>
                    <td class="text-muted">Nama Sarana</td>
                    <td>:</td>
                    <td><strong>Apotek MediCore Farma Central Branch</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Alamat Apotek</td>
                    <td>:</td>
                    <td>Jl. Kesehatan Farmasi No. 45, Bandung, Jawa Barat</td>
                </tr>
            </table>

            Mengajukan pesanan obat kepada Pedagang Besar Farmasi (PBF):<br>
            <table class="table table-borderless table-sm mb-3" style="font-size: 0.85rem;">
                <tr>
                    <td style="width: 170px;" class="text-muted">Nama PBF Distributor</td>
                    <td style="width: 10px;">:</td>
                    <td><strong><?= htmlspecialchars($po['supplier_name']) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Alamat PBF</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($po['supplier_address'] ?? 'Kota Bandung, Jawa Barat') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Telepon / Kontak</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($po['supplier_phone'] ?? '-') ?></td>
                </tr>
            </table>
            Dengan rincian permohonan obat sebagai berikut:
        </div>

        <!-- Ordered Items Table -->
        <table class="table sp-table mb-4">
            <thead>
                <tr>
                    <th style="width: 35px;">NO</th>
                    <th>NAMA OBAT / BENTUK SEDIAAN / ZAT AKTIF</th>
                    <th style="width: 110px;">SATUAN</th>
                    <th style="width: 90px;">JUMLAH</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($po['items'] as $it): ?>
                    <tr>
                        <td class="text-center font-mono"><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($it['product_name']) ?></strong>
                            <div class="text-muted font-mono" style="font-size: 0.72rem;">SKU: <?= htmlspecialchars($it['sku']) ?></div>
                        </td>
                        <td class="text-center font-mono"><?= htmlspecialchars($it['unit_symbol'] ?? 'Box/Strip') ?></td>
                        <td class="text-center font-mono"><strong><?= $it['quantity_ordered'] ?></strong></td>
                        <td class="small text-muted"><?= htmlspecialchars($po['notes'] ?? 'Untuk kebutuhan stok pelayanan apotek') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Signatures Box -->
        <div class="signature-box">
            <div class="sign-col">
                <div class="small text-muted mb-1">Diterima oleh PBF,</div>
                <div class="sign-space"></div>
                <div class="fw-bold small">( ............................................ )</div>
                <div class="small text-muted">Sales / Petugas PBF</div>
            </div>

            <div class="sign-col">
                <div class="small text-muted mb-1">Bandung, <?= date('d F Y', strtotime($po['order_date'])) ?></div>
                <div class="small text-dark fw-bold">Apoteker Penanggung Jawab,</div>
                <div class="sign-space d-flex align-items-center justify-content-center">
                    <span class="badge border border-teal text-teal font-mono small py-1 px-2" style="font-size: 0.65rem;">
                        <i class="fas fa-stamp me-1"></i> SIPA VALIDATED
                    </span>
                </div>
                <div class="fw-bold small text-decoration-underline">apt. MediCore Head Pharmacist, S.Farm.</div>
                <div class="small font-mono text-muted"><?= htmlspecialchars($po['pharmacist_sipa'] ?? 'SIPA Active') ?></div>
            </div>
        </div>
    </div>

</body>
</html>
