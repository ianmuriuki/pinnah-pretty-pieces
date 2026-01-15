<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login&redirect=checkout');
    exit();
}

// Get user data for pre-filling the form
$user = getUser();
?>

<section class="checkout-section py-5 bg-light">
    <div class="container">
        <h1 class="text-center mb-5 fw-bold" style="color: #4a148c;">Complete Your Order</h1>
        
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <form id="checkout-form">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">Shipping Information</h4>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name</label>
                                <input type="text" id="full-name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" id="phone" class="form-control" required placeholder="0712 345 678">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Shipping Address *</label>
                                <textarea id="shipping-address" rows="3" class="form-control" placeholder="Enter street, apartment, and town" required></textarea>
                            </div>
                        </div>

                        <h4 class="fw-bold mt-5 mb-4 border-bottom pb-2">Payment Method</h4>
                        <div class="mb-4">
                            <div class="form-check mb-3 p-3 border rounded-3">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay-mpesa" value="mpesa" checked>
                                <label class="form-check-label fw-bold" for="pay-mpesa">
                                    M-PESA / Mobile Money
                                </label>
                            </div>
                            <div class="form-check mb-3 p-3 border rounded-3">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay-card" value="card">
                                <label class="form-check-label fw-bold" for="pay-card">
                                    Credit/Debit Card (Mock)
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="place-order-btn" class="btn btn-primary btn-lg w-100 rounded-pill py-3 mt-3">
                            PLACE ORDER NOW
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-4">Your Selection</h4>
                    <div id="order-summary-list">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span id="summary-subtotal">KSh 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Delivery</span>
                            <span class="text-success fw-bold">Calculated on confirmation</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <span class="h5 fw-bold">Total</span>
                            <span class="h5 fw-bold text-primary">KSh <span id="summary-total">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutSummary();

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('place-order-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing Order...';

        const formData = {
            full_name: document.getElementById('full-name').value,
            phone: document.getElementById('phone').value,
            shipping_address: document.getElementById('shipping-address').value,
            payment_method: document.querySelector('input[name="payment_method"]:checked').value
        };

        fetch('api/orders.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed!',
                    text: 'Thank you! Your order #' + data.order_id + ' has been received.',
                    confirmButtonText: 'View My Orders'
                }).then(() => {
                    window.location.href = 'index.php?page=orders';
                });
            } else {
                Swal.fire('Error', data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = 'PLACE ORDER NOW';
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Checkout Failed', 'Connection to server lost. Please try again.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'PLACE ORDER NOW';
        });
    });

    function loadCheckoutSummary() {
        fetch('api/cart.php?action=list')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('order-summary-list');
                if (!data.success || data.data.length === 0) {
                    window.location.href = 'index.php?page=cart';
                    return;
                }

                let html = '<div class="checkout-items">';
                data.data.forEach(item => {
                    html += `
                        <div class="d-flex align-items-center mb-3">
                            <img src="${item.image}" class="rounded-2 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="small fw-bold">${item.name}</div>
                                <div class="small text-muted">${item.quantity} x KSh ${item.price.toLocaleString()}</div>
                            </div>
                            <div class="fw-bold small">KSh ${item.subtotal.toLocaleString()}</div>
                        </div>`;
                });
                html += '</div>';
                
                container.innerHTML = html;
                document.getElementById('summary-subtotal').textContent = 'KSh ' + data.total.toLocaleString();
                document.getElementById('summary-total').textContent = data.total.toLocaleString();
            });
    }
});
</script>