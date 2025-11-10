<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional online bookstore with a wide selection of books">
    <title><?php echo isset($pageTitle) ? sanitizeOutput($pageTitle) . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <a href="<?php echo BASE_URL; ?>/index.php">
                        <i class="fas fa-book"></i> <?php echo APP_NAME; ?>
                    </a>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <div class="nav-menu" id="navMenu">
                    <ul class="nav-links">
                        <li><a href="<?php echo BASE_URL; ?>/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/pages/shop.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'shop.php' ? 'active' : ''; ?>">Shop</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/pages/cart.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart"></i> Cart <span class="cart-count" id="cartCount">0</span>
                        </a></li>
                        <?php if (isAdmin()): ?>
                        <li><a href="<?php echo BASE_URL; ?>/pages/admin/dashboard.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'admin') !== false ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield"></i> Admin
                        </a></li>
                        <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/pages/admin/login.php">Admin Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="main-content">
