<?php
/**
 * pages/orders.php - User Order History
 */
$pageTitle = 'My Orders';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit();
}
?>

<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold mb-0" style="color: #4a148c;">My Order History</h1>
            <a href="index.php?page=collections" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-shopping-bag me-2"></i>Shop More
            </a>
        </div>

        <div id="orders-container">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Fetching your order history...</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/orders.php?action=list')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('orders-container');
            
            if (!data.success || data.data.length === 0) {
                container.innerHTML = `
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>No orders yet</h4>
                        <p class="text-muted">Once you make a purchase, your orders will appear here.</p>
                        <a href="index.php?page=collections" class="btn btn-primary rounded-pill px-4 mt-2">Browse Collections</a>
                    </div>`;
                return;
            }

            let html = '';
            data.data.forEach(order => {
                const date = new Date(order.created_at).toLocaleDateString('en-GB', {
                    day: 'numeric', month: 'short', year: 'numeric'
                });
                
                const statusClass = getStatusClass(order.status);

                html += `
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-0 p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <small class="text-muted d-block text-uppercase">Order Number</small>
                                    <span class="fw-bold">#PIN-${order.id.toString().padStart(5, '0')}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block text-uppercase">Date Placed</small>
                                    <span class="fw-bold">${date}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block text-uppercase">Total Amount</small>
                                    <span class="fw-bold text-primary">KSh ${parseFloat(order.total).toLocaleString()}</span>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <span class="badge ${statusClass} rounded-pill px-3 py-2 text-uppercase">
                                        ${order.status}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bg-light border-top p-3 px-4">
                            <div class="small">
                                <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                <strong>Shipping to:</strong> ${order.shipping_address}
                            </div>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
        });

    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'pending': return 'bg-warning text-dark';
            case 'paid': return 'bg-info text-white';
            case 'shipped': return 'bg-primary text-white';
            case 'delivered': return 'bg-success text-white';
            default: return 'bg-secondary text-white';
        }
    }
});
</script>