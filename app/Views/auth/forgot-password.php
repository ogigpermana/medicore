<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    <title>MediCore — Forgot Password</title>
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
                <h1 class="h5 mb-1 text-dark fw-bold">Reset Your Password</h1>
                <p class="text-muted small mb-0">We will send secure reset instructions to your email</p>
            </div>
            
            <div class="auth-card-body">
                <form id="forgotPasswordForm" method="POST" action="/forgot-password">
                    <div class="mb-3">
                        <label for="email" class="form-label">Registered Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required
                                   placeholder="e.g. pharmacist@medicore.com">
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send Password Reset Link
                        </button>
                    </div>
                    
                    <div class="text-center pt-2 border-top">
                        <p class="small text-muted mb-0">Remember your password? 
                            <a href="/login" class="fw-semibold">Back to Sign In</a>
                        </p>
                    </div>
                </form>

                <div id="alert-container" class="mt-3"></div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            data._token = csrfToken;
            
            try {
                const response = await fetch('/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message || 'Reset link sent! Please check your inbox.', 'success');
                    if (result.redirect) {
                        setTimeout(() => window.location.href = result.redirect, 2000);
                    }
                } else {
                    showAlert(result.message || 'Unable to process reset request.', 'danger');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'danger');
            }
        });
        
        function showAlert(message, type) {
            const container = document.getElementById('alert-container');
            container.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show small mb-0" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>