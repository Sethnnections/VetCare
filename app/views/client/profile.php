<?php
// app/views/client/profile.php
$client = $data['client'] ?? [];
$current_page = 'profile';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>My Profile
                    </h4>
                    <a href="<?php echo url('/client/profile/edit'); ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($data['flash'])): ?>
                        <div class="alert alert-<?php echo $data['flash']['type']; ?>">
                            <?php echo $data['flash']['message']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="user-avatar-large mx-auto mb-3">
                                <?php
                                $initials = '';
                                if (!empty($client['first_name'])) {
                                    $initials = strtoupper(substr($client['first_name'], 0, 1));
                                    if (!empty($client['last_name'])) {
                                        $initials .= strtoupper(substr($client['last_name'], 0, 1));
                                    }
                                } else {
                                    $initials = strtoupper(substr($client['username'], 0, 2));
                                }
                                echo $initials;
                                ?>
                            </div>
                            <h5><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></h5>
                            <p class="text-muted">Client</p>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h6>Contact Information</h6>
                                    <p><strong>Email:</strong><br><?php echo htmlspecialchars($client['email']); ?></p>
                                    <p><strong>Phone:</strong><br><?php echo htmlspecialchars($client['phone'] ?? 'Not provided'); ?></p>
                                    <p><strong>Emergency Contact:</strong><br><?php echo htmlspecialchars($client['emergency_contact'] ?? 'Not provided'); ?></p>
                                </div>
                                
                                <div class="col-sm-6">
                                    <h6>Preferences</h6>
                                    <p><strong>Contact Method:</strong><br>
                                        <?php 
                                        $method = $client['preferred_contact_method'] ?? 'Not specified';
                                        echo ucfirst($method);
                                        ?>
                                    </p>
                                    <p><strong>Address:</strong><br><?php echo htmlspecialchars($client['address'] ?? 'Not provided'); ?></p>
                                </div>
                            </div>
                            
                            <?php if (!empty($client['notes'])): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Additional Notes</h6>
                                    <div class="bg-light p-3 rounded">
                                        <?php echo nl2br(htmlspecialchars($client['notes'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Profile Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Your profile is complete and active.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo url('/client/animals'); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-paw me-2"></i>My Animals
                        </a>
                        <a href="<?php echo url('/auth/change-password'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-2"></i>Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 2rem;
    margin: 0 auto;
}
</style>