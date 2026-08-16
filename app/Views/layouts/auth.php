<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    <title>MediCore — <?= $title ?? 'Pharmacy System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page">
    <div class="auth-page-wrapper">
        <div class="auth-card-clean">
            <div class="auth-card-header">
                <a href="/" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-2">
                    <div class="icon-box-solid icon-box-teal" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <span class="fs-4 fw-bold text-dark" style="letter-spacing: -0.03em;">MediCore</span>
                </a>
                <p class="text-muted small mb-0">Pharmacy Management System</p>
            </div>
            <div class="auth-card-body">
                <?= $content ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>