<?php
$pageTitle = 'Collections';
$category = sanitizeInput($_GET['category'] ?? '');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<!-- page fragment; header provided by index.php -->

<h1 style="text-align: center; padding: 2rem; color: var(--purple);">Collections</h1>
<div id="collections-grid" class="product-grid">
    <!-- Dynamic load -->
    <p style="text-align: center; color: var(--text-light);">Loading collections...</p>
</div>
<button id="load-more" class="btn" style="display: block; margin: 2rem auto; visibility: hidden;">Load More</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let page = 0;
    const perPage = 8;
    loadProducts(page);

    document.getElementById('load-more').addEventListener('click', () => {
        page++;
        loadProducts(page);
    });

   // Inside the <script> tag of pages/collections.php
function loadProducts(p) {
    const url = new URL('<?php echo SITE_URL; ?>api/products.php?action=list');
    url.searchParams.append('limit', perPage);
    url.searchParams.append('offset', p * perPage);
    <?php if ($category): ?>url.searchParams.append('category', '<?php echo $category; ?>');<?php endif; ?>

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('collections-grid');
            if (p === 0) grid.innerHTML = ''; // Clear loading text

            if (!data.success || data.data.length === 0) {
                if (p === 0) grid.innerHTML = '<p class="text-center py-5">No jewelry found in this collection.</p>';
                document.getElementById('load-more').style.visibility = 'hidden';
                return;
            }

            data.data.forEach(product => {
                const card = document.createElement('div');
                card.className = 'col-md-3 mb-4'; // Added Bootstrap columns
                card.innerHTML = `
                    <div class="product-card shadow-sm rounded-4 overflow-hidden bg-white h-100 border-0 transition-all">
                        <div class="position-relative">
                            <img src="assets/images/products/${product.image || 'placeholder.jpg'}" 
                                 alt="${product.name}" 
                                 class="w-100 object-fit-cover" style="height: 250px;">
                        </div>
                        <div class="p-3 text-center">
                            <h5 class="fw-bold mb-1" style="color: #4a148c;">${product.name}</h5>
                            <p class="text-muted small mb-2">${product.description.substring(0, 60)}...</p>
                            <p class="fw-bold mb-3" style="color: #7b1fa2; font-size: 1.2rem;">KSh ${parseFloat(product.price).toLocaleString()}</p>
                            <button onclick="addToCart(${product.id})" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                            <a href="index.php?page=product-detail&id=${product.id}" class="d-block mt-2 small text-decoration-none text-muted">View Details</a>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });

            document.getElementById('load-more').style.visibility = data.data.length < perPage ? 'hidden' : 'visible';
        });
}

// Fixed Add to Cart Function
window.addToCart = function(productId) {
    fetch('api/cart.php?action=add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update the navbar badge
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.innerText = data.cart_count;

            Swal.fire({
                icon: 'success',
                title: 'Added to Cart!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
};