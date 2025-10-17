<?php

// Get client data directly from the extracted variables
$client = $client ?? []; // Use the extracted variable instead of $data['client']
$current_page = 'profile';

// Debug: Check what variables are available
error_log("=== PROFILE VIEW DEBUG ===");
error_log("Available variables: " . print_r(get_defined_vars(), true));

// Safely get client data with defaults
$firstName = $client['first_name'] ?? 'Not set';
$lastName = $client['last_name'] ?? 'Not set';
$email = $client['email'] ?? 'Not set';
$phone = $client['phone'] ?? 'Not set';
$emergencyContact = $client['emergency_contact'] ?? 'Not set';
$preferredContactMethod = $client['preferred_contact_method'] ?? 'Not set';
$address = $client['address'] ?? 'Not set';
$notes = $client['notes'] ?? 'No notes';

// Generate initials
$initials = '';
if (!empty($firstName) && $firstName !== 'Not set') {
    $initials = strtoupper(substr($firstName, 0, 1));
    if (!empty($lastName) && $lastName !== 'Not set') {
        $initials .= strtoupper(substr($lastName, 0, 1));
    }
} else {
    $initials = 'CL';
}

$fullName = trim($firstName . ' ' . $lastName);
if ($fullName === 'Not set Not set') {
    $fullName = 'Client Profile';
}
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-12">
            <div class="card border-0 shadow-sm profile-card">
                <!-- Card Header -->
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <p class="text-muted mb-0 small">Manage your personal information</p>
                        </div>
                        <a href="<?php echo url('/client/profile/edit'); ?>" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show shadow-sm border-0 mb-4">
                            <i class="fas fa-check-circle me-2"></i><?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Show message if no client data -->
                    <?php if (empty($client) || !isset($client['client_id'])): ?>
                        <div class="alert alert-warning border-0 shadow-sm">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="alert-heading  mb-2">Profile Not Complete</h6>
                                    <p class="mb-3">Your client profile is not set up yet. Please complete your profile to access all features and enhance your experience.</p>
                                    <a href="<?php echo url('/client/profile/create'); ?>" class="btn btn-warning shadow-sm">
                                        <i class="fas fa-plus-circle me-2"></i>Complete Your Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Profile content when data is available -->
                        <div class="row g-4">
                            <!-- Profile Avatar Section -->
                            <div class="col-lg-4">
                                <div class="text-center p-4 bg-light rounded-3">
                                    <div class="user-avatar-large mx-auto mb-3 shadow">
                                        <?php echo $initials; ?>
                                    </div>
                                    <h5 class=" mb-1"><?php echo htmlspecialchars($fullName); ?></h5>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                        <i class="fas fa-user me-1"></i>Client
                                    </span>
                                    <hr class="my-3">
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo url('/client/profile/edit'); ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit me-2"></i>Edit Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Details Section -->
                            <div class="col-lg-8">
                                <!-- Contact Information -->
                                <div class="mb-4">
                                    <h5 class=" mb-3 pb-2 border-bottom">
                                        <i class="fas fa-address-book me-2 text-primary"></i>Contact Information
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="info-item p-3 bg-light rounded-3 h-100">
                                                <div class="d-flex align-items-start">
                                                    <div class="icon-box me-3">
                                                        <i class="fas fa-envelope text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block mb-1">Email Address</small>
                                                        <strong class="text-break"><?php echo htmlspecialchars($email); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="info-item p-3 bg-light rounded-3 h-100">
                                                <div class="d-flex align-items-start">
                                                    <div class="icon-box me-3">
                                                        <i class="fas fa-phone text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block mb-1">Phone Number</small>
                                                        <strong><?php echo htmlspecialchars($phone); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="info-item p-3 bg-light rounded-3 h-100">
                                                <div class="d-flex align-items-start">
                                                    <div class="icon-box me-3">
                                                        <i class="fas fa-user-shield text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block mb-1">Emergency Contact</small>
                                                        <strong><?php echo htmlspecialchars($emergencyContact); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="info-item p-3 bg-light rounded-3 h-100">
                                                <div class="d-flex align-items-start">
                                                    <div class="icon-box me-3">
                                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block mb-1">Address</small>
                                                        <strong><?php echo htmlspecialchars($address); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Preferences -->
                                <div class="mb-4">
                                    <h5 class=" mb-3 pb-2 border-bottom">
                                        <i class="fas fa-cog me-2 text-primary"></i>Preferences
                                    </h5>
                                    <div class="info-item p-3 bg-light rounded-3">
                                        <div class="d-flex align-items-start">
                                            <div class="icon-box me-3">
                                                <i class="fas fa-comment-dots text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block mb-1">Preferred Contact Method</small>
                                                <strong>
                                                    <?php 
                                                    if (!empty($preferredContactMethod) && $preferredContactMethod !== 'Not set') {
                                                        echo ucfirst(htmlspecialchars($preferredContactMethod));
                                                    } else {
                                                        echo '<span class="text-muted">Not specified</span>';
                                                    }
                                                    ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Notes -->
                                <?php if (!empty($notes) && $notes !== 'No notes'): ?>
                                <div>
                                    <h5 class=" mb-3 pb-2 border-bottom">
                                        <i class="fas fa-sticky-note me-2 text-primary"></i>Additional Notes
                                    </h5>
                                    <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($notes)); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Profile Card Styling */
.profile-card {
    border-radius: 12px;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

/* User Avatar */
.user-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #234d60 0%, #2a5f75 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 2.5rem;
    letter-spacing: 2px;
    position: relative;
    transition: transform 0.3s ease;
}

.user-avatar-large:hover {
    transform: scale(1.05);
}

.user-avatar-large::before {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: linear-gradient(135deg, #234d60, #2a5f75, #234d60);
    z-index: -1;
    opacity: 0.3;
    animation: pulse 2s ease-in-out infinite;
}

/* Info Items */
.info-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: rgba(35, 77, 96, 0.1);
}

.icon-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.icon-box i {
    color: #234d60;
}

/* Text Color Override */
.text-primary {
    color: #234d60 !important;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.05);
    }
}

/* Button Enhancements */
.btn-primary {
    background: linear-gradient(135deg, #234d60 0%, #2a5f75 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(35, 77, 96, 0.3);
    background: linear-gradient(135deg, #2a5f75 0%, #234d60 100%);
}

.btn-outline-primary {
    border-color: #234d60;
    color: #234d60;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #234d60;
    border-color: #234d60;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(35, 77, 96, 0.2);
}

/* Badge Enhancement */
.badge {
    font-weight: 600;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}

.bg-primary-subtle {
    background-color: rgba(35, 77, 96, 0.1) !important;
}

.badge.bg-primary-subtle {
    color: #234d60 !important;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
}

.alert-warning {
    background: linear-gradient(135deg, #fff9e6 0%, #fff4d4 100%);
}

/* Border Styling */
.border-primary {
    border-color: #234d60 !important;
}

.border-start {
    border-left-width: 4px !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .user-avatar-large {
        width: 100px;
        height: 100px;
        font-size: 2rem;
    }
    
    .card-header h4 {
        font-size: 1.25rem;
    }
}

/* Border Bottom Accent */
.border-bottom {
    border-bottom: 2px solid #f0f0f0 !important;
}

/* Smooth Transitions */
* {
    transition: background-color 0.2s ease, color 0.2s ease;
}
</style>