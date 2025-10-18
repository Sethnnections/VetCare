<?php
$users = $users ?? [];
$search = $search ?? '';
$role = $role ?? '';
$status = $status ?? '';
$stats = $stats ?? [];
$pagination = $pagination ?? [];
$current_page = 'admin_users';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>User Management
                    </h4>
                    <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>Add New User
                    </a>
                </div>
                <div class="card-body">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card primary">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                                <div class="stat-label">Total Users</div>
                            </div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
                                <div class="stat-label">Active Users</div>
                            </div>
                        </div>
                        <div class="stat-card danger">
                            <div class="stat-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['inactive'] ?? 0; ?></div>
                                <div class="stat-label">Inactive Users</div>
                            </div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['veterinary'] ?? 0; ?></div>
                                <div class="stat-label">Veterinarians</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="<?php echo url('/admin/users'); ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Search</label>
                                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                                   placeholder="Search by name, email...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Role</label>
                                            <select class="form-control" name="role">
                                                <option value="">All Roles</option>
                                                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="veterinary" <?php echo $role === 'veterinary' ? 'selected' : ''; ?>>Veterinary</option>
                                                <option value="client" <?php echo $role === 'client' ? 'selected' : ''; ?>>Client</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select class="form-control" name="status">
                                                <option value="">All Status</option>
                                                <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Users List</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($users)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No users found</p>
                                    <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary">Add First User</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Last Login</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar me-3">
                                                            <?php 
                                                            $initials = '';
                                                            if (!empty($user['first_name'])) {
                                                                $initials = strtoupper(substr($user['first_name'], 0, 1));
                                                                if (!empty($user['last_name'])) {
                                                                    $initials .= strtoupper(substr($user['last_name'], 0, 1));
                                                                }
                                                            } else {
                                                                $initials = strtoupper(substr($user['username'], 0, 2));
                                                            }
                                                            echo $initials;
                                                            ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo $user['role'] === 'admin' ? 'bg-danger' : 
                                                              ($user['role'] === 'veterinary' ? 'bg-primary' : 'bg-secondary'); ?>">
                                                        <?php echo ucfirst($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-warning'; ?>">
                                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($user['last_login']): ?>
                                                        <small><?php echo formatDateTime($user['last_login']); ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">Never</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo url('/admin/users/' . $user['user_id']); ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo url('/admin/users/' . $user['user_id'] . '/edit'); ?>" 
                                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($user['is_active']): ?>
                                                            <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/deactivate'); ?>" 
                                                                  method="POST" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                                        title="Deactivate" onclick="return confirm('Deactivate this user?')">
                                                                    <i class="fas fa-user-times"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/activate'); ?>" 
                                                                  method="POST" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                        title="Activate" onclick="return confirm('Activate this user?')">
                                                                    <i class="fas fa-user-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <?php if ($pagination && $pagination['total_pages'] > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                            <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo url('/admin/users?page=' . $i . '&search=' . $search . '&role=' . $role . '&status=' . $status); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}
</style>