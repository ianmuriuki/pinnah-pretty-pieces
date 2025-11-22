<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
if (!ENABLE_GUEST_CART && !isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    <h1 style="text-align: center; color: var(--purple);">Shopping Cart</h1>
    
    <div id="cart-container">
        <!-- Dynamic content loaded via JS -->
        <p style="text-align: center; color: var(--text-light);">Loading cart...</p>
    </div>

    <div id="cart-total" style="text-align: center; margin-top: 2rem; display: none;">
        <h3 style="color: var(--purple);">Total: $<span id="total-amount">0.00</span></h3>
        <?php if (isLoggedIn()): ?>
            <a href="?page=checkout" class="btn" style="display: inline-block; margin-top: 1rem;">Proceed to Checkout</a>
        <?php else: ?>
            <a href="?page=login" class="btn" style="display: inline-block; margin-top: 1rem;">Login to Checkout</a>
        <?php endif; ?>
    </div>

    <?php if (empty($_SESSION['cart']) && !isLoggedIn()): ?>
        <div style="text-align: center; padding: 2rem;">
            <p>Your cart is empty. <a href="?page=collections">Continue Shopping</a></p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCart();

    function loadCart() {
        fetch('<?php echo SITE_URL; ?>api/cart.php?action=list')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('cart-container');
                const totalEl = document.getElementById('total-amount');
                const totalDiv = document.getElementById('cart-total');

                if (!data.success || data.data.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: var(--text-light);">Your cart is empty. <a href="?page=collections">Continue Shopping</a></p>';
                    totalDiv.style.display = 'none';
                    return;
                }

                let html = '<div class="product-grid" style="grid-template-columns: 1fr; @media (min-width: 768px) { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }">';
                let total = 0;
                data.data.forEach(item => {
                    total += item.subtotal;
                    html += `
                        <div class="product-card" style="display: flex; flex-direction: column; align-items: center; padding: 1rem;">
                            <img src="${item.image}" alt="${item.name}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                            <h3>${item.name}</h3>
                            <p>$${item.price} x ${item.quantity} = $${item.subtotal.toFixed(2)}</p>
                            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                <input type="number" value="${item.quantity}" min="1" style="width: 60px; padding: 0.5rem;" onchange="updateQuantity(${item.id}, this.value)">
                                <button onclick="removeFromCart(${item.id})" class="btn" style="padding: 0.5rem 1rem;">Remove</button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
                totalEl.textContent = total.toFixed(2);
                totalDiv.style.display = 'block';
            })
            .catch(err => {
                document.getElementById('cart-container').innerHTML = '<p style="text-align: center; color: red;">Error loading cart. <button onclick="loadCart()">Retry</button></p>';
                console.error(err);
            });
    }

    window.updateQuantity = function(id, qty) {
        fetch('<?php echo SITE_URL; ?>api/cart.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: id, quantity: parseInt(qty) })
        }).then(res => res.json()).then(data => {
            if (data.success) loadCart();
        });
    };

    window.removeFromCart = function(id) {
        fetch('<?php echo SITE_URL; ?>api/cart.php?action=remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: id })
        }).then(res => res.json()).then(data => {
            if (data.success) loadCart();
        });
    };
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>