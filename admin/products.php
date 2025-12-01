<?php
require_once __DIR__ . '/../includes/session.php';
// Ensure user is admin
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
                // If update includes image, change query
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
<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/admin.css">
<!-- Custom Style for Modal -->
<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
    .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px; }
    .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
</style>

<div class="admin-sidebar">
    <h2>Admin Panel</h2>
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="products.php" class="active">💎 Products</a></li>
            <li><a href="custom-requests.php">✨ Requests</a></li>
            <li><a href="../" class="btn-logout">🚪 Logout</a></li>
        </ul>
    </nav>
</div>

<div class="admin-main">
    <div class="admin-card">
        <h1 style="color: var(--purple);">Manage Products</h1>
        <button class="btn btn-add" onclick="openAddModal()" style="float: right;">+ Add Product</button>
        <input type="text" class="search-input" placeholder="Search products..." style="width: 200px; margin-bottom: 1rem; padding: 0.5rem;">
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><img src="../<?php echo $product['image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></td>
                    <td>KSh <?php echo number_format($product['price'], 2); ?></td> <!-- FIXED: Changed '$' to 'KSh' -->
                    <td><?php echo ucfirst($product['category']); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo $product['is_featured'] ? '<span style="color:green">Yes</span>' : 'No'; ?></td>
                    <td>
                        <!-- Pass data directly to editItem() via data attributes -->
                        <button class="btn btn-edit btn-small"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-category="<?php echo $product['category']; ?>"
                                data-stock="<?php echo $product['stock']; ?>"
                                data-featured="<?php echo $product['is_featured']; ?>"
                                onclick="editItem(this)">
                            Edit
                        </button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger btn-small" onclick="return confirm('Delete product? This will remove from frontend too!')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add Product</h2>
            <form id="product-form" method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="id" id="product-id" value="0">
                
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="product-name" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="product-description" rows="3" style="width: 100%; padding: 8px;"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" id="product-price" step="0.01" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="product-category" required style="width: 100%; padding: 8px;">
                        <option value="bracelet">Bracelet</option>
                        <option value="necklace">Necklace</option>
                        <option value="ring">Ring</option>
                        <option value="waistbead">Waistbead</option>
                        <option value="earrings">Earrings</option>
                        <option value="cosmetics">Cosmetics</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" id="product-stock" required style="width: 100%; padding: 8px;">
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <label>Featured (Show on Home)</label>
                    <input type="checkbox" name="is_featured" id="product-featured" value="1">
                </div>
                
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                    <p style="font-size: 0.8rem; color: #666;">Leave empty to keep current image when editing.</p>
                </div>
                
                <button type="submit" class="btn" style="margin-top: 15px; padding: 10px 20px; background-color: var(--purple, #6b21a8); color: white; border: none; border-radius: 4px; cursor: pointer;">Save Product</button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('product-modal');

    function openAddModal() {
        // Reset form for adding
        document.getElementById('product-form').reset();
        document.getElementById('product-id').value = "0";
        document.getElementById('modal-title').textContent = 'Add Product';
        document.getElementById('product-featured').checked = false;
        modal.style.display = 'block';
    }

    function editItem(btn) {
        // Populate form from data attributes
        document.getElementById('product-id').value = btn.dataset.id;
        document.getElementById('product-name').value = btn.dataset.name;
        document.getElementById('product-description').value = btn.dataset.description;
        document.getElementById('product-price').value = btn.dataset.price;
        document.getElementById('product-category').value = btn.dataset.category;
        document.getElementById('product-stock').value = btn.dataset.stock;
        
        // Handle boolean for featured checkbox
        // Note: dataset.featured might be string "0" or "1" or empty
        document.getElementById('product-featured').checked = (btn.dataset.featured == "1");

        document.getElementById('modal-title').textContent = 'Edit Product';
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // Close modal if user clicks outside
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>