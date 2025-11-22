<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../includes/session.php';
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    <h1 style="text-align: center; color: var(--purple);">Checkout</h1>
    
    <div id="order-summary">
        <!-- Dynamic cart summary -->
        <p style="text-align: center; color: var(--text-light);">Loading order summary...</p>
    </div>

    <form id="checkout-form" style="margin-top: 2rem; background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-light);">
        <h2>Billing & Shipping</h2>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="full-name" value="<?php echo htmlspecialchars(getUser()['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars(getUser()['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Shipping Address *</label>
            <textarea id="shipping-address" rows="3" placeholder="Enter full address" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--primary-pink); border-radius: 4px;"></textarea>
        </div>
        <div class="form-group">
            <label>Payment Method</label>
            <select id="payment-method" required>
                <option value="card">Credit Card (Mock)</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <div class="form-group">
            <label>Card Number (Mock)</label>
            <input type="text" id="card-number" placeholder="1234 5678 9012 3456" required>
        </div>
        <button type="submit" class="btn" style="width: 100%; margin-top: 1rem;">Place Order</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            shipping_address: document.getElementById('shipping-address').value,
            payment_method: document.getElementById('payment-method').value
        };

        fetch('<?php echo SITE_URL; ?>api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Order placed! Check your email for confirmation.');
                window.location.href = '?page=orders';  // Or thank-you page
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Checkout failed: ' + err));
    });

    function loadOrderSummary() {
        fetch('<?php echo SITE_URL; ?>api/cart.php?action=list')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('order-summary');
                if (!data.success || data.data.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: red;">Cart empty. <a href="?page=cart">Back to Cart</a></p>';
                    return;
                }
                let html = '<div style="background: var(--white); padding: 1rem; border-radius: var(--border-radius); box-shadow: var(--shadow-light);">';
                data.data.forEach(item => {
                    html += `<p>${item.name} x ${item.quantity} - $${item.subtotal.toFixed(2)}</p>`;
                });
                html += `<h3>Total: $${data.total.toFixed(2)}</h3></div>`;
                container.innerHTML = html;
            });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>