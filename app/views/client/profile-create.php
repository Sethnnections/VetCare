<?php
// app/views/client/profile-create.php
$old = $data['old'] ?? [];
$errors = $data['errors'] ?? [];
$user = $data['user'] ?? [];

// Set current page for sidebar highlighting
$current_page = 'profile';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Complete Your Profile
                    </h4>
                </div>
                <div class="card-body">
                    <?php 
                    // Display flash messages
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?>">
                            <?php echo $flash['message']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Welcome! Please complete your profile information to get started with our veterinary services.
                    </div>

                    <form action="<?php echo url('/client/profile/store'); ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
                                           id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($old['first_name'] ?? $user['first_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
                                           id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($old['last_name'] ?? $user['last_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                           id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($old['phone'] ?? $user['phone'] ?? ''); ?>" required>
                                    <?php if (isset($errors['phone'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">We'll use this for appointment reminders</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label">Emergency Contact *</label>
                                    <input type="tel" class="form-control <?php echo isset($errors['emergency_contact']) ? 'is-invalid' : ''; ?>" 
                                           id="emergency_contact" name="emergency_contact" 
                                           value="<?php echo htmlspecialchars($old['emergency_contact'] ?? ''); ?>" required>
                                    <?php if (isset($errors['emergency_contact'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['emergency_contact']; ?></div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">In case we need to reach someone about your pet</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Your full address..."><?php echo htmlspecialchars($old['address'] ?? $user['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preferred_contact_method" class="form-label">Preferred Contact Method</label>
                                    <select class="form-control <?php echo isset($errors['preferred_contact_method']) ? 'is-invalid' : ''; ?>" 
                                            id="preferred_contact_method" name="preferred_contact_method">
                                        <option value="">Select preferred method</option>
                                        <option value="phone" <?php echo ($old['preferred_contact_method'] ?? '') == 'phone' ? 'selected' : ''; ?>>Phone Call</option>
                                        <option value="email" <?php echo ($old['preferred_contact_method'] ?? '') == 'email' ? 'selected' : ''; ?>>Email</option>
                                        <option value="sms" <?php echo ($old['preferred_contact_method'] ?? '') == 'sms' ? 'selected' : ''; ?>>SMS</option>
                                    </select>
                                    <?php if (isset($errors['preferred_contact_method'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['preferred_contact_method']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any special instructions, pet preferences, or important information..."><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">* indicates required fields</small>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Complete Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>