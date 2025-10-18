<?php
$user = $user ?? [];
$activityLogs = $activityLogs ?? [];
$current_page = 'admin_users';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>User Details
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/admin/users/' . $user['user_id'] . '/edit'); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="<?php echo url('/admin/users'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Full Name:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Username:</div>
                                        <div class="col-sm-8">@<?php echo htmlspecialchars($user['username']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Email:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Phone:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Role:</div>
                                        <div class="col-sm-8">
                                            <span class="badge 
                                                <?php echo $user['role'] === 'admin' ? 'bg-danger' : 
                                                      ($user['role'] === 'veterinary' ? 'bg-primary' : 'bg-secondary'); ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Status:</div>
                                        <div class="col-sm-8">
                                            <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Last Login:</div>
                                        <div class="col-sm-8">
                                            <?php if ($user['last_login']): ?>
                                                <?php echo formatDateTime($user['last_login']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never logged in</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 fw-bold">Member Since:</div>
                                        <div class="col-sm-8"><?php echo formatDateTime($user['created_at']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address and Additional Info -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Additional Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Address:</strong>
                                            <p class="mt-1"><?php echo nl2br(htmlspecialchars($user['address'] ?? 'Not provided')); ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="mt-4">
                                        <h6 class="mb-3">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <?php if ($user['is_active']): ?>
                                                <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/deactivate'); ?>" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Deactivate this user?')">
                                                        <i class="fas fa-user-times me-1"></i>Deactivate User
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/activate'); ?>" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Activate this user?')">
                                                        <i class="fas fa-user-check me-1"></i>Activate User
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <!-- Password Reset Form -->
                                            <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                                                <i class="fas fa-key me-1"></i>Reset Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($activityLogs)): ?>
                                        <p class="text-muted">No recent activity</p>
                                    <?php else: ?>
                                        <div class="list-group">
                                            <?php foreach ($activityLogs as $log): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($log['message']); ?></h6>
                                                    <small><?php echo formatDateTime($log['created_at']); ?></small>
                                                </div>
                                                <?php if ($log['level'] !== 'INFO'): ?>
                                                    <span class="badge 
                                                        <?php echo $log['level'] === 'ERROR' ? 'bg-danger' : 
                                                              ($log['level'] === 'WARNING' ? 'bg-warning' : 'bg-info'); ?>">
                                                        <?php echo $log['level']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo url('/admin/users/' . $user['user_id'] . '/reset-password'); ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>