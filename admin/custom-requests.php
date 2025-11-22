<?php
require_once __DIR__ . '/../includes/session.php';
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}
$pageTitle = 'Custom Requests';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$requests = $db->query("SELECT * FROM custom_requests ORDER BY created_at DESC");

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['request_id'];
    $status = sanitizeInput($_POST['status']);
    $db->query("UPDATE custom_requests SET status = ? WHERE id = ?", [$status, $id]);
    header('Location: custom-requests.php');
    exit();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/admin.css">
<script src="../assets/js/admin.js" defer></script>

<div class="admin-sidebar">
    <h2>Admin Panel</h2>
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="products.php">💎 Products</a></li>
            <li><a href="custom-requests.php" class="active">✨ Requests</a></li>
            <li><a href="../" class="btn-logout">🚪 Logout</a></li>
        </ul>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-card">
        <h1 style="color: var(--purple);">Custom Design Requests</h1>
        <input type="text" class="search-input" placeholder="Search requests..." style="width: 200px; margin-bottom: 1rem; padding: 0.5rem;">
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo $request['id']; ?></td>
                    <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['user_email']); ?></td>
                    <td><?php echo ucfirst($request['jewelry_type']); ?></td>
                    <td><?php echo htmlspecialchars(substr($request['description'], 0, 50)) . '...'; ?></td>
                    <td>$<?php echo number_format($request['budget'], 2); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="padding: 0.25rem; border: 1px solid var(--primary-pink); border-radius: 4px;">
                                <option value="new" <?php echo $request['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="contacted" <?php echo $request['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $request['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                    <td>
                        <a href="mailto:<?php echo $request['user_email']; ?>?subject=Re: Custom <?php echo ucfirst($request['jewelry_type']); ?> Design" class="btn btn-edit btn-small">Reply</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>