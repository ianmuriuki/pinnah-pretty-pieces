<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php

if (!defined('SITE_NAME')) {
    exit('Direct access not permitted');
}

// 2. REDIRECT: If already logged in, the user shouldn't be here
if (isLoggedIn()) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>

<section class="auth-section py-5" style="background: #fdfbff;">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100">
            
            <div class="col-lg-6 d-none d-lg-block">
                <div class="auth-image-wrapper position-relative overflow-hidden rounded-4 shadow-lg" style="height: 650px;">
                    <img src="assets/images/products/slide2.jpg" alt="Pinnah's Jewelry" class="auth-image w-100 h-100 object-fit-cover">
                    <div class="auth-image-overlay d-flex align-items-center justify-content-center text-center p-5" 
                         style="background: linear-gradient(rgba(106, 27, 154, 0.45), rgba(49, 14, 85, 0.7)); position: absolute; inset: 0;">
                        <div class="auth-brand text-white">
                            <h2 class="auth-brand-title display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif; letter-spacing: 1px;">Join the Family</h2>
                            <p class="auth-brand-subtitle lead opacity-90">Unlock exclusive access to handcrafted treasures and personalized designs.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-8 offset-lg-1">
                <div class="auth-card fade-in-up p-4 p-md-5 shadow-lg bg-white rounded-4 border-0">
                    <div class="auth-header text-center mb-5">
                        <h1 class="auth-title h2 fw-bold" style="color: #4a148c; letter-spacing: -0.5px;">Create Account</h1>
                        <p class="auth-subtitle text-muted">Begin your journey with Pinnah's Pretty Pieces</p>
                    </div>

                    <form id="register-form" novalidate>
                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: #666;">Full Name</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-user position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="full-name" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="John Doe" required style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: #666;">Email Address</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-envelope position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="email" id="email" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="john@example.com" required style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: #666;">Password</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-lock position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="password" id="password" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="••••••••" minlength="6" required style="border: 1.5px solid #eee;">
                            </div>
                            <div class="progress mt-2 d-none" id="pass-strength-bar" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: #666;">Phone Number</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-phone position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="tel" id="phone" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="+254 700 000 000" style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg py-3 shadow border-0 transition-all" 
                                style="background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%); border-radius: 12px; font-weight: 700; letter-spacing: 0.5px;">
                            <i class="fas fa-user-plus me-2"></i> CREATE ACCOUNT
                        </button>

                        <div class="auth-footer text-center mt-4">
                            <p class="text-muted small">Already a member? 
                                <a href="index.php?page=login" class="fw-bold text-decoration-none" style="color: #7b1fa2;">Sign in here</a>
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
    const regForm = document.getElementById('register-form');
    
    // Simple Password Strength Indicator
    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function() {
        const bar = document.getElementById('pass-strength-bar');
        bar.classList.remove('d-none');
        const strength = Math.min((this.value.length / 8) * 100, 100);
        bar.firstElementChild.style.width = strength + '%';
        bar.firstElementChild.className = strength < 50 ? 'progress-bar bg-danger' : 'progress-bar bg-success';
    });

    regForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable & Loading State
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> CREATING ACCOUNT...';

        const formData = {
            full_name: document.getElementById('full-name').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: passwordInput.value,
            phone: document.getElementById('phone').value.trim()
        };

        // API Call
        fetch('api/auth.php?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Server error');
            return data;
        })
        .then(data => {
            if (data.success) {
                // Successful Registration
                Swal.fire({
                    icon: 'success',
                    title: 'Welcome!',
                    text: data.message,
                    confirmButtonColor: '#6a1b9a'
                }).then(() => {
                    window.location.href = 'index.php?page=login';
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(err => {
            console.error('Fetch Error:', err);
            // Error Handling UI
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: err.message,
                confirmButtonColor: '#6a1b9a'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>