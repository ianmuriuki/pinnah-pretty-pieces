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
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/admin.css">
<script src="../assets/js/admin.js" defer></script>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-gem"></i>
                <span>Admin Panel</span>
            </a>
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <nav class="admin-nav">
            <ul>
                <li>
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="products.php" class="nav-link">
                        <i class="fas fa-gem"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="custom-requests.php" class="nav-link active">
                        <i class="fas fa-star"></i>
                        <span>Custom Requests</span>
                    </a>
                </li>
                <li>
                    <a href="../index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>View Site</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../index.php?logout=1" class="nav-link logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

<main class="admin-main">
        <div class="admin-header">
            <div>
                <h1 class="page-title">Custom Design Requests</h1>
                <p class="page-subtitle">Manage custom jewelry design requests from customers</p>
            </div>
            <div class="header-actions">
                <button class="btn-icon d-lg-none" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        <div class="admin-card">
            <div class="card-header">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search requests..." id="requestSearch">
                </div>
            </div>
            
            <div class="table-wrapper">
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
                    <td>KSh <?php echo number_format($request['budget'], 2); ?></td>
                    <td>
                        <form method="POST" style="display: inline;" onchange="this.submit()">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="form-control status-select">
                                <option value="new" <?php echo $request['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="contacted" <?php echo $request['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $request['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                    <td>
                        <a href="mailto:<?php echo $request['user_email']; ?>?subject=Re: Custom <?php echo ucfirst($request['jewelry_type']); ?> Design" class="btn-icon btn-edit" title="Reply">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
        });
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
    }
    
    // Request search
    const searchInput = document.getElementById('requestSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>