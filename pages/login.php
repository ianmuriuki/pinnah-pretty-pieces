<?php
/**
 * login.php - Luxury UI with AJAX Authentication
 */

// 1. SECURITY: Prevent direct access
if (!defined('SITE_NAME')) exit('Direct access not permitted');

// 2. REDIRECT: If already logged in, skip this page
if (isLoggedIn()) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>

<section class="auth-section py-5" style="background: #fdfbff;">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100">
            
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-image-wrapper position-relative overflow-hidden rounded-4 shadow-lg" style="height: 600px;">
                    <img src="assets/images/products/slide1.jpg" alt="Pinnah's Jewelry" class="auth-image w-100 h-100 object-fit-cover">
                    <div class="auth-image-overlay d-flex align-items-center justify-content-center text-center p-5" 
                         style="background: linear-gradient(rgba(106, 27, 154, 0.3), rgba(49, 14, 85, 0.7)); position: absolute; inset: 0;">
                        <div class="auth-brand text-white">
                            <h2 class="auth-brand-title display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif;">Welcome Back</h2>
                            <p class="auth-brand-subtitle lead opacity-90">Continue your journey with Pinnah's Pretty Pieces</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-8 offset-lg-1">
                <div class="auth-card fade-in-up p-4 p-md-5 shadow-lg bg-white rounded-4 border-0">
                    <div class="auth-header text-center mb-5">
                        <h1 class="auth-title h2 fw-bold" style="color: #4a148c; letter-spacing: -0.5px;">Login</h1>
                        <p class="auth-subtitle text-muted">Sign in to your account to continue shopping</p>
                    </div>

                    <form id="login-form" novalidate>
                        <div class="form-group mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Email Address</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-envelope position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="email" id="email" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="your@email.com" required style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Password</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-lock position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="password" id="password" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="Enter your password" required style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label small text-muted" for="remember">Remember me</label>
                            </div>
                            <a href="index.php?page=forgot-password" class="small fw-bold text-decoration-none" style="color: #7b1fa2;">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg py-3 shadow border-0 transition-all mb-4" 
                                style="background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%); border-radius: 12px; font-weight: 700;">
                            <i class="fas fa-sign-in-alt me-2"></i> SIGN IN
                        </button>

                        <div class="auth-footer text-center">
                            <p class="text-muted small">Don't have an account? 
                                <a href="index.php?page=signup" class="fw-bold text-decoration-none" style="color: #7b1fa2;">Create one here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        // Loading State
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> SIGNING IN...';

        const formData = {
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
        };

        // Call our API endpoint
        fetch('api/auth.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Success: Redirect to Home or Profile
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome Back!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                // Error from API
                throw new Error(data.message);
            }
        })
        .catch(err => {
            // Display Error via SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: err.message || 'Invalid email or password',
                confirmButtonColor: '#6a1b9a'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});
</script>