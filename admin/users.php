<?php
/**
 * admin/users.php - Luxury User Management
 */
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

// Handle add/edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name']) && !isset($_POST['delete_user'])) {
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Users'); ?> | Pinnah's Pretty Pieces</title>
    <link rel="stylesheet" href="../assets/css/admin-luxury.css">
</head>
<body class="admin-page">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <h2>✨ Admin Panel</h2>
        <nav class="admin-nav">
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="users.php" class="active">👥 Users</a></li>
                <li><a href="products.php">💎 Products</a></li>
                <li><a href="custom-requests.php">✨ Requests</a></li>
                <li><a href="dashboard.php?logout=1" class="btn-logout">🚪 Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h1>Manage Users</h1>
                <button class="btn btn-add" onclick="openAddModal()">
                    <span>+</span> Add User
                </button>
            </div>

            <input 
                type="text" 
                class="search-input" 
                id="search-users"
                placeholder="🔍 Search users by name, email, phone..."
            >

            <!-- Users Table -->
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 120px;">Phone</th>
                            <th style="width: 90px;">Role</th>
                            <th style="width: 110px;">Joined</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        <?php foreach ($users as $user): ?>
                        <tr data-user-id="<?php echo $user['id']; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($user['phone'] ?: '-'); ?>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge badge-admin">👑 Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-user">👤 User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 0.9rem; color: var(--text-muted);">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </span>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-edit"
                                    data-id="<?php echo $user['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                    data-role="<?php echo $user['role']; ?>"
                                    onclick="editItem(this)"
                                >
                                    Edit
                                </button>
                                <?php if ($user['role'] !== 'admin'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button 
                                        type="submit" 
                                        name="delete_user" 
                                        class="btn btn-danger"
                                        onclick="return confirm('🔥 Delete this user permanently? This action cannot be undone.');"
                                    >
                                        Delete
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div id="user-modal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="closeModal()">&times;</button>
                <h2 id="modal-title">Add User</h2>
                
                <form id="user-form" method="POST" class="admin-form">
                    <input type="hidden" name="user_id" id="user-id" value="0">
                    
                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="user-name">Full Name <span style="color: var(--rose-gold);">*</span></label>
                        <input 
                            type="text" 
                            name="full_name" 
                            id="user-name" 
                            required 
                            placeholder="e.g., Aminata Johnson"
                        >
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="user-email">Email <span style="color: var(--rose-gold);">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            id="user-email" 
                            required
                            placeholder="user@example.com"
                        >
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="user-password">Password <span style="color: var(--rose-gold);">*</span></label>
                        <input 
                            type="password" 
                            name="password" 
                            id="user-password" 
                            required
                            placeholder="••••••••"
                        >
                        <small style="color: var(--text-muted); display: block; margin-top: 6px;">
                            Minimum 8 characters recommended.
                        </small>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="user-phone">Phone Number</label>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="user-phone"
                            placeholder="+254 712 345 678"
                        >
                    </div>

                    <!-- Role -->
                    <div class="form-group">
                        <label for="user-role">Role <span style="color: var(--rose-gold);">*</span></label>
                        <select name="role" id="user-role" required>
                            <option value="">Select a role</option>
                            <option value="user">👤 Regular User</option>
                            <option value="admin">👑 Administrator</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" style="align-self: flex-start; margin-top: 10px;">
                        💾 Save User
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const modal = document.getElementById('user-modal');

        function openAddModal() {
            document.getElementById('user-form').reset();
            document.getElementById('user-id').value = "0";
            document.getElementById('modal-title').textContent = '➕ Add User';
            document.getElementById('user-password').required = true;
            modal.style.display = 'block';
        }

        function editItem(btn) {
            document.getElementById('user-id').value = btn.dataset.id;
            document.getElementById('user-name').value = btn.dataset.name;
            document.getElementById('user-email').value = btn.dataset.email;
            document.getElementById('user-phone').value = btn.dataset.phone;
            document.getElementById('user-role').value = btn.dataset.role;
            document.getElementById('user-password').value = '';
            document.getElementById('user-password').required = false;
            document.getElementById('user-password').placeholder = 'Leave empty to keep current password';
            document.getElementById('modal-title').textContent = '✏️ Edit User';
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Search functionality
        document.getElementById('search-users').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#users-tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
