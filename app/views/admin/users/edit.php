<?php
$user = $user ?? [];
$errors = $errors ?? [];
$current_page = 'admin_users_edit';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit User: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                    </h4>
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

                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/update'); ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
                                                           id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                                    <?php if (isset($errors['first_name'])): ?>
                                                        <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="last_name" class="form-label">Last Name</label>
                                                    <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
                                                           id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                                    <?php if (isset($errors['last_name'])): ?>
                                                        <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                                   id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                                            <?php if (isset($errors['username'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                                   id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                            <?php if (isset($errors['email'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-control <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                                                    id="role" name="role" required>
                                                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                                <option value="veterinary" <?php echo ($user['role'] ?? '') === 'veterinary' ? 'selected' : ''; ?>>Veterinary</option>
                                                <option value="client" <?php echo ($user['role'] ?? '') === 'client' ? 'selected' : ''; ?>>Client</option>
                                            </select>
                                            <?php if (isset($errors['role'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['role']; ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                                   id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                            <?php if (isset($errors['phone'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">
                                                Active User Account
                                            </label>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="<?php echo url('/admin/users/' . $user['user_id']); ?>" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>