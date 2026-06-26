<?php
// admin/users.php
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

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    
    try {
        if ($action === 'add') {
            // Add new user
            if (empty($name) || empty($email) || empty($password)) {
                $error = 'All fields are required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
            } else {
                // Check if email exists
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email already exists';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $hashedPassword, $role]);
                    $message = '<div class="alert alert-success">User added successfully!</div>';
                }
            }
        } elseif ($action === 'edit' && $userId > 0) {
            // Edit user
            if (empty($name) || empty($email)) {
                $error = 'Name and email are required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address';
            } else {
                // Check if email exists for other users
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $userId]);
                if ($stmt->fetch()) {
                    $error = 'Email already exists for another user';
                } else {
                    // Update user info
                    $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $role, $userId]);
                    
                    // Update password if provided
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $error = 'Password must be at least 6 characters';
                        } else {
                            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $stmt->execute([$hashedPassword, $userId]);
                        }
                    }
                    
                    if (!$error) {
                        $message = '<div class="alert alert-success">User updated successfully!</div>';
                    }
                }
            }
        } elseif ($action === 'delete' && $userId > 0) {
            // Delete user
            if ($userId == $_SESSION['user_id']) {
                $error = 'You cannot delete your own account!';
            } else {
                // Check if user has orders
                $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                $stmt->execute([$userId]);
                $orderCount = $stmt->fetchColumn();
                
                if ($orderCount > 0) {
                    $error = 'Cannot delete user with existing orders!';
                } else {
                    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $message = '<div class="alert alert-success">User deleted successfully!</div>';
                }
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get all users
$users = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

// Get statistics
$totalUsers = count($users);
$adminCount = 0;
$userCount = 0;

foreach($users as $user) {
    if ($user['role'] === 'admin') {
        $adminCount++;
    } else {
        $userCount++;
    }
}
?>
<div class="container mt-4">
    <!-- Admin Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-people"></i> Manage Users</h2>
            <p class="text-muted">Add, edit, or delete users</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <?php echo $message; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Users</h6>
                    <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Regular Users</h6>
                    <h2 class="mb-0"><?php echo $userCount; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Admin Users</h6>
                    <h2 class="mb-0"><?php echo $adminCount; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add User Button -->
    <div class="mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
            <i class="bi bi-plus-circle"></i> Add New User
        </button>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                        <?php if($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-info">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                <button class="btn btn-warning edit-user" 
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                        data-role="<?php echo $user['role']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger delete-user" 
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($user['name']); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-warning edit-user" 
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                        data-role="<?php echo $user['role']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <span class="text-muted ms-2">Cannot delete self</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- User Modal (Add/Edit) -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="user_id" id="userId" value="0">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password 
                            <span id="passwordRequired" class="text-danger">*</span>
                            <small class="text-muted" id="passwordHint">(Min 6 characters)</small>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter password" minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">
                        <i class="bi bi-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" id="deleteUserId" value="0">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Edit user handler
document.querySelectorAll('.edit-user').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('userId').value = this.dataset.id;
        document.getElementById('name').value = this.dataset.name;
        document.getElementById('email').value = this.dataset.email;
        document.getElementById('role').value = this.dataset.role;
        document.getElementById('password').placeholder = 'Leave blank to keep current password';
        document.getElementById('password').required = false;
        document.getElementById('passwordRequired').style.display = 'none';
        document.getElementById('passwordHint').textContent = '(Leave blank to keep current)';
        
        new bootstrap.Modal(document.getElementById('userModal')).show();
    });
});

// Delete user handler
document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteUserName').textContent = this.dataset.name;
        document.getElementById('deleteUserId').value = this.dataset.id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

// Reset form when adding new user
function resetUserForm() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('formAction').value = 'add';
    document.getElementById('userId').value = '0';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('role').value = 'user';
    document.getElementById('password').value = '';
    document.getElementById('password').placeholder = 'Enter password';
    document.getElementById('password').required = true;
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('passwordHint').textContent = '(Min 6 characters)';
}

// Form validation before submit
document.getElementById('userForm').addEventListener('submit', function(e) {
    const action = document.getElementById('formAction').value;
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    if (!name || !email) {
        e.preventDefault();
        alert('Name and email are required.');
        return false;
    }
    
    if (action === 'add' && !password) {
        e.preventDefault();
        alert('Password is required for new users.');
        return false;
    }
    
    if (password && password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters.');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address.');
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

<style>
.btn-group .btn {
    border-radius: 4px;
    margin: 0 2px;
}

#usersTable tbody tr:hover {
    background-color: #f8f9fa;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}
</style>

<?php require_once '../includes/footer.php'; ?>