<?php
/**
 * admin/dashboard.php - Luxury Admin Dashboard
 */
require_once __DIR__ . '/../includes/session.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';

$dbInstance = Database::getInstance();
$pdo = $dbInstance->pdo;

try {
    $usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $ordersCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
    $requestsCount = $pdo->query("SELECT COUNT(*) FROM custom_requests WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    $usersCount = $ordersCount = $revenue = $requestsCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel'); ?> | Pinnah's Pretty Pieces</title>
    <link rel="stylesheet" href="../assets/css/admin-luxury.css">
</head>
<body class="admin-page">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <h2>✨ Admin Panel</h2>
        <nav class="admin-nav">
            <ul>
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="products.php">💎 Products</a></li>
                <li><a href="custom-requests.php">✨ Requests</a></li>
                <li><a href="dashboard.php?logout=1" class="btn-logout">🚪 Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></h1>
            <p>Your luxury jewelry empire awaits. <strong>Total Revenue: KSh <?php echo number_format($revenue, 2); ?></strong></p>
        </div>

        <!-- Stats Grid -->
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number"><?php echo $usersCount; ?></div>
                <div class="stat-label">Registered Customers</div>
            </div>
            <div class="stat-card">
                <h3>Active Orders</h3>
                <div class="stat-number"><?php echo $ordersCount; ?></div>
                <div class="stat-label">Pending & Processing</div>
            </div>
            <div class="stat-card">
                <h3>Custom Requests</h3>
                <div class="stat-number"><?php echo $requestsCount; ?></div>
                <div class="stat-label">Awaiting Response</div>
            </div>
            <div class="stat-card">
                <h3>Monthly Revenue</h3>
                <div class="stat-number">KSh</div>
                <div class="stat-label">Gross Sales</div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="admin-card" style="margin-top: 30px;">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 20px;">
                <a href="products.php" class="btn btn-primary">
                    <span>💎</span> Manage Products
                </a>
                <a href="users.php" class="btn btn-primary">
                    <span>👥</span> Manage Users
                </a>
                <a href="custom-requests.php" class="btn btn-primary">
                    <span>✨</span> View Requests
                </a>
                <a href="../" class="btn btn-primary">
                    <span>🛍️</span> View Store
                </a>
            </div>
        </div>
    </main>

    <script>
        // Simple logout handler
        const logoutLink = document.querySelector('[href*="logout=1"]');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = this.href;
                } else {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>