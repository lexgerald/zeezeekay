<?php
// admin/products.php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'You do not have permission to access the admin area.';
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$db = getDB();
$message = '';
$error = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $current_image = $_POST['current_image'] ?? 'placeholder.png';
    
    // Handle image upload
    $image_name = $current_image;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/zeekay-store/assets/images/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            // Generate unique filename
            $image_name = time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                // Delete old image if it's not the default placeholder
                if ($current_image !== 'placeholder.png' && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                $error = '<div class="alert alert-danger">Failed to upload image.</div>';
                $image_name = $current_image;
            }
        } else {
            $error = '<div class="alert alert-danger">Invalid file type. Allowed: JPG, PNG, GIF, WEBP</div>';
            $image_name = $current_image;
        }
    }
    
    try {
        if ($action === 'add') {
            $stmt = $db->prepare("INSERT INTO products (name, description, price, stock, category, featured, image) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $stock, $category, $featured, $image_name]);
            $message = '<div class="alert alert-success">Product added successfully!</div>';
        } elseif ($action === 'edit' && $id > 0) {
            $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, featured = ?, image = ? 
                                   WHERE id = ?");
            $stmt->execute([$name, $description, $price, $stock, $category, $featured, $image_name, $id]);
            $message = '<div class="alert alert-success">Product updated successfully!</div>';
        } elseif ($action === 'delete' && $id > 0) {
            // Get product image before deleting
            $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            
            // Delete image file
            if ($product && $product['image'] !== 'placeholder.png') {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/zeekay-store/assets/images/' . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div class="alert alert-success">Product deleted successfully!</div>';
        }
    } catch (PDOException $e) {
        $error = '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
    }
}

// Get all products
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-box-seam"></i> Manage Products</h2>
            <p class="text-muted">Add, edit, or delete products with images</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php echo $message; ?>
    <?php echo $error; ?>
    
    <div class="mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">
            <i class="bi bi-plus-circle"></i> Add New Product
        </button>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if($product['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . '/zeekay-store/assets/images/' . $product['image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             onerror="this.src='<?php echo BASE_URL; ?>assets/images/placeholder.png'">
                                    <?php else: ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/placeholder.png" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" 
                                             alt="Placeholder">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>Le <?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php if($product['stock'] <= 0): ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php elseif($product['stock'] <= 5): ?>
                                        <span class="badge bg-warning"><?php echo $product['stock']; ?> left</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td>
                                    <?php if($product['featured']): ?>
                                        <span class="badge bg-primary"><i class="bi bi-star-fill"></i> Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-warning edit-product" 
                                                data-id="<?php echo $product['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                data-price="<?php echo $product['price']; ?>"
                                                data-stock="<?php echo $product['stock']; ?>"
                                                data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                                data-featured="<?php echo $product['featured']; ?>"
                                                data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal with Image Upload -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="productId" value="0">
                    <input type="hidden" name="current_image" id="currentImage" value="placeholder.png">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price (Le) *</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Electronics, Clothing">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1">
                                <label class="form-check-label" for="featured">
                                    <i class="bi bi-star"></i> Featured Product
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                <div class="image-upload-container">
                                    <div class="current-image-preview mb-2 text-center">
                                        <img id="imagePreview" 
                                             src="<?php echo BASE_URL; ?>assets/images/placeholder.png" 
                                             alt="Product Image Preview"
                                             style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 2px dashed #dee2e6; padding: 5px;">
                                    </div>
                                    <input type="file" class="form-control" id="product_image" name="product_image" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Allowed: JPG, PNG, GIF, WEBP (Max 2MB)</small>
                                    <div id="imageInfo" class="mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveProductBtn">
                        <i class="bi bi-save"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.image-upload-container {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.image-upload-container:hover {
    border-color: #0d6efd;
    background: #f0f7ff;
}

.current-image-preview {
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#imagePreview {
    object-fit: contain;
    transition: all 0.3s ease;
}

#imagePreview:hover {
    transform: scale(1.02);
}

.btn-group .btn {
    border-radius: 4px;
}
</style>

<script>
// Preview image before upload
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const imageInfo = document.getElementById('imageInfo');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const file = input.files[0];
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            imageInfo.innerHTML = '<div class="text-danger"><i class="bi bi-exclamation-triangle"></i> File too large (max 2MB)</div>';
            input.value = '';
            return;
        }
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            imageInfo.innerHTML = '<div class="text-danger"><i class="bi bi-exclamation-triangle"></i> Invalid file type. Allowed: JPG, PNG, GIF, WEBP</div>';
            input.value = '';
            return;
        }
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            imageInfo.innerHTML = `<div class="text-success"><i class="bi bi-check-circle"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</div>`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '<?php echo BASE_URL; ?>assets/images/placeholder.png';
        imageInfo.innerHTML = '';
    }
}

// Edit product handler
document.querySelectorAll('.edit-product').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('productId').value = this.dataset.id;
        document.getElementById('name').value = this.dataset.name;
        document.getElementById('description').value = this.dataset.description;
        document.getElementById('price').value = this.dataset.price;
        document.getElementById('stock').value = this.dataset.stock;
        document.getElementById('category').value = this.dataset.category;
        document.getElementById('featured').checked = this.dataset.featured == '1';
        document.getElementById('currentImage').value = this.dataset.image || 'placeholder.png';
        
        // Update image preview
        const imagePath = this.dataset.image && this.dataset.image !== 'placeholder.png' 
            ? '<?php echo BASE_URL; ?>assets/images/' + this.dataset.image 
            : '<?php echo BASE_URL; ?>assets/images/placeholder.png';
        document.getElementById('imagePreview').src = imagePath;
        document.getElementById('imageInfo').innerHTML = `<div class="text-muted"><i class="bi bi-image"></i> Current image</div>`;
        
        // Reset file input
        document.getElementById('product_image').value = '';
        
        new bootstrap.Modal(document.getElementById('productModal')).show();
    });
});

// Reset form when modal is hidden
document.getElementById('productModal').addEventListener('hidden.bs.modal', function() {
    resetForm();
});

// Reset form function
function resetForm() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('formAction').value = 'add';
    document.getElementById('productId').value = '0';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.getElementById('price').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('category').value = '';
    document.getElementById('featured').checked = false;
    document.getElementById('currentImage').value = 'placeholder.png';
    document.getElementById('imagePreview').src = '<?php echo BASE_URL; ?>assets/images/placeholder.png';
    document.getElementById('product_image').value = '';
    document.getElementById('imageInfo').innerHTML = '';
}

// Form validation before submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a product name.');
        return false;
    }
    
    if (!price || parseFloat(price) <= 0) {
        e.preventDefault();
        alert('Please enter a valid price.');
        return false;
    }
    
    if (!stock || parseInt(stock) < 0) {
        e.preventDefault();
        alert('Please enter a valid stock quantity.');
        return false;
    }
    
    return true;
});

// Auto-dismiss alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});
</script>

<?php require_once '../includes/footer.php'; ?>