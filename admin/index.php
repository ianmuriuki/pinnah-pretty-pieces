<?php
/**
 * admin/index.php
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// If already admin, skip login
if (isAdmin()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We use 'email' to match standard login forms
    $email = sanitizeInput($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? '';

    if (loginUser($email, $password)) {
        if (isAdmin()) {
            header('Location: dashboard.php');
            exit();
        } else {
            // Logged in but not an admin
            $_SESSION = array();
            session_destroy();
            $error = "Access Denied: You do not have admin privileges.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Pinnah's Pretty Pieces</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body { background: #4a148c; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .login-box { background: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 350px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .error { color: red; background: #ffdada; padding: 10px; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #4a148c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center; color: #4a148c;">Admin Portal</h2>
        <?php if ($error): ?> <div class="error"><?php echo $error; ?></div> <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN TO DASHBOARD</button>
        </form>
    </div>
</body>
</html>