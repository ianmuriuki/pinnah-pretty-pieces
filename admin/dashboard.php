<?php
require_once __DIR__ . '/../includes/session.php';
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
// Stats queries
$usersCount = $db->query("SELECT COUNT(*) as count FROM users")[0]['count'];
$ordersCount = $db->query("SELECT COUNT(*) as count FROM orders WHERE status != 'cancelled'")[0]['count'];
$revenue = $db->query("SELECT SUM(total) as sum FROM orders WHERE status != 'cancelled'")[0]['sum'] ?? 0;
$requestsCount = $db->query("SELECT COUNT(*) as count FROM custom_requests WHERE status = 'new'")[0]['count'];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/admin.css">
<script src="../assets/js/admin.js" defer></script>

<div class="admin-sidebar">
    <h2>Admin Panel</h2>
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="products.php">💎 Products</a></li>
            <li><a href="custom-requests.php">✨ Requests</a></li>
            <li><a href="../" class="btn-logout">🚪 Logout</a></li>
        </ul>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-card">
        <h1 style="color: var(--purple);">Welcome to the Admin Dashboard</h1>
        <p>Manage your sparkling empire! Last updated: <?php echo date('F j, Y \a\t g:i A'); ?>.</p>
    </div>

    <div class="admin-stats">
        <div class="stat-item admin-card">
            <span style="font-size: 2.5rem; color: var(--purple);">👥 <?php echo $usersCount; ?></span>
            <p>Total Users</p>
        </div>
        <div class="stat-item admin-card">
            <span style="font-size: 2.5rem; color: var(--purple);">🛒 <?php echo $ordersCount; ?></span>
            <p>Active Orders</p>
        </div>
        <div class="stat-item admin-card">
            <span style="font-size: 2.5rem; color: var(--primary-pink);">💰 $<?php echo number_format($revenue, 2); ?></span>
            <p>Revenue</p>
        </div>
        <div class="stat-item admin-card">
            <span style="font-size: 2.5rem; color: var(--purple);">✨ <?php echo $requestsCount; ?></span>
            <p>New Requests</p>
        </div>
    </div>

    <div class="admin-card">
        <h2>Quick Actions</h2>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="users.php" class="btn">Manage Users</a>
            <a href="products.php" class="btn">Add Product</a>
            <a href="custom-requests.php" class="btn">View Requests</a>
            <a href="../?page=orders" class="btn" style="background: var(--primary-pink);">View Orders</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>