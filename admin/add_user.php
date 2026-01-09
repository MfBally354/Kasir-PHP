<?php
// ===================================
// admin/add_user.php
// ===================================
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Tambah User';
$userClass = new User();

if (isPost()) {
    $data = [
        'username' => post('username'),
        'password' => post('password'),
        'full_name' => post('full_name'),
        'email' => post('email'),
        'role' => post('role'),
        'phone' => post('phone'),
        'address' => post('address'),
        'status' => post('status')
    ];
    
    $result = $userClass->createUser($data);
    
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
            <h2 class="fw-bold">Tambah User</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="users.php">Users</a></li>
                    <li class="breadcrumb-item active">Tambah User</li>
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
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="kasir">Kasir</option>
                                    <option value="client">Client</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="address" rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan User
                            </button>
                            <a href="users.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
