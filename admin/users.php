<?php
require_once __DIR__ . '/../includes/session.php';
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC");

// Handle delete (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id = (int)$_POST['user_id'];
    $db->query("DELETE FROM users WHERE id = ? AND role != 'admin'", [$id]);
    header('Location: users.php');
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
            <li><a href="users.php" class="active">👥 Users</a></li>
            <li><a href="products.php">💎 Products</a></li>
            <li><a href="custom-requests.php">✨ Requests</a></li>
            <li><a href="../" class="btn-logout">🚪 Logout</a></li>
        </ul>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-card">
        <h1 style="color: var(--purple);">Manage Users</h1>
        <button class="btn btn-add" data-modal="add-user-modal" style="float: right;">+ Add User</button>
        <input type="text" class="search-input" placeholder="Search users..." style="width: 200px; margin-bottom: 1rem; padding: 0.5rem;">
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <button onclick="editItem(<?php echo $user['id']; ?>)" class="btn btn-edit btn-small">Edit</button>
                        <?php if ($user['role'] !== 'admin'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-small" onclick="return confirm('Delete user?')">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div id="add-user-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modal-title">Add User</h2>
            <form id="user-form" method="POST" action="users.php" style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn">Save</button>
            </form>
        </div>
    </div>
</div>

<?php
// Handle add/edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'])) {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email'], 'email');
    $password = hashPassword($_POST['password']);
    $phone = sanitizeInput($_POST['phone']);
    $role = sanitizeInput($_POST['role']);

    $db->query("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)", [$full_name, $email, $password, $phone, $role]);
    header('Location: users.php');
    exit();
}
?>

<?php include __DIR__ . '/../includes/footer.php'; ?>