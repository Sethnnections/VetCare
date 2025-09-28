<?php
// app/views/partials/modals/base-modal.php
// Reusable Bootstrap 5 modal component

$modalId = $modalId ?? 'baseModal';
$modalTitle = $modalTitle ?? 'Modal Title';
$modalSize = $modalSize ?? ''; // '', 'modal-lg', 'modal-xl', 'modal-sm'
$modalClass = $modalClass ?? '';
$footerButtons = $footerButtons ?? null;
$showCloseButton = $showCloseButton ?? true;
$backdrop = $backdrop ?? 'true'; // 'true', 'false', 'static'
$keyboard = $keyboard ?? 'true';
?>

<div class="modal fade <?= $modalClass ?>" id="<?= $modalId ?>" tabindex="-1" 
     data-bs-backdrop="<?= $backdrop ?>" data-bs-keyboard="<?= $keyboard ?>" 
     aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
    <div class="modal-dialog <?= $modalSize ?>">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="<?= $modalId ?>Label">
                    <?= $modalTitle ?>
                </h5>
                <?php if ($showCloseButton): ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <?php endif; ?>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <?php if (isset($modalContent)): ?>
                    <?= $modalContent ?>
                <?php else: ?>
                    <div class="modal-body-content">
                        <!-- Content will be loaded here -->
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <?php if ($footerButtons): ?>
                    <?= $footerButtons ?>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary" id="<?= $modalId ?>SaveBtn">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// User Modal Component
// app/views/partials/modals/user-modal.php
?>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="userForm" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="user_id" id="userId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userName" class="form-label">
                                    Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="userName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="userEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userPhone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="userPhone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userRole" class="form-label">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="<?= ROLE_ADMIN ?>">Administrator</option>
                                    <option value="<?= ROLE_VETERINARY ?>">Veterinary Officer</option>
                                    <option value="<?= ROLE_CLIENT ?>">Client</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="passwordFields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" id="userPassword" name="password" required>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Minimum <?= PASSWORD_MIN_LENGTH ?> characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userPasswordConfirm" class="form-label">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" id="userPasswordConfirm" name="password_confirm" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userStatus" class="form-label">Status</label>
                                <select class="form-select" id="userStatus" name="status">
                                    <option value="<?= STATUS_ACTIVE ?>">Active</option>
                                    <option value="<?= STATUS_INACTIVE ?>">Inactive