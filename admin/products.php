<?php
require_once __DIR__ . '/../includes/session.php';
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}
$pageTitle = 'Manage Products';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getInstance();
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC");

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = (int)$_POST['product_id'];
    $db->query("DELETE FROM products WHERE id = ?", [$id]);
    header('Location: products.php');
    exit();
}

// Handle add/edit (POST)
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
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploaded = handleUpload($_FILES['image'], 'products');
            if ($uploaded) $params[3] = $uploaded;  // Wait, adjust for image column
            $sql = "UPDATE products SET name=?, description=?, price=?, image=?, category=?, stock=?, is_featured=? WHERE id=?";
            $params = [$name, $description, $price, $uploaded, $category, $stock, $is_featured, $id];
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
<script src="../assets/js/admin.js" defer></script>

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
        <button class="btn btn-add" data-modal="add-product-modal" style="float: right;">+ Add Product</button>
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
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo ucfirst($product['category']); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo $product['is_featured'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button onclick="editItem(<?php echo $product['id']; ?>)" class="btn btn-edit btn-small">Edit</button>
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
    <div id="add-product-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modal-title">Add Product</h2>
            <form id="product-form" method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="id" value="0">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="bracelet">Bracelet</option>
                        <option value="necklace">Necklace</option>
                        <option value="ring">Ring</option>
                        <option value="waistbead">Waistbead</option>
                        <option value="earrings">Earrings</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock</label>
                    <input type="number" name="stock" required>
                </div>
                <div class="form-group">
                    <label>Featured</label>
                    <input type="checkbox" name="is_featured">
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" class="btn">Save</button>
            </form>
        </div>
    </div>
</div>

<script>
// For edit: Populate modal (fetch single product)
function editItem(id) {
    fetch(`../api/products.php?action=list&id=${id}`)  // Assume API supports single fetch
        .then(res => res.json())
        .then(data => {
            const product = data.data[0];  // First match
            const form = document.getElementById('product-form');
            form.elements['id'].value = product.id;
            form.elements['name'].value = product.name;
            form.elements['description'].value = product.description;
            form.elements['price'].value = product.price;
            form.elements['category'].value = product.category;
            form.elements['stock'].value = product.stock;
            form.elements['is_featured'].checked = product.is_featured;
            document.getElementById('modal-title').textContent = 'Edit Product';
            document.getElementById('add-product-modal').style.display = 'block';
        });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>