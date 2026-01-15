<?php
$pageTitle = 'Product Detail';
$id = (int)($_GET['id'] ?? 0);
require_once __DIR__ . '/../includes/session.php';
if ($id <= 0) {
    header('Location: ?page=collections');
    exit();
}
?>
<!-- page fragment; header provided by index.php -->

<div id="product-detail" style="padding: 2rem; max-width: 1000px; margin: 0 auto; display: none;">
    <!-- Dynamic -->
    <p style="text-align: center; color: var(--text-light);">Loading product...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const url = new URL('<?php echo SITE_URL; ?>api/products.php');
    url.searchParams.append('action', 'list');  // Reuse list with single filter if needed; or add 'get' action later
    url.searchParams.append('id', <?php echo $id; ?>);  // Assume API supports ?id= for single

    fetch('<?php echo SITE_URL; ?>api/products.php?action=list')  // Fetch all, filter client-side for now
        .then(res => res.json())
        .then(data => {
            const product = data.data.find(p => p.id === <?php echo $id; ?>);
            const container = document.getElementById('product-detail');
            if (!product) {
                container.innerHTML = '<p style="text-align: center; color: red;">Product not found.</p>';
                return;
            }

            container.innerHTML = `
                <div style="display: flex; gap: 2rem; flex-wrap: wrap; @media (min-width: 768px) { flex-direction: row; }">
                    <img src="${product.image}" alt="${product.name}" style="width: 100%; max-width: 400px; height: 400px; object-fit: cover; border-radius: var(--border-radius);">
                    <div style="flex: 1;">
                        <h1>${product.name}</h1>
                        <p style="font-size: 1.5rem; color: var(--purple); font-weight: bold;">$${product.price}</p>
                        <p>${product.description}</p>
                        <p><strong>Category:</strong> ${product.category} | <strong>Stock:</strong> ${product.stock}</p>
                        <div style="margin-top: 1rem;">
                            <button onclick="addToCart(${product.id})" class="btn" style="margin-right: 1rem;">Add to Cart</button>
                            <a href="?page=cart" class="btn" style="background: var(--primary-pink);">View Cart</a>
                        </div>
                    </div>
                </div>
            `;
            container.style.display = 'block';
        });
});
</script>

<!-- footer provided by index.php -->