<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found — MediCore ERP</title>
    
    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS & Custom MediCore Design System -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/medicore.css">
</head>
<body class="bg-page d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="card-modern p-4 p-md-5 text-center shadow" style="max-width: 500px; width: 100%;">
        <div class="icon-box-solid icon-box-amber mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.75rem;">
            <i class="fas fa-search-location"></i>
        </div>

        <span class="badge-tag badge-tag-dark font-mono mb-2">HTTP ERROR 404</span>
        <h1 class="h3 fw-bold text-dark mb-2">Page Not Found</h1>
        <p class="text-muted small mb-4">
            The requested clinical or operational resource could not be located on this pharmacy server. It may have been moved or removed.
        </p>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
            <a href="/dashboard" class="btn btn-primary btn-sm px-4 fw-bold">
                <i class="fas fa-th-large me-1"></i> Return to Dashboard
            </a>
            <button onclick="window.history.back()" class="btn btn-outline-dark btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Go Back
            </button>
        </div>

        <div class="text-muted font-mono mt-4 pt-3 border-top" style="font-size: 0.7rem;">
            MediCore Pharmacy ERP • System Protected
        </div>
    </div>

</body>
</html>
