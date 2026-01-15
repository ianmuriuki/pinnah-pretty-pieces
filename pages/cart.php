<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
?>

<section class="cart-section py-5">
    <div class="container">
        <h1 class="text-center mb-5 fw-bold" style="color: #4a148c;">Your Jewelry Box</h1>
        
        <div class="row">
            <div class="col-lg-8">
                <div id="cart-container" class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <p class="text-center text-muted">Loading your treasures...</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div id="cart-summary" class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; display: none;">
                    <h4 class="fw-bold mb-4">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold">KSh <span id="total-amount">0.00</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Shipping</span>
                        <span class="text-success small">Calculated at checkout</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Total</span>
                        <span class="h5 fw-bold text-primary">KSh <span id="final-amount">0.00</span></span>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <a href="index.php?page=checkout" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm py-3">
                            PROCEED TO CHECKOUT
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-outline-primary btn-lg w-100 rounded-pill py-3">
                            LOGIN TO CHECKOUT
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php?page=collections" class="btn btn-link w-100 mt-3 text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCart();

    function loadCart() {
        fetch('api/cart.php?action=list')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('cart-container');
                const summary = document.getElementById('cart-summary');

                if (!data.success || data.data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h4>Your cart is empty</h4>
                            <p class="text-muted">Looks like you haven't picked any jewelry yet.</p>
                            <a href="index.php?page=collections" class="btn btn-primary rounded-pill px-4 mt-3">Start Shopping</a>
                        </div>`;
                    summary.style.display = 'none';
                    updateBadge(0);
                    return;
                }

                let html = '<div class="table-responsive"><table class="table align-middle border-0">';
                data.data.forEach(item => {
                    html += `
                        <tr class="border-bottom">
                            <td style="width: 100px;">
                                <img src="${item.image}" class="rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                            </td>
                            <td>
                                <h6 class="fw-bold mb-0">${item.name}</h6>
                                <small class="text-muted">KSh ${parseFloat(item.price).toLocaleString()}</small>
                            </td>
                            <td style="width: 150px;">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="updateQty(${item.id}, ${item.quantity - 1})">-</button>
                                    <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary" onclick="updateQty(${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                            </td>
                            <td class="text-end fw-bold">
                                KSh ${item.subtotal.toLocaleString()}
                            </td>
                            <td class="text-end">
                                <button onclick="removeItem(${item.id})" class="btn btn-link text-danger p-0">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`;
                });
                html += '</table></div>';
                
                container.innerHTML = html;
                document.getElementById('total-amount').textContent = data.total.toLocaleString();
                document.getElementById('final-amount').textContent = data.total.toLocaleString();
                summary.style.display = 'block';
                updateBadge(data.cart_count);
            });
    }

    window.updateQty = function(id, qty) {
        if (qty < 0) return;
        fetch('api/cart.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: id, quantity: qty })
        }).then(() => loadCart());
    };

    window.removeItem = function(id) {
        Swal.fire({
            title: 'Remove item?',
            text: "Are you sure you want to remove this piece?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6a1b9a',
            confirmButtonText: 'Yes, remove it'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('api/cart.php?action=remove', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: id })
                }).then(() => loadCart());
            }
        });
    };

    function updateBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) badge.innerText = count;
    }
});
</script>