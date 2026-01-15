<?php
/**
 * pages/custom-design.php - Final Sync
 */
$pageTitle = 'Custom Design';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// If guest cart is disabled, redirect to login
if (!defined('ENABLE_GUEST_CART') || (!ENABLE_GUEST_CART && !isLoggedIn())) {
    header('Location: index.php?page=login');
    exit();
}
?>

<section class="page-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <header class="text-center mb-5">
                        <h1 class="fw-bold" style="color: #4a148c;">New Custom Design Request</h1>
                        <p class="text-muted">Fill out the form below to start your personalized jewelry journey.</p>
                    </header>

                    <form id="custom-form">
                        <div class="mb-5">
                            <h3 class="h5 fw-bold border-start border-4 border-primary ps-3 mb-4">1. Jewelry Type</h3>
                            <div class="row g-3">
                                <?php 
                                $types = [
                                    'bracelet' => ['icon' => 'fa-gem', 'label' => 'Bracelet', 'sub' => 'Wrist jewelry'],
                                    'necklace' => ['icon' => 'fa-gem', 'label' => 'Necklace', 'sub' => 'Neck pieces'],
                                    'ring'     => ['icon' => 'fa-ring', 'label' => 'Ring', 'sub' => 'Finger jewelry'],
                                    'waistbead'=> ['icon' => 'fa-gem', 'label' => 'Waistbead', 'sub' => 'Waist jewelry']
                                ];
                                foreach ($types as $key => $info): ?>
                                <div class="col-6 col-md-3">
                                    <div class="type-option p-3 text-center border rounded-3 cursor-pointer <?php echo ($key === 'bracelet') ? 'selected border-primary bg-light' : ''; ?>" 
                                         data-type="<?php echo $key; ?>" 
                                         style="transition: all 0.3s ease; cursor: pointer;">
                                        <i class="fas <?php echo $info['icon']; ?> fa-2x mb-2 text-primary"></i>
                                        <div class="fw-bold d-block"><?php echo $info['label']; ?></div>
                                        <small class="text-muted"><?php echo $info['sub']; ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="jewelry-type" name="jewelry_type" value="bracelet">
                        </div>

                        <?php if (!isLoggedIn()): ?>
                        <div class="mb-5">
                            <h3 class="h5 fw-bold border-start border-4 border-primary ps-3 mb-4">2. Your Contact Details</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" id="guest_name" class="form-control" required placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" id="guest_email" class="form-control" required placeholder="john@example.com">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mb-5">
                            <h3 class="h5 fw-bold border-start border-4 border-primary ps-3 mb-4"><?php echo isLoggedIn() ? '2.' : '3.'; ?> Design Vision</h3>
                            <div class="mb-4">
                                <label for="vision" class="form-label">Describe Your Vision *</label>
                                <textarea id="vision" name="description" class="form-control" rows="5" required 
                                          placeholder="E.g., Elegant purple beads for a wedding. Describe colors, materials, and size..."></textarea>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="budget" class="form-label">Estimated Budget (KSh)</label>
                                    <input type="number" id="budget" name="budget" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label for="occasion" class="form-label">Occasion/Style</label>
                                    <input type="text" id="occasion" name="occasion" class="form-control" placeholder="E.g., Casual, Wedding...">
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm w-100 w-md-auto">
                                <i class="fas fa-paper-plane me-2"></i> Submit Design Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.type-option.selected { border: 2px solid #4a148c !important; background-color: #f3e5f5 !important; }
.cursor-pointer { cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Jewelry Type Selection
    const types = document.querySelectorAll('.type-option');
    types.forEach(option => {
        option.addEventListener('click', () => {
            types.forEach(o => o.classList.remove('selected', 'border-primary', 'bg-light'));
            option.classList.add('selected', 'border-primary', 'bg-light');
            document.getElementById('jewelry-type').value = option.dataset.type;
        });
    });

    // 2. Form Submission
    document.getElementById('custom-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';

        // Prepare Data (Mapping fields correctly for the API)
        const formData = {
            full_name: document.getElementById('guest_name')?.value || '<?php echo $_SESSION['user_name'] ?? ""; ?>',
            email: document.getElementById('guest_email')?.value || '<?php echo $_SESSION['user_email'] ?? ""; ?>',
            jewelry_type: document.getElementById('jewelry-type').value,
            vision: document.getElementById('vision').value,
            budget: parseFloat(document.getElementById('budget').value) || 0,
            occasion: document.getElementById('occasion').value
        };

        fetch('api/custom-requests.php?action=submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Request Sent!',
                    text: data.message + ' Would you like to chat on WhatsApp for faster service?',
                    showCancelButton: true,
                    confirmButtonText: 'Chat on WhatsApp',
                    cancelButtonText: 'Maybe Later',
                    confirmButtonColor: '#25D366'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const msg = encodeURIComponent(`Hi Pinnah, I just submitted a custom request for a ${formData.jewelry_type}: ${formData.vision}`);
                        window.open(`https://wa.me/<?php echo OWNER_PHONE; ?>?text=${msg}`, '_blank');
                    }
                    window.location.href = 'index.php';
                });
            } else {
                Swal.fire('Error', data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Request';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire('Error', 'Connection failed. Please check if the API path is correct.', 'error');
            btn.disabled = false;
        });
    });
});
</script>