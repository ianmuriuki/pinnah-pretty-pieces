<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['user_id'] = 1;  // Mock admin ID
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_email'] = ADMIN_EMAIL;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--gradient);">
    <div class="admin-card" style="width: 100%; max-width: 400px; padding: 2rem;">
        <h1 style="text-align: center; color: var(--purple); margin-bottom: 1rem;">Admin Dashboard</h1>
        <?php if ($error): ?><p style="color: #ff6b6b; text-align: center;"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--primary-pink); border-radius: 4px;">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--primary-pink); border-radius: 4px;">
            </div>
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: var(--text-light);">Dev: admin / admin</p>
    </div>
</body>
</html>