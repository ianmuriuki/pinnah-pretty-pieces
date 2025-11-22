<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/session.php';

$pageTitle = isset($pageTitle) ? $pageTitle . ' - ' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Luxurious handcrafted beads jewelry – <?php echo SITE_NAME; ?>. Shop necklaces, bracelets, rings & custom designs.">
    <title><?php echo htmlspecialchars($pageTitle . SITE_NAME); ?></title>
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/logo.png" type="image/png">
    <!-- Bootstrap 5 for modern responsive navbar/forms -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Fonts: Playfair headings, Inter body -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <script src="<?php echo SITE_URL; ?>assets/js/main.js" defer></script>
    <!-- Bootstrap JS for navbar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Navbar: Logo left, links center, search/icons right – No overlap -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" style="backdrop-filter: blur(10px);">
        <div class="container-fluid">
            <!-- Logo far left – No margin -->
            <a class="navbar-brand fw-bold fs-3 ms-0" href="<?php echo SITE_URL; ?>index.php" style="background: linear-gradient(135deg, var(--purple), var(--primary-pink)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                <?php echo SITE_NAME; ?> <i class="fas fa-gem ms-1" style="color: var(--primary-pink);"></i>
            </a>
            
            <!-- Toggler for mobile -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Collapsible nav -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Nav links – Center aligned -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="<?php echo SITE_URL; ?>index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="<?php echo SITE_URL; ?>index.php?page=collections"><i class="fas fa-boxes me-1"></i>Collections</a></li>
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="<?php echo SITE_URL; ?>index.php?page=custom-design"><i class="fas fa-magic me-1"></i>Custom Design</a></li>
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="<?php echo SITE_URL; ?>index.php?page=about"><i class="fas fa-info-circle me-1"></i>About</a></li>
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="<?php echo SITE_URL; ?>index.php?page=contact"><i class="fas fa-envelope me-1"></i>Contact</a></li>
                </ul>
                
                <!-- Search + Icons – Right aligned -->
                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <form action="<?php echo SITE_URL; ?>index.php" method="GET" class="d-flex me-3">
                        <input class="form-control me-1" type="search" name="search" placeholder="Search products..." aria-label="Search" style="border-radius: 25px 0 0 25px; border: 1px solid var(--primary-pink);" <?php echo isset($_GET['search']) ? 'value="' . htmlspecialchars($_GET['search']) . '"' : ''; ?>>
                        <button class="btn btn-outline-purple" type="submit" style="border-radius: 0 25px 25px 0;"><i class="fas fa-search"></i></button>
                        <input type="hidden" name="page" value="collections">
                    </form>
                    
                    <!-- Icons -->
                    <ul class="navbar-nav mb-0">
                        <li class="nav-item me-2"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=cart"><i class="fas fa-shopping-cart"></i> <span class="cart-count badge bg-pink rounded-pill">0</span></a></li>
                        <li class="nav-item me-2"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=wishlist"><i class="fas fa-heart"></i></a></li>
                        <?php if (isLoggedIn()): $user = getUser(); ?>
                            <li class="nav-item me-2"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=profile"><i class="fas fa-user"></i> <?php echo htmlspecialchars(substr($user['full_name'], 0, 1)); ?>.</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?logout=1"><i class="fas fa-sign-out-alt"></i></a></li>
                        <?php else: ?>
                            <li class="nav-item me-2"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=login"><i class="fas fa-sign-in-alt"></i></a></li>
                            <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=register"><i class="fas fa-user-plus"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <main class="pt-5 mt-3 pb-5">  <!-- Padding for fixed nav -->