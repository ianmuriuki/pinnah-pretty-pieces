<?php
/**
 * profile.php - Luxury User Dashboard
 */

// SECURITY: Prevent direct access
if (!defined('SITE_NAME')) exit('Direct access not permitted');

// Ensure session is active and user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit();
}

try {
    $db = Database::getInstance();
    // Fetch user data based on current session
    $stmt = $db->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header('Location: index.php?page=login');
        exit();
    }
} catch (Exception $e) {
    error_log("Profile Fetch Error: " . $e->getMessage());
    die("<div class='container mt-5 pt-5 text-center'><h3>Service temporarily unavailable. Please try again later.</h3></div>");
}
?>

<section class="auth-section py-5" style="background: #fdfbff; min-height: 100vh;">
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="auth-card p-4 text-center border-0 shadow-sm rounded-4 bg-white">
                    <div class="profile-avatar mb-3 position-relative d-inline-block">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px; background: var(--purple-gradient, linear-gradient(135deg, #6a1b9a, #4a148c));">
                            <span class="h1 text-white mb-0"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #4a148c;"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                    <hr class="opacity-25">
                    <div class="list-group list-group-flush text-start rounded-3 overflow-hidden">
                        <a href="?page=profile" class="list-group-item list-group-item-action border-0 py-3 active" 
                           style="background: #f3e5f5; color: #6a1b9a; font-weight: 600;">
                            <i class="fas fa-user-edit me-2"></i> Account Details
                        </a>
                        <a href="?page=orders" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-shopping-bag me-2 text-muted"></i> My Orders
                        </a>
                        <a href="index.php?logout=1" class="list-group-item list-group-item-action border-0 py-3 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="auth-card p-4 p-md-5 shadow-sm border-0 rounded-4 bg-white">
                    <h2 class="fw-bold mb-4" style="color: #4a148c;">Profile Settings</h2>
                    
                    <form id="update-profile-form" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase text-muted">Full Name</label>
                                <div class="input-wrapper position-relative">
                                    <i class="fas fa-user position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" id="upd-name" class="form-control ps-5 py-3 rounded-3" 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required style="border: 1.5px solid #eee;">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label small fw-bold text-uppercase text-muted">Phone Number</label>
                                <div class="input-wrapper position-relative">
                                    <i class="fas fa-phone position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                    <input type="tel" id="upd-phone" class="form-control ps-5 py-3 rounded-3" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>" style="border: 1.5px solid #eee;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Email Address</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-envelope position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="email" class="form-control ps-5 py-3 rounded-3 bg-light" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="border: 1.5px solid #eee;">
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size: 0.8rem;">
                                <i class="fas fa-info-circle me-1"></i> Email cannot be changed for security reasons.
                            </p>
                        </div>

                        <hr class="my-5 opacity-25">
                        <h5 class="mb-4 fw-bold" style="color: #6a1b9a;">Security Update</h5>
                        
                        <div class="mb-5">
                            <label class="form-label small fw-bold text-uppercase text-muted">New Password</label>
                            <div class="input-wrapper position-relative">
                                <i class="fas fa-lock position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                <input type="password" id="upd-pass" class="form-control ps-5 py-3 rounded-3" 
                                       placeholder="Leave blank to keep current password" style="border: 1.5px solid #eee;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow border-0" 
                                style="background: linear-gradient(135deg, #6a1b9a, #4a148c); border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.getElementById('update-profile-form');

    updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

        const data = {
            full_name: document.getElementById('upd-name').value.trim(),
            phone: document.getElementById('upd-phone').value.trim(),
            password: document.getElementById('upd-pass').value
        };

        fetch('api/auth.php?action=update_profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                // Using SweetAlert2 for the success message
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated',
                    text: res.message,
                    confirmButtonColor: '#6a1b9a'
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(res.message);
            }
        })
        .catch((err) => {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: err.message || 'Error updating profile',
                confirmButtonColor: '#6a1b9a'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});
</script>