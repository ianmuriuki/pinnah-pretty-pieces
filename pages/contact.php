<?php
$pageTitle = 'Contact';
require_once __DIR__ . '/../includes/session.php';
?>

<section class="page-section contact-section">
    <div class="container">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="contact-info-card">
                    <h2>Contact Information</h2>
                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <i class="fas fa-phone contact-info-icon"></i>
                            <div>
                                <strong>Phone</strong>
                                <p><?php echo defined('OWNER_PHONE') ? OWNER_PHONE : '+254 712 345 678'; ?></p>
                                <p class="text-muted small"><?php echo defined('BUSINESS_HOURS') ? BUSINESS_HOURS : 'Mon-Sat: 9AM-6PM'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-envelope contact-info-icon"></i>
                            <div>
                                <strong>Email</strong>
                                <p><a href="mailto:<?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@pinnahsprettypieces.com'; ?>"><?php echo defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'support@pinnahsprettypieces.com'; ?></a></p>
                                <p class="text-muted small">We respond within 24 hours</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fab fa-whatsapp contact-info-icon"></i>
                            <div>
                                <strong>WhatsApp</strong>
                                <p><?php echo defined('OWNER_PHONE') ? OWNER_PHONE : '+254 712 345 678'; ?></p>
                                <p class="text-muted small">Instant messaging support</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt contact-info-icon"></i>
                            <div>
                                <strong>Visit Us</strong>
                                <p><?php echo defined('OWNER_ADDRESS') ? OWNER_ADDRESS : 'Nairobi, Kenya'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <i class="fas fa-clock contact-info-icon"></i>
                            <div>
                                <strong>Business Hours</strong>
                                <p><?php echo defined('BUSINESS_HOURS') ? BUSINESS_HOURS : 'Mon-Sat: 9AM-6PM'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="form-card">
                    <header class="form-header">
                        <h2>Send us a Message</h2>
                        <p>Have a question or custom design idea? We'd love to hear from you.</p>
                    </header>

                    <form id="contact-form" class="contact-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full-name">Full Name <span class="required">*</span></label>
                                    <input type="text" id="full-name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="required">*</span></label>
                                    <input type="email" id="email" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="preferred-method">Preferred Contact Method <span class="required">*</span></label>
                                    <select id="preferred-method" class="form-control" required>
                                        <option value="">Select method</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="whatsapp">WhatsApp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <input type="text" id="subject" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" class="form-control" rows="5" required placeholder="Tell us more about your inquiry or custom design ideas."></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

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

<!-- footer provided by index.php -->