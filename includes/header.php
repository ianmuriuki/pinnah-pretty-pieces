<?php
// Ensure session is active and user is logged in
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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?php echo SITE_URL; ?>assets/js/main.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const collapse = document.querySelector('#navbarNav');
            const overlay = document.querySelector('.mobile-menu-overlay');
            const navbar = document.querySelector('.main-navbar');
            
            if (toggle && collapse && overlay) {
                toggle.addEventListener('click', function() {
                    setTimeout(function() {
                        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                        if (isExpanded) {
                            overlay.classList.add('active');
                            navbar.classList.add('show');
                        } else {
                            overlay.classList.remove('active');
                            navbar.classList.remove('show');
                        }
                    }, 10);
                });
                
                overlay.addEventListener('click', function() {
                    if (collapse.classList.contains('show')) {
                        toggle.click();
                    }
                });
                
                const navLinks = collapse.querySelectorAll('.nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 991 && collapse.classList.contains('show')) {
                            setTimeout(function() {
                                toggle.click();
                            }, 100);
                        }
                    });
                });
            }
        });
    </script>
</head>
<body>
   <nav class="navbar navbar-expand-lg fixed-top main-navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>index.php">
                <span class="brand-text">Pinnah's</span>
                <span class="brand-accent">Pretty Pieces</span>
                <i class="fas fa-gem brand-icon"></i>
            </a>
            
            <button class="navbar-toggler mobile-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>index.php">
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=collections">
                            <span>Collections</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>index.php?page=custom-design">
                            <span>Custom Design</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL;?>index.php?page=contact">
                            <span>Contact</span>
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-actions">
                    <a href="<?php echo SITE_URL; ?>index.php?page=collections" class="nav-action-icon" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>index.php?page=cart" class="nav-action-icon cart-icon" aria-label="Shopping Cart">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-badge">0</span>
                    </a>

                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>index.php?page=profile" class="nav-action-icon" aria-label="Profile">
                            <i class="fas fa-user-circle"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>index.php?page=login" class="btn btn-login shadow-sm">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <div class="mobile-menu-overlay"></div>
    <main class="main-content">