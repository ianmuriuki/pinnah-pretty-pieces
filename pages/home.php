<?php
// Assuming index.php is in a subdirectory, keeping the user's relative path structure
require_once __DIR__ . '/../config/config.php';
// Include functions to access the Database class (Adjust path as needed based on your structure)
require_once __DIR__ . '/../includes/functions.php'; 

$pageTitle = 'Home';

// FETCH FEATURED PRODUCTS
$featuredProducts = [];
try {
    $db = Database::getInstance();
    $featuredProducts = $db->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 4");
} catch (Exception $e) {
   
}
?>
<!-- Main Content Wrapper -->
<div class="main-content">
    <!-- HERO SECTION: Designed for Full-Page Background Slider -->
    <div style="position: relative; width: 100%; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem; overflow: hidden; z-index: 1000;">

        <div class="absolute inset-0 z-0">
              <img id="slide-0" class="slider-image active" 
                  src="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>assets/images/products/slide0.jpg" 
                  alt="Image of elegant jewelry and cosmetic products">
              <img id="slide-1" class="slider-image" 
                  src="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>assets/images/products/slide1.jpg" 
                  alt="Image of vibrant bead bracelets and waistbeads">
              <img id="slide-2" class="slider-image" 
                  src="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>assets/images/products/slide2.jpg" 
                  alt="Close-up image of handcrafted coral bead jewelry">

            <div style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.4); transition: opacity 1s ease-in-out;"></div>
        </div>

        <div style="position: relative; z-index: 10; width: 100%; max-width: 64rem; display: flex; flex-direction: column; align-items: center; text-align: center; color: var(--white);">
            <h1 class="hero-title">
                Coral Sunset Treasures Meets Artistry
            </h1>
            <p class="hero-subtitle">
                Vibrant coral beads capturing golden hour magic. Handcrafted treasures, designed uniquely for you.
            </p>
            
            <!-- Hero Buttons -->
            <div class="hero-buttons">
                <a href="index.php?page=collections" class="btn btn-primary" style="padding: 0.75rem 2.5rem; border-radius: 9999px; font-weight: 600;">
                    Shop Coral →
                </a>
                <a href="index.php?page=custom-design" class="btn btn-secondary" style="padding: 0.75rem 2.5rem; border-radius: 9999px; font-weight: 600;">
                    Custom Design
                </a>
            </div>

            <!-- Stats Cards Grid -->
            <div class="row g-4 justify-content-center" style="width: 100%; max-width: 28rem;">
                <?php
                $stats = [
                    ['value' => '100+', 'label' => 'Happy Customers'],
                    ['value' => '50+', 'label' => 'Unique Pieces'],
                    ['value' => '4.9', 'label' => 'Average Rating'],
                ];
                foreach ($stats as $stat) {
                    echo '
                    <div class="col-4">
                        <div class="card" style="background: rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 0.75rem; box-shadow: 0 0 2rem rgba(0, 0, 0, 0.2); text-align: center; transition: transform 0.3s ease;">
                            <div class="card-body" style="padding: 1rem;">
                                <span style="font-size: 1.5rem; font-weight: 800; color: var(--white); display: block; text-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.3);">' . $stat['value'] . '</span>
                                <span style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.9); font-weight: 500;">' . $stat['label'] . '</span>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>

            <!-- Slider Dots/Indicators -->
            <div id="slider-dots" style="margin-top: 3rem; display: flex; gap: 0.5rem; z-index: 20;">
                <button data-slide="0" class="slider-dot" style="width: 0.75rem; height: 0.75rem; background-color: var(--white); border-radius: 9999px; opacity: 0.8;"></button>
                <button data-slide="1" class="slider-dot" style="width: 0.75rem; height: 0.75rem; background-color: var(--white); border-radius: 9999px; opacity: 0.4;"></button>
                <button data-slide="2" class="slider-dot" style="width: 0.75rem; height: 0.75rem; background-color: var(--white); border-radius: 9999px; opacity: 0.4;"></button>
            </div>
            
        </div>
        
    </div>
    <!-- END HERO SECTION -->

    <!-- FEATURED ITEMS SECTION -->
    <section style="padding: 4rem 2rem; background-color: var(--white);" id="featured-collections">
        <div class="container">
            <header style="text-align: center; margin-bottom: 3rem;">
                <p style="color: var(--deep-pink); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.875rem;">Our Handpicked Selection</p>
                <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--text-dark); margin-top: 0.5rem;">
                    Treasures of the Sunset
                </h2>
                <p style="color: var(--text-light); margin-top: 1rem; max-width: 42rem; margin-left: auto; margin-right: auto;">
                    Explore our most popular pieces—where vibrant coral meets golden artistry. Each piece is crafted with passion.
                </p>
            </header>

            <div class="product-grid">
                
                <?php
                // MOCK DATA FALLBACK - ONLY USED IF DB IS EMPTY OR FETCH FAILS
                if (empty($featuredProducts)) {
                    $featuredProducts = [
                        ['id' => 1, 'name' => 'Coral Sunset Necklace', 'price' => '45.99', 'description' => 'Handcrafted vibrant coral beads capturing golden hour magic.', 'image' => 'assets/images/products/coral-necklace.jpg', 'tag' => 'Best Seller'],
                        ['id' => 2, 'name' => 'Waistbead Bracelet', 'price' => '29.99', 'description' => 'Elegant waist jewelry with soft pink and purple beads.', 'image' => 'assets/images/products/waistbead-bracelet.jpg', 'tag' => 'New Arrival'],
                        ['id' => 3, 'name' => 'Royal Blue Set', 'price' => '55.00', 'description' => 'A majestic set of blue beads symbolizing wisdom and depth.', 'image' => 'assets/images/products/royal-blue-set.jpg', 'tag' => 'Trending'],
                        ['id' => 4, 'name' => 'Sunstone Charm', 'price' => '78.99', 'description' => 'A vibrant sunstone pendant on a beaded chain, capturing light.', 'image' => 'assets/images/products/sunstone-charm.jpg', 'tag' => 'Premium'],
                    ];
                }
                // END MOCK DATA FALLBACK

                if (!empty($featuredProducts)) {
                    foreach ($featuredProducts as $product) {
                        $tag = isset($product['tag']) ? $product['tag'] : 'Featured';
                        if(isset($product['is_featured']) && $product['is_featured']) $tag = 'Trending';

                    
                        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
                        // Assuming the DB stores the web-root relative path, e.g., 'assets/images/products/my.jpg'
                        $dbImagePath = isset($product['image']) ? $product['image'] : 'assets/images/products/default.jpg';
                        $imagePath = $siteUrl . ltrim($dbImagePath, '/'); // Ensure no leading slash issues
                    
                        
                        $placeholder_text = str_replace(' ', '+', $product['name']);
                        $placeholder_url = "https://placehold.co/600x450/e9d5ff/4c51bf?text={$placeholder_text}";
                        
                        $display_price = number_format(isset($product['price']) ? $product['price'] : 0.00, 2);
                        
                        echo '
                        <div class="product-card" data-id="'.$product['id'].'">
                            <div class="product-image-wrapper">
                                <span style="position: absolute; top: 0.75rem; left: 0.75rem; background-color: var(--deep-pink); color: var(--white); font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; z-index: 10; box-shadow: var(--shadow-sm);">
                                    '.$tag.'
                                </span>
                                <img src="'.$imagePath.'" 
                                     onerror="this.onerror=null;this.src=\''.$placeholder_url.'\'" 
                                     alt="'.htmlspecialchars($product['name']).'" 
                                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-base);"/>
                            </div>
                            
                            <div class="product-card-body">
                                <h3 class="product-title">'.htmlspecialchars($product['name']).'</h3>
                                <p style="font-size: 0.875rem; color: var(--text-light); margin-bottom: 1rem; height: 3rem; overflow: hidden;">'.htmlspecialchars($product['description']).'</p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <p style="font-size: 1.875rem; font-weight: 800; color: var(--deep-pink);">KSh '.$display_price.'</p>
                                    <div style="color: #fbbf24; display: flex; align-items: center; gap: 0.25rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.487 7.09l6.56-.955L10 0l2.953 6.135 6.56.955-4.758 4.63 1.123 6.545z"/></svg>
                                        <span style="font-size: 0.875rem; color: var(--text-light);">4.8 (120)</span>
                                    </div>
                                </div>

                                <button onclick="addToCart('.$product['id'].')" 
                                        class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 600; border-radius: 0.5rem; box-shadow: var(--shadow-lg); transition: all var(--transition-base); display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z"></path></svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p style="grid-column: 1 / -1; text-align: center; color: var(--text-light); padding: 3rem 0;">No featured products found. Please use the Admin Panel to mark products as featured.</p>';
                }
                ?>
                
            </div>
            
            <div style="text-align: center; margin-top: 3rem;">
                <a href="index.php?page=collections" style="display: inline-flex; align-items: center; font-size: 1.125rem; font-weight: 600; color: var(--deep-purple); transition: color var(--transition-base);">
                    View All Collections
                    <svg style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>
    <!-- END FEATURED ITEMS SECTION -->

</div>

<script>
    // --- Slider/Carousel Logic (Unchanged) ---
    const slides = document.querySelectorAll('.slider-image');
    const dotsContainer = document.getElementById('slider-dots');
    let currentSlide = 0;
    const slideInterval = 5000; // Change image every 5 seconds

    function showSlide(index) {
        // Reset all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        document.querySelectorAll('.slider-dot').forEach(dot => {
            dot.classList.remove('active-dot');
            dot.style.opacity = '0.4';
        });

        // Set the active slide and dot
        slides[index].classList.add('active');
        // Find the dot whose data-slide attribute matches the current index
        const activeDot = dotsContainer.querySelector(`[data-slide="${index}"]`);
        if (activeDot) {
            activeDot.classList.add('active-dot');
            activeDot.style.opacity = '0.8';
        }
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    // Event listeners for dots
    dotsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('slider-dot')) {
            const slideIndex = parseInt(e.target.dataset.slide); // Parse the 0, 1, 2 index
            currentSlide = slideIndex;
            showSlide(currentSlide);
        }
    });

    // Start the automatic slideshow
    setInterval(nextSlide, slideInterval);


    // --- Placeholder Functions for Interactivity (Re-added) ---
    function addToCart(productId) {
        // IMPORTANT: In a live PHP environment, this would be an AJAX call
        // to a backend endpoint to securely add the item to the user's session/database cart.
        const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
        const productName = productCard ? productCard.querySelector('h3').textContent : `Product ${productId}`;
        
        // Custom Modal UI instead of alert()
        const message = `${productName} has been added to your cart!`;
        console.log(message);
        // You would typically show a temporary notification here (like a toast/snackbar).
    }
    
</script>