<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    <title>MediCore — Register Apotek</title>
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
                <h1 class="h5 mb-1 text-dark fw-bold">Register New Account</h1>
                <p class="text-muted small mb-0">Create your administrative or pharmacist account</p>
            </div>
            
            <div class="auth-card-body">
                <form id="registerForm" method="POST" action="/register">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="full_name" name="full_name" required
                                   placeholder="e.g. apt. Sarah Wijaya, S.Farm">
                        </div>
                        <div class="invalid-feedback" id="full_name-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Work Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required
                                   placeholder="e.g. pharmacist@apoteksehat.com">
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone / WhatsApp Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="phone" name="phone"
                                   placeholder="e.g. +62 812-3456-7890">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" required
                                   placeholder="Minimum 8 characters">
                        </div>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-shield-alt"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password_confirmation" name="password_confirmation" required
                                   placeholder="Repeat your password">
                        </div>
                        <div class="invalid-feedback" id="password_confirmation-error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Initial Role Assignment</label>
                        <select class="form-select" id="role" name="role">
                            <option value="pharmacist" selected>Licensed Pharmacist (Apoteker)</option>
                            <option value="owner">Pharmacy Owner</option>
                            <option value="cashier">Cashier / Front Desk</option>
                            <option value="warehouse">Warehouse & Inventory Officer</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Complete Registration
                        </button>
                    </div>
                    
                    <div class="text-center pt-2 border-top">
                        <p class="small text-muted mb-0">Already registered? 
                            <a href="/login" class="fw-semibold">Sign In here</a>
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
        
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            data._token = csrfToken;
            
            try {
                const response = await fetch('/register', {
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
                    showAlert('Account registered successfully! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect || '/login';
                    }, 1500);
                } else {
                    showAlert(result.message || 'Registration failed. Please check your inputs.', 'danger');
                }
            } catch (error) {
                showAlert('An error occurred during registration. Please try again.', 'danger');
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