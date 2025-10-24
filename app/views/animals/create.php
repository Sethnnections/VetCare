<?php
$errors = $errors ?? [];
$old = $old ?? [];
$clients = $clients ?? [];
$current_page = 'animals_create';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Add New Animal
                    </h4>
                    <a href="<?php echo url('/animals'); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Animals
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

                    <form action="<?php echo url('/animals'); ?>" method="POST" id="animalForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <!-- Client selection (admin only) -->
                        <?php if (getCurrentUserRole() === ROLE_ADMIN): ?>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id" class="form-label">Owner <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['client_id']) ? 'is-invalid' : ''; ?>" 
                                            id="client_id" 
                                            name="client_id" 
                                            required>
                                        <option value="">Select Owner</option>
                                        <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['client_id']; ?>" 
                                                <?php echo ($old['client_id'] ?? '') == $client['client_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name'] . ' (' . $client['email'] . ')'); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['client_id'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['client_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Animal Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" 
                                           required 
                                           placeholder="Enter animal name">
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="species" class="form-label">Species <span class="text-danger">*</span></label>
                                    <select class="form-control <?php echo isset($errors['species']) ? 'is-invalid' : ''; ?>" 
                                            id="species" 
                                            name="species" 
                                            required>
                                        <option value="">Select Species</option>
                                        <option value="dog" <?php echo ($old['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                        <option value="cat" <?php echo ($old['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                        <option value="bird" <?php echo ($old['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                        <option value="rabbit" <?php echo ($old['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                        <option value="horse" <?php echo ($old['species'] ?? '') == 'horse' ? 'selected' : ''; ?>>Horse</option>
                                        <option value="other" <?php echo ($old['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['species'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['species']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Rest of the form fields (same as edit.php) -->
                        <!-- ... include the same form fields as in edit.php ... -->

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/animals'); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Add Animal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Same validation script as edit.php
</script>