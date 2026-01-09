<?php
// ===================================
// admin/users.php
// ===================================
require_once '../config/config.php';
requireRole('admin');

$pageTitle = 'Kelola Users';
$userClass = new User();

$roleFilter = get('role', '');
$users = $roleFilter ? $userClass->getAllUsers($roleFilter) : $userClass->getAllUsers();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Kelola Users</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="add_user.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah User
            </a>
        </div>
    </div>
    
    <?php displayFlashMessage(); ?>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <form method="GET">
                        <select class="form-select" name="role" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            <option value="kasir" <?php echo $roleFilter == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
                            <option value="client" <?php echo $roleFilter == 'client' ? 'selected' : ''; ?>>Client</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>No. Telepon</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong><?php echo $user['username']; ?></strong></td>
                            <td><?php echo $user['full_name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo roleBadge($user['role']); ?></td>
                            <td><?php echo $user['phone'] ?: '-'; ?></td>
                            <td><?php echo statusBadge($user['status']); ?></td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-outline-danger delete-confirm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
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

<?php include '../includes/footer.php'; ?>
