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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | Coral Sunset</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* Custom styles for the slider and font override */
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background-color: #f7f9fb; /* Light background for the Featured section */
        }
        /* Custom class to manage slider images (Full-screen background mode) */
        .slider-image {
            transition: opacity 1s ease-in-out;
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures the image covers the entire screen area */
            border-radius: 0; /* No rounding needed for full-screen */
        }
        .slider-image.active {
            opacity: 1;
        }
        .slider-dot {
            transition: background-color 0.3s;
        }
    </style>
</head>
<body>

<!-- Main Content Wrapper -->
<div class="min-h-screen">

    <!-- HERO SECTION: Designed for min-h-screen with Full-Page Background Slider -->
    <div class="relative w-full min-h-screen flex flex-col justify-center items-center p-4 md:p-8 overflow-hidden">

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

            <div class="absolute inset-0 bg-black/40 transition duration-1000 ease-in-out"></div>
        </div>

        <div class="relative z-10 w-full max-w-6xl flex flex-col items-center text-center text-white">
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white leading-tight mb-4 drop-shadow-2xl">
                Coral Sunset Treasures Meets Artistry
            </h1>
            <p class="text-lg md:text-xl text-white/90 mb-8 max-w-xl drop-shadow-lg">
                Vibrant coral beads capturing golden hour magic. Handcrafted treasures, designed uniquely for you.
            </p>
            
            <!-- Hero Buttons -->
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-10 w-full justify-center">
                <a href="?page=collections" class="w-full sm:w-auto px-10 py-3 rounded-full font-semibold text-pink-700 shadow-2xl transform transition duration-300 
                   bg-white hover:bg-pink-100 hover:scale-[1.03]">
                    Shop Coral →
                </a>
                <a href="?page=custom-design" class="w-full sm:w-auto px-10 py-3 rounded-full font-semibold text-white border-2 border-white 
                   bg-white/20 backdrop-blur-sm shadow-md transform transition duration-300 hover:bg-white/30 hover:scale-[1.03]">
                    Custom Design
                </a>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-3 gap-4 w-full max-w-md lg:max-w-lg mt-8">
                <?php
                $stats = [
                    ['value' => '100+', 'label' => 'Happy Customers'],
                    ['value' => '50+', 'label' => 'Unique Pieces'],
                    ['value' => '4.9', 'label' => 'Average Rating'],
                ];
                foreach ($stats as $stat) {
                    echo '
                    <div class="p-4 sm:p-6 bg-white/30 backdrop-blur-sm rounded-xl shadow-2xl border border-white/50 
                         text-center transform hover:scale-[1.05] transition duration-300">
                        <span class="text-2xl sm:text-3xl font-extrabold text-white block drop-shadow-md">'.$stat['value'].'</span>
                        <span class="text-xs sm:text-sm text-white/90 font-medium">'.$stat['label'].'</span>
                    </div>';
                }
                ?>
            </div>

            <!-- Slider Dots/Indicators (Positioned at the bottom of the hero content) -->
            <div id="slider-dots" class="mt-12 flex space-x-2 z-20">
                <!-- Data slide attributes match the slide indices (0, 1, 2) to align with JS -->
                <button data-slide="0" class="slider-dot w-3 h-3 bg-white rounded-full active-dot opacity-80"></button>
                <button data-slide="1" class="slider-dot w-3 h-3 bg-white rounded-full opacity-40 hover:opacity-80"></button>
                <button data-slide="2" class="slider-dot w-3 h-3 bg-white rounded-full opacity-40 hover:opacity-80"></button>
            </div>
            
        </div>
        
    </div>
    <!-- END HERO SECTION -->

    <!-- FEATURED ITEMS SECTION -->
    <section class="py-16 px-4 md:px-8 bg-white" id="featured-collections">
        <div class="max-w-7xl mx-auto">
            <header class="text-center mb-12">
                <p class="text-pink-600 font-semibold uppercase tracking-wider text-sm">Our Handpicked Selection</p>
                <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mt-2">
                    Treasures of the Sunset
                </h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                    Explore our most popular pieces—where vibrant coral meets golden artistry. Each piece is crafted with passion.
                </p>
            </header>

            <div id="featured-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
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
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden group transform transition duration-500 hover:shadow-2xl hover:-translate-y-2 border border-gray-100 product-card" data-id="'.$product['id'].'">
                            <!-- Product Image Container -->
                            <div class="relative overflow-hidden h-64">
                                <!-- Tag -->
                                <span class="absolute top-3 left-3 bg-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-md">
                                    '.$tag.'
                                </span>
                                <img src="'.$imagePath.'" 
                                     onerror="this.onerror=null;this.src=\''.$placeholder_url.'\'" 
                                     alt="'.htmlspecialchars($product['name']).'" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.05]"/>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-1 leading-snug">'.htmlspecialchars($product['name']).'</h3>
                                <p class="text-sm text-gray-500 mb-4 h-12 overflow-hidden">'.htmlspecialchars($product['description']).'</p>
                                
                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-3xl font-extrabold text-pink-600">KSh '.$display_price.'</p> <!-- CORRECTED: Added space -->
                                    <!-- Example Placeholder for Rating -->
                                    <div class="text-yellow-400 flex items-center space-x-1">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.487 7.09l6.56-.955L10 0l2.953 6.135 6.56.955-4.758 4.63 1.123 6.545z"/></svg>
                                        <span class="text-sm text-gray-600">4.8 (120)</span>
                                    </div>
                                </div>

                                <button onclick="addToCart('.$product['id'].')" 
                                        class="w-full py-3 bg-purple-600 text-white font-semibold rounded-lg shadow-lg hover:bg-purple-700 
                                               transition duration-300 transform hover:shadow-xl hover:scale-[0.99] flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z"></path></svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="col-span-full text-center text-gray-500 py-12">No featured products found. Please use the Admin Panel to mark products as featured.</p>';
                }
                ?>
                
            </div>
            
            <div class="text-center mt-12">
                <a href="?page=all-collections" class="inline-flex items-center text-lg font-semibold text-purple-600 hover:text-purple-800 transition">
                    View All Collections
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
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
        document.querySelectorAll('.slider-dot').forEach(dot => dot.classList.remove('active-dot', 'opacity-80'));
        document.querySelectorAll('.slider-dot').forEach(dot => dot.classList.add('opacity-40'));

        // Set the active slide and dot
        slides[index].classList.add('active');
        // Find the dot whose data-slide attribute matches the current index
        const activeDot = dotsContainer.querySelector(`[data-slide="${index}"]`);
        if (activeDot) {
            activeDot.classList.add('active-dot', 'opacity-80');
            activeDot.classList.remove('opacity-40');
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
</body>
</html>