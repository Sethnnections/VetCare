<?php include 'app/views/layouts/header.php'; ?>
<?php include 'app/views/layouts/sidebar.php'; ?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">My Profile</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Profile Information</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                        <?php echo $_SESSION['flash']['message']; ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>

                <form id="profileForm" action="/client/profile/update" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" class="form-control" name="first_name" 
                                       value="<?php echo htmlspecialchars($client['first_name'] ?? ''); ?>" required>
                                <?php if (isset($errors['first_name'])): ?>
                                    <div class="text-danger"><?php echo $errors['first_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" class="form-control" name="last_name" 
                                       value="<?php echo htmlspecialchars($client['last_name'] ?? ''); ?>" required>
                                <?php if (isset($errors['last_name'])): ?>
                                    <div class="text-danger"><?php echo $errors['last_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($client['email'] ?? ''); ?>" readonly>
                                <small class="form-text text-muted">Email cannot be changed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="text" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="text-danger"><?php echo $errors['phone']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emergency Contact</label>
                                <input type="text" class="form-control" name="emergency_contact" 
                                       value="<?php echo htmlspecialchars($client['emergency_contact'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preferred Contact Method</label>
                                <select class="form-control" name="preferred_contact_method">
                                    <option value="phone" <?php echo ($client['preferred_contact_method'] ?? 'phone') == 'phone' ? 'selected' : ''; ?>>Phone</option>
                                    <option value="email" <?php echo ($client['preferred_contact_method'] ?? '') == 'email' ? 'selected' : ''; ?>>Email</option>
                                    <option value="sms" <?php echo ($client['preferred_contact_method'] ?? '') == 'sms' ? 'selected' : ''; ?>>SMS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($client['notes'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Profile Summary</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="<?php echo htmlspecialchars($client['profile_picture'] ?? 'img/figure/admin.jpg'); ?>" 
                         alt="Profile Picture" class="rounded-circle" width="120" height="120">
                </div>
                
                <div class="profile-info">
                    <h5><?php echo htmlspecialchars(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')); ?></h5>
                    <p class="text-muted">Client</p>
                    
                    <div class="mt-4">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email'] ?? 'Not set'); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['phone'] ?? 'Not set'); ?></p>
                        <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($client['emergency_contact'] ?? 'Not set'); ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('M j, Y', strtotime($client['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>