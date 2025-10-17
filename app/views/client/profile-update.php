<?php

// Get data from extracted variables instead of $data array
$client = $client ?? [];
$errors = $errors ?? [];
$old = $old ?? $client;

$current_page = 'profile';

// Safely get data with defaults
$firstName = $old['first_name'] ?? $client['first_name'] ?? '';
$lastName = $old['last_name'] ?? $client['last_name'] ?? '';
$email = $old['email'] ?? $client['email'] ?? '';
$phone = $old['phone'] ?? $client['phone'] ?? '';
$address = $old['address'] ?? $client['address'] ?? '';
$emergencyContact = $old['emergency_contact'] ?? $client['emergency_contact'] ?? '';
$preferredContactMethod = $old['preferred_contact_method'] ?? $client['preferred_contact_method'] ?? '';
$notes = $old['notes'] ?? $client['notes'] ?? '';

// Debug: Check what data is available
error_log("=== PROFILE UPDATE VIEW DEBUG ===");
error_log("Client data: " . print_r($client, true));
error_log("Old data: " . print_r($old, true));
error_log("Errors: " . print_r($errors, true));
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </h4>
                    <a href="<?php echo url('/client/profile'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Profile
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

                    <!-- Debug info - remove in production -->
                    <?php if (DEBUG_MODE && empty($client)): ?>
                        <div class="alert alert-warning">
                            <strong>Debug:</strong> No client data found. Make sure the controller is setting the data correctly.
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo url('/client/profile/update'); ?>" method="POST" id="profileForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="<?php echo htmlspecialchars($firstName); ?>" 
                                           required>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="<?php echo htmlspecialchars($lastName); ?>" 
                                           required>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" 
                                           disabled>
                                    <small class="form-text text-muted">Email cannot be changed. Contact support if needed.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?php echo htmlspecialchars($phone); ?>" 
                                           required 
                                           placeholder="+265 XXX XXX XXX">
                                    <?php if (isset($errors['phone'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      placeholder="Enter your full address"><?php echo htmlspecialchars($address); ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['address']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="emergency_contact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control <?php echo isset($errors['emergency_contact']) ? 'is-invalid' : ''; ?>" 
                                           id="emergency_contact" 
                                           name="emergency_contact" 
                                           value="<?php echo htmlspecialchars($emergencyContact); ?>" 
                                           required 
                                           placeholder="+265 XXX XXX XXX">
                                    <small class="form-text text-muted">Phone number of emergency contact person</small>
                                    <?php if (isset($errors['emergency_contact'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['emergency_contact']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="preferred_contact_method" class="form-label">Preferred Contact Method <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['preferred_contact_method']) ? 'is-invalid' : ''; ?>" 
                                            id="preferred_contact_method" 
                                            name="preferred_contact_method" 
                                            required>
                                        <option value="">Select Method</option>
                                        <option value="phone" <?php echo ($preferredContactMethod == 'phone') ? 'selected' : ''; ?>>Phone Call</option>
                                        <option value="email" <?php echo ($preferredContactMethod == 'email') ? 'selected' : ''; ?>>Email</option>
                                        <option value="sms" <?php echo ($preferredContactMethod == 'sms') ? 'selected' : ''; ?>>SMS</option>
                                    </select>
                                    <?php if (isset($errors['preferred_contact_method'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['preferred_contact_method']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control <?php echo isset($errors['notes']) ? 'is-invalid' : ''; ?>" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      placeholder="Any special instructions, allergies, or important information..."><?php echo htmlspecialchars($notes); ?></textarea>
                            <small class="form-text text-muted">This information helps us provide better care for your animals.</small>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['notes']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/client/profile'); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Update Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-exclamation-circle me-2"></i>Important Notes:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Fields marked with <span class="text-danger">*</span> are required</li>
                            <li>Keep your emergency contact updated</li>
                            <li>Choose your preferred contact method for reminders</li>
                            <li>Provide accurate address for home visits</li>
                        </ul>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Need Help?</h6>
                        <p class="small text-muted mb-2">
                            If you need to change your email address or have other account issues, please contact our support team.
                        </p>
                        <a href="mailto:support@veterinary-system.com" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-label {
    font-weight: 600;
    color: #134d60;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    
    form.addEventListener('submit', function(e) {
        // Client-side validation
        const phone = document.getElementById('phone').value;
        const emergencyContact = document.getElementById('emergency_contact').value;
        
        // Basic phone validation (Malawi format)
        const phoneRegex = /^(\+265|265|0)[1-9]\d{7,8}$/;
        
        if (phone && !phoneRegex.test(phone.replace(/\s/g, ''))) {
            e.preventDefault();
            alert('Please enter a valid Malawi phone number (e.g., +265 XXX XXX XXX)');
            document.getElementById('phone').focus();
            return;
        }
        
        if (emergencyContact && !phoneRegex.test(emergencyContact.replace(/\s/g, ''))) {
            e.preventDefault();
            alert('Please enter a valid emergency contact number (e.g., +265 XXX XXX XXX)');
            document.getElementById('emergency_contact').focus();
            return;
        }
    });
    
    // Format phone numbers as user types
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.startsWith('265')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+265' + value.substring(1);
            } else if (!value.startsWith('+')) {
                value = '+265' + value;
            }
            
            // Format with spaces: +265 XXX XXX XXX
            if (value.length > 4) {
                value = value.substring(0, 4) + ' ' + value.substring(4);
            }
            if (value.length > 8) {
                value = value.substring(0, 8) + ' ' + value.substring(8);
            }
            if (value.length > 12) {
                value = value.substring(0, 12) + ' ' + value.substring(12);
            }
            
            e.target.value = value;
        });
    });
});
</script>