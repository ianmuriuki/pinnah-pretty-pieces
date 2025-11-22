<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../includes/session.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div style="padding: 4rem 2rem; max-width: 400px; margin: 0 auto;">
    <h1 style="text-align: center; color: var(--purple);">Login</h1>
    <form id="login-form" style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-light);">
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="email" required>
        </div>
        <div class="form-group">
            <label>Password *</label>
            <input type="password" id="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Don't have an account? <a href="?page=register">Register here</a></p>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    fetch('<?php echo SITE_URL; ?>api/auth.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/';
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>