<?php
$pageTitle = 'Custom Design';
require_once __DIR__ . '/../includes/session.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div style="padding: 2rem; text-align: center; max-width: 800px; margin: 0 auto;">
    <h1>New Custom Design Request</h1>
    <p>Fill out the form below to start your personalized jewelry journey.</p>

    <form id="custom-form" style="max-width: 600px; margin: 0 auto;">
        <h3>1. Jewelry Type</h3>
        <p>Choose the type of jewelry you want to create.</p>
        <div class="jewelry-types">
            <div class="type-option selected" data-type="bracelet">
                <span class="type-icon">📿</span>
                <strong>Bracelet</strong><br>
                Wrist jewelry
            </div>
            <div class="type-option" data-type="necklace">
                <span class="type-icon">📿</span>
                <strong>Necklace</strong><br>
                Neck pieces
            </div>
            <div class="type-option" data-type="ring">
                <span class="type-icon">💍</span>
                <strong>Ring</strong><br>
                Finger jewelry
            </div>
            <div class="type-option" data-type="waistbead">
                <span class="type-icon">💎</span>
                <strong>Waistbead</strong><br>
                Waist jewelry
            </div>
        </div>
        <input type="hidden" id="jewelry-type" name="jewelry_type" value="bracelet" required>

        <h3>2. Design Vision & Details</h3>
        <p>Describe your vision, budget, and select the occasion/style.</p>
        <div class="form-group">
            <label>Describe Your Vision *</label>
            <textarea id="vision" name="description" rows="5" required placeholder="E.g., Elegant purple beads for a wedding..."></textarea>
        </div>
        <div class="form-group">
            <label>Budget ($)</label>
            <input type="number" id="budget" name="budget" step="0.01" min="0">
        </div>
        <div class="form-group">
            <label>Occasion/Style</label>
            <input type="text" id="occasion" name="occasion" placeholder="E.g., Casual, Formal, Wedding...">
        </div>
        <button type="submit" class="btn" style="width: 100%;">Submit Request</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const types = document.querySelectorAll('.type-option');
    types.forEach(option => {
        option.addEventListener('click', () => {
            types.forEach(o => o.classList.remove('selected'));
            option.classList.add('selected');
            document.getElementById('jewelry-type').value = option.dataset.type;
        });
    });

    document.getElementById('custom-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            user_name: '<?php echo htmlspecialchars(getUser()['full_name'] ?? 'Guest'); ?>',
            user_email: '<?php echo htmlspecialchars(getUser()['email'] ?? ''); ?>',
            phone: '<?php echo htmlspecialchars(getUser()['phone'] ?? ''); ?>',
            jewelry_type: document.getElementById('jewelry-type').value,
            description: document.getElementById('vision').value,
            budget: parseFloat(document.getElementById('budget').value) || 0,
            occasion: document.getElementById('occasion').value
        };

        fetch('<?php echo SITE_URL; ?>api/custom-requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Request submitted! We\'ll reply within 24 hours.');
                openWhatsApp(`Custom design: ${formData.description}`);
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>