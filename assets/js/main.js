// Main JS for Pinnah's Pretty Pieces – Cart, Forms, WhatsApp, Products
// Vanilla JS – No libs. Fixes lint errors (no redeclarations, proper objects/arrays)

let cart = JSON.parse(sessionStorage.getItem('cart')) || [];  // Cart state

// Update nav cart count
function updateCartCount() {
  const countEl = document.querySelector('.cart-count');
  if (countEl) {
    const totalQty = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
    countEl.textContent = totalQty;
  }
}

// Add to cart (AJAX + local update)
function addToCart(productId, quantity = 1) {
  const bodyData = { product_id: productId, quantity: quantity };
  fetch('api/cart.php?action=add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(bodyData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const existing = cart.find(item => item.id === productId);
      if (existing) {
        existing.quantity += quantity;
      } else {
        cart.push({ id: productId, quantity: quantity });
      }
      sessionStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
      alert('Added to cart! 💖');
    } else {
      alert('Error: ' + (data.message || 'Add failed'));
    }
  })
  .catch(err => {
    console.error('Cart add error:', err);
    alert('Add to cart failed—check connection.');
  });
}

// Remove from cart
function removeFromCart(productId) {
  const bodyData = { product_id: productId };
  fetch('api/cart.php?action=remove', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(bodyData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      cart = cart.filter(item => item.id !== productId);
      sessionStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
      location.reload();  // Refresh UI
    }
  })
  .catch(err => console.error('Cart remove error:', err));
}

// Update quantity
function updateQuantity(productId, quantity) {
  if (quantity <= 0) return removeFromCart(productId);
  const bodyData = { product_id: productId, quantity: parseInt(quantity) };
  fetch('api/cart.php?action=update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(bodyData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const item = cart.find(item => item.id === productId);
      if (item) item.quantity = parseInt(quantity);
      sessionStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
    }
  })
  .catch(err => console.error('Quantity update error:', err));
}

// Form validation
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return false;
  const required = form.querySelectorAll('[required]');
  for (let field of required) {
    if (!field.value.trim()) {
      alert('Please fill all required fields.');
      field.focus();
      return false;
    }
  }
  return true;
}

// WhatsApp open
function openWhatsApp(message) {
  const phone = '<?php echo str_replace("+", "", OWNER_PHONE); ?>';  // PHP in JS—header.php outputs it
  const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
  window.open(url, '_blank', 'noopener,noreferrer');
}

// Custom design type select
function initTypeSelection() {
  const options = document.querySelectorAll('.type-option');
  options.forEach(option => {
    option.addEventListener('click', () => {
      options.forEach(o => o.classList.remove('selected'));
      option.classList.add('selected');
      const hiddenInput = document.getElementById('jewelry-type');
      if (hiddenInput) hiddenInput.value = option.dataset.type;
    });
  });
}

// Contact form
function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;
  form.addEventListener('submit', (e) => {
    if (!validateForm('contact-form')) {
      e.preventDefault();
      return;
    }
    const method = document.getElementById('preferred-method').value;
    if (method === 'whatsapp') {
      e.preventDefault();
      const subject = document.getElementById('subject').value;
      const message = document.getElementById('message').value;
      openWhatsApp(`Subject: ${subject}\nMessage: ${message}`);
    } else {
      // Submit to API
      const formData = new FormData(form);
      fetch('api/custom-requests.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) alert('Message sent!');
        else alert('Error: ' + data.message);
      });
    }
  });
}

// Custom form
function initCustomForm() {
  const form = document.getElementById('custom-form');
  if (!form) return;
  form.addEventListener('submit', (e) => {
    if (!validateForm('custom-form')) {
      e.preventDefault();
      return;
    }
    const formData = new FormData(form);
    fetch('api/custom-requests.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Request sent! WhatsApp opening...');
        const vision = document.getElementById('vision').value;
        openWhatsApp(`Custom design inquiry: ${vision}`);
      } else {
        alert('Error: ' + data.message);
      }
    });
    e.preventDefault();
  });
}

// Load products (home/collections)
function loadProducts(containerId, params = '') {
  const container = document.getElementById(containerId);
  if (!container) return;
  fetch(`api/products.php?action=list${params}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success || !data.data || data.data.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: var(--text-light);">No products found. <a href="?page=collections">Shop all</a></p>';
        return;
      }
      let html = '';
      data.data.forEach(product => {
        html += `
          <div class="product-card">
            <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            <h3>${product.name}</h3>
            <p style="font-weight: bold; color: var(--purple);">$ ${product.price}</p>
            <p>${product.description.substring(0, 100)}...</p>
            <button onclick="addToCart(${product.id})" class="btn" style="width: 100%;">Add to Cart</button>
            <a href="?page=product-detail&id=${product.id}" style="display: block; text-align: center; margin-top: 0.5rem; color: var(--purple);">View Details</a>
          </div>
        `;
      });
      container.innerHTML = html;
    })
    .catch(err => {
      console.error('Products load error:', err);
      container.innerHTML = '<p style="text-align: center; color: red;">Error loading products. <button onclick="loadProducts(\'' + containerId + '\', \'' + params + '\')">Retry</button></p>';
    });
}

// DOM ready
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  initTypeSelection();
  initContactForm();
  initCustomForm();
  
  // Load featured on home
  const featured = document.getElementById('featured-grid');
  if (featured) loadProducts('featured-grid', '&featured=1');
  
  // Load collections
  const collections = document.getElementById('collections-grid');
  if (collections) loadProducts('collections-grid');
});

// Expose globals for onclick
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.openWhatsApp = openWhatsApp;
window.loadProducts = loadProducts;
window.validateForm = validateForm;
window.initCustomForm = initCustomForm;
window.initContactForm = initContactForm;