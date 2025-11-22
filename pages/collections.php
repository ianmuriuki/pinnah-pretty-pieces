<?php
$pageTitle = 'Collections';
$category = sanitizeInput($_GET['category'] ?? '');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

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

    function loadProducts(p) {
        const url = new URL('<?php echo SITE_URL; ?>api/products.php?action=list');
        url.searchParams.append('limit', perPage);
        url.searchParams.append('offset', p * perPage);
        <?php if ($category): ?>url.searchParams.append('category', '<?php echo $category; ?>');<?php endif; ?>

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const grid = document.getElementById('collections-grid');
                if (!data.success || data.data.length === 0) {
                    if (p === 0) grid.innerHTML = '<p style="text-align: center; color: var(--text-light);">No products found.</p>';
                    document.getElementById('load-more').style.visibility = 'hidden';
                    return;
                }

                data.data.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.innerHTML = `
                        <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 200px; object-fit: cover;">
                        <h3>${product.name}</h3>
                        <p style="font-weight: bold; color: var(--purple);">$ ${product.price}</p>
                        <p>${product.description.substring(0, 100)}...</p>
                        <button onclick="addToCart(${product.id})" class="btn" style="width: 100%; margin-top: 0.5rem;">Add to Cart</button>
                        <a href="?page=product-detail&id=${product.id}" style="display: block; text-align: center; margin-top: 0.5rem; color: var(--purple);">View Details</a>
                    `;
                    grid.appendChild(card);
                });

                if (data.data.length < perPage) document.getElementById('load-more').style.visibility = 'hidden';
            });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>