<?php
$pageTitle = 'Register';
require_once __DIR__ . '/../includes/session.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div style="padding: 4rem 2rem; max-width: 400px; margin: 0 auto;">
    <h1 style="text-align: center; color: var(--purple);">Register</h1>
    <form id="register-form" style="background: var(--white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-light);">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" id="full-name" required>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="email" required>
        </div>
        <div class="form-group">
            <label>Password *</label>
            <input type="password" id="password" minlength="6" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="tel" id="phone">
        </div>
        <button type="submit" class="btn" style="width: 100%;">Register</button>
    </form>
    <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="?page=login">Login here</a></p>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {
        full_name: document.getElementById('full-name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        phone: document.getElementById('phone').value
    };

    fetch('<?php echo SITE_URL; ?>api/auth.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Registered! Please login.');
            window.location.href = '?page=login';
        } else {
            alert('Error: ' + data.message);
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>