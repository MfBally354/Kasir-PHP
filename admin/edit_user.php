<?php
// ===================================
// admin/edit_user.php
// ===================================
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Edit User';
$userClass = new User();

$id = get('id');
if (!$id) {
    redirect('/admin/users.php');
}

$user = $userClass->getUserById($id);
if (!$user) {
    setFlashMessage('User tidak ditemukan', 'danger');
    redirect('/admin/users.php');
}

if (isPost()) {
    $data = [
        'username' => post('username'),
        'full_name' => post('full_name'),
        'email' => post('email'),
        'role' => post('role'),
        'phone' => post('phone'),
        'address' => post('address'),
        'status' => post('status')
    ];
    
    // Update password if provided
    $newPassword = post('password');
    if (!empty($newPassword)) {
        $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
    }
    
    $result = $userClass->updateUser($id, $data);
    
    if ($result['success']) {
        setFlashMessage($result['message'], 'success');
        redirect('/admin/users.php');
    } else {
        setFlashMessage($result['message'], 'danger');
    }
}

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Edit User</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" 
                                       value="<?php echo $user['username']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" name="password" 
                                       placeholder="Kosongkan jika tidak ingin mengubah">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" 
                                   value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $user['email']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo $user['phone']; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" required>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="kasir" <?php echo $user['role'] == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
                                    <option value="client" <?php echo $user['role'] == 'client' ? 'selected' : ''; ?>>Client</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="address" rows="3"><?php echo $user['address']; ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Update User
                            </button>
                            <a href="users.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi User</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="120">Terdaftar:</td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                        </tr>
                        <tr>
                            <td>Terakhir Update:</td>
                            <td><?php echo formatDate($user['updated_at']); ?></td>
                        </tr>
                        <tr>
                            <td>Role Saat Ini:</td>
                            <td><?php echo roleBadge($user['role']); ?></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td><?php echo statusBadge($user['status']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
