<?php
/**
 * admin/products.php - Luxury Product Management
 */
require_once __DIR__ . '/../includes/session.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Manage Products';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC");

// Handle DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = (int)$_POST['product_id'];
    $db->query("DELETE FROM products WHERE id = ?", [$id]);
    header('Location: products.php');
    exit();
}

// Handle ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $category = sanitizeInput($_POST['category']);
    $stock = (int)$_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $id = (int)$_POST['id'] ?? 0;

    if ($id > 0) {
        // Update
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, stock=?, is_featured=? WHERE id=?";
        $params = [$name, $description, $price, $category, $stock, $is_featured, $id];
        
        // Handle Image Update if new file provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploaded = handleUpload($_FILES['image'], 'products');
            if ($uploaded) {
                $sql = "UPDATE products SET name=?, description=?, price=?, image=?, category=?, stock=?, is_featured=? WHERE id=?";
                $params = [$name, $description, $price, $uploaded, $category, $stock, $is_featured, $id];
            }
        }
        $db->query($sql, $params);
    } else {
        // Add
        $image = isset($_FILES['image']) && $_FILES['image']['error'] === 0 ? handleUpload($_FILES['image'], 'products') : 'assets/images/products/default.jpg';
        $db->query("INSERT INTO products (name, description, price, image, category, stock, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)", [$name, $description, $price, $image, $category, $stock, $is_featured]);
    }
    header('Location: products.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Products'); ?> | Pinnah's Pretty Pieces</title>
    <link rel="stylesheet" href="../assets/css/admin-luxury.css">
</head>
<body class="admin-page">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <h2>✨ Admin Panel</h2>
        <nav class="admin-nav">
            <ul>
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="products.php" class="active">💎 Products</a></li>
                <li><a href="custom-requests.php">✨ Requests</a></li>
                <li><a href="dashboard.php?logout=1" class="btn-logout">🚪 Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h1>Manage Products</h1>
                <button class="btn btn-add" onclick="openAddModal()">
                    <span>+</span> Add Product
                </button>
            </div>

            <input 
                type="text" 
                class="search-input" 
                id="search-products"
                placeholder="🔍 Search products by name, category..."
            >

            <!-- Products Table -->
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Image</th>
                            <th>Name</th>
                            <th style="width: 100px;">Price</th>
                            <th style="width: 100px;">Category</th>
                            <th style="width: 70px;">Stock</th>
                            <th style="width: 80px;">Featured</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        <?php foreach ($products as $product): ?>
                        <tr data-product-id="<?php echo $product['id']; ?>">
                            <td>
                                <img 
                                    src="../<?php echo $product['image']; ?>" 
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    class="product-thumbnail"
                                    onerror="this.src='../assets/images/products/default.jpg'"
                                >
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 3px;">
                                    <?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?>
                                </div>
                            </td>
                            <td>
                                <strong>KSh <?php echo number_format($product['price'], 2); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?php echo ucfirst($product['category']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $product['stock'] > 5 ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-success">Featured</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-edit"
                                    data-id="<?php echo $product['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                    data-price="<?php echo $product['price']; ?>"
                                    data-category="<?php echo $product['category']; ?>"
                                    data-stock="<?php echo $product['stock']; ?>"
                                    data-featured="<?php echo $product['is_featured']; ?>"
                                    onclick="editItem(this)"
                                >
                                    Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button 
                                        type="submit" 
                                        name="delete_product" 
                                        class="btn btn-danger"
                                        onclick="return confirm('🔥 Delete this product permanently? This action cannot be undone.');"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div id="product-modal" class="modal">
            <div class="modal-content">
                <button class="close" onclick="closeModal()">&times;</button>
                <h2 id="modal-title">Add Product</h2>
                
                <form id="product-form" method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="id" id="product-id" value="0">
                    
                    <!-- Product Name -->
                    <div class="form-group">
                        <label for="product-name">Product Name <span style="color: var(--rose-gold);">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="product-name" 
                            required 
                            placeholder="e.g., Gold Beaded Necklace"
                        >
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="product-description">Description <span style="color: var(--rose-gold);">*</span></label>
                        <textarea 
                            name="description" 
                            id="product-description" 
                            rows="4"
                            required
                            placeholder="Describe your beautiful piece..."
                        ></textarea>
                    </div>

                    <!-- Price & Category Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price">Price (KSh) <span style="color: var(--rose-gold);">*</span></label>
                            <input 
                                type="number" 
                                name="price" 
                                id="product-price" 
                                step="0.01" 
                                required
                                placeholder="0.00"
                            >
                        </div>
                        <div class="form-group">
                            <label for="product-category">Category <span style="color: var(--rose-gold);">*</span></label>
                            <select name="category" id="product-category" required>
                                <option value="">Select a category</option>
                                <option value="bracelet">Bracelet</option>
                                <option value="necklace">Necklace</option>
                                <option value="ring">Ring</option>
                                <option value="waistbead">Waistbead</option>
                                <option value="earrings">Earrings</option>
                                <option value="cosmetics">Cosmetics</option>
                            </select>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="form-group">
                        <label for="product-stock">Stock Quantity <span style="color: var(--rose-gold);">*</span></label>
                        <input 
                            type="number" 
                            name="stock" 
                            id="product-stock" 
                            required
                            placeholder="0"
                        >
                    </div>

                    <!-- Featured Checkbox -->
                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            name="is_featured" 
                            id="product-featured" 
                            value="1"
                        >
                        <label for="product-featured">
                            ✨ Feature on Home Page
                        </label>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="product-image">Product Image</label>
                        <input 
                            type="file" 
                            name="image" 
                            id="product-image"
                            accept="image/*"
                        >
                        <small style="color: var(--text-muted); display: block; margin-top: 6px;">
                            Leave empty to keep current image when editing. Accepts JPG, PNG, GIF.
                        </small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary" style="align-self: flex-start; margin-top: 10px;">
                        💾 Save Product
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const modal = document.getElementById('product-modal');

        function openAddModal() {
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = "0";
            document.getElementById('modal-title').textContent = '➕ Add Product';
            modal.style.display = 'block';
        }

        function editItem(btn) {
            document.getElementById('product-id').value = btn.dataset.id;
            document.getElementById('product-name').value = btn.dataset.name;
            document.getElementById('product-description').value = btn.dataset.description;
            document.getElementById('product-price').value = btn.dataset.price;
            document.getElementById('product-category').value = btn.dataset.category;
            document.getElementById('product-stock').value = btn.dataset.stock;
            document.getElementById('product-featured').checked = (btn.dataset.featured == "1");
            document.getElementById('modal-title').textContent = '✏️ Edit Product';
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Search functionality
        document.getElementById('search-products').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#products-tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
