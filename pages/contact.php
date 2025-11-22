<?php
$pageTitle = 'Contact';
require_once __DIR__ . '/../includes/session.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="form-section">
    <div class="contact-info">
        <h2>Contact Information</h2>
        <div class="contact-card">
            <span class="contact-icon">📞</span>
            <strong>Phone</strong><br>
            <?php echo OWNER_PHONE; ?><br>
            <?php echo BUSINESS_HOURS; ?>
        </div>
        <div class="contact-card">
            <span class="contact-icon">✉️</span>
            <strong>Email</strong><br>
            <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a><br>
            We respond within 24 hours.
        </div>
        <div class="contact-card">
            <span class="contact-icon">💬</span>
            <strong>WhatsApp</strong><br>
            <?php echo OWNER_PHONE; ?><br>
            Instant messaging support.
        </div>
        <div class="contact-card">
            <span class="contact-icon">📍</span>
            <strong>Visit Us</strong><br>
            <?php echo OWNER_ADDRESS; ?>
        </div>
        <div class="contact-card">
            <span class="contact-icon">🕒</span>
            <strong>Business Hours</strong><br>
            <?php echo BUSINESS_HOURS; ?>
        </div>
    </div>

    <form id="contact-form" class="message-form" style="max-width: 500px;">
        <h2>Send us a Message</h2>
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" id="full-name" required>
        </div>
        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" id="email" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" id="phone">
        </div>
        <div class="form-group">
            <label>Preferred Contact Method *</label>
            <select id="preferred-method" required>
                <option value="">Select method</option>
                <option value="email">Email</option>
                <option value="phone">Phone</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
        </div>
        <div class="form-group">
            <label>Subject *</label>
            <input type="text" id="subject" required>
        </div>
        <div class="form-group">
            <label>Message *</label>
            <textarea id="message" rows="5" required placeholder="Tell us more about your inquiry or custom design ideas."></textarea>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Send Message →</button>
    </form>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const method = document.getElementById('preferred-method').value;
    if (method === 'whatsapp') {
        openWhatsApp(`Subject: ${document.getElementById('subject').value}\nMessage: ${document.getElementById('message').value}`);
        return;
    }

    const formData = {
        user_name: document.getElementById('full-name').value,
        user_email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        jewelry_type: 'general',  // For contact, not custom
        description: document.getElementById('message').value,
        subject: document.getElementById('subject').value
    };

    fetch('<?php echo SITE_URL; ?>api/custom-requests.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) alert('Message sent! We\'ll respond soon.');
        else alert('Error: ' + data.message);
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>