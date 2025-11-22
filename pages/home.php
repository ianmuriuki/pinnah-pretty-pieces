<?php $pageTitle = 'Home'; ?>
<div class="hero">
    <h1>Coral Sunset Treasures Meets Artistry</h1>
    <p>Vibrant coral beads capturing golden hour magic. Handcrafted treasures.</p>
    <div class="hero-buttons">
        <a href="?page=collections" class="btn">Shop Coral →</a>
        <a href="?page=custom-design" class="btn">Custom Design</a>
    </div>
    <div class="stats">
        <div class="stat-card">
            <span>100+</span>
            Happy Customers
        </div>
        <div class="stat-card">
            <span>50+</span>
            Unique Pieces
        </div>
        <div class="stat-card">
            <span>4.9</span>
            Average Rating
        </div>
    </div>
</div>

<section>
    <h2>Featured Collections</h2>
    <div id="featured-grid" class="product-grid">
        <div class="loading"></div>  <!-- Spinner -->
        <p class="error-message" style="display: none; text-align: center; color: red;">Error loading products. <button onclick="loadProducts('featured-grid', '&featured=1')" class="btn">Retry</button></p>
        <!-- Fallback if JS fails -->
        <div class="product-card">
            <img src="assets/images/products/coral-necklace.jpg" alt="Coral Sunset Necklace">
            <div class="product-card-body">
                <h3>Coral Sunset Necklace</h3>
                <p class="price">$45.99</p>
                <p>Handcrafted vibrant beads.</p>
                <button onclick="addToCart(1)" class="btn">Add to Cart</button>
            </div>
        </div>
        <div class="product-card">
            <img src="assets/images/products/bracelet1.jpg" alt="Waistbead Bracelet">
            <div class="product-card-body">
                <h3>Waistbead Bracelet</h3>
                <p class="price">$29.99</p>
                <p>Elegant wrist jewelry.</p>
                <button onclick="addToCart(2)" class="btn">Add to Cart</button>
            </div>
        </div>
    </div>
</section>