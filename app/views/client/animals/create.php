<?php
$current_page = 'client_animals_add';
$title = $title ?? 'Add New Animal';
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= $title ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/client/animals') ?>">My Animals</a></li>
                <li class="breadcrumb-item active">Add Animal</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Animal Information</h4>
            </div>
            <div class="card-body">
                <form action="<?= url('/client/animals/add') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <!-- Flash Messages -->
                    <?php if ($flash = getFlashMessage()): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $flash['message'] ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Animal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= $old['name'] ?? '' ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="species">Species <span class="text-danger">*</span></label>
                                <select class="form-control <?= isset($errors['species']) ? 'is-invalid' : '' ?>" 
                                        id="species" name="species" required>
                                    <option value="">Select Species</option>
                                    <option value="dog" <?= ($old['species'] ?? '') == 'dog' ? 'selected' : '' ?>>Dog</option>
                                    <option value="cat" <?= ($old['species'] ?? '') == 'cat' ? 'selected' : '' ?>>Cat</option>
                                    <option value="bird" <?= ($old['species'] ?? '') == 'bird' ? 'selected' : '' ?>>Bird</option>
                                    <option value="rabbit" <?= ($old['species'] ?? '') == 'rabbit' ? 'selected' : '' ?>>Rabbit</option>
                                    <option value="horse" <?= ($old['species'] ?? '') == 'horse' ? 'selected' : '' ?>>Horse</option>
                                    <option value="other" <?= ($old['species'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <?php if (isset($errors['species'])): ?>
                                    <div class="invalid-feedback"><?= $errors['species'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="breed">Breed</label>
                                <input type="text" class="form-control <?= isset($errors['breed']) ? 'is-invalid' : '' ?>" 
                                       id="breed" name="breed" value="<?= $old['breed'] ?? '' ?>">
                                <?php if (isset($errors['breed'])): ?>
                                    <div class="invalid-feedback"><?= $errors['breed'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                <select class="form-control <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" 
                                        id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" <?= ($old['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= ($old['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="unknown" <?= ($old['gender'] ?? '') == 'unknown' ? 'selected' : '' ?>>Unknown</option>
                                </select>
                                <?php if (isset($errors['gender'])): ?>
                                    <div class="invalid-feedback"><?= $errors['gender'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="birth_date">Birth Date</label>
                                <input type="date" class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>" 
                                       id="birth_date" name="birth_date" value="<?= $old['birth_date'] ?? '' ?>">
                                <?php if (isset($errors['birth_date'])): ?>
                                    <div class="invalid-feedback"><?= $errors['birth_date'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="weight">Weight (kg)</label>
                                <input type="number" step="0.1" class="form-control <?= isset($errors['weight']) ? 'is-invalid' : '' ?>" 
                                       id="weight" name="weight" value="<?= $old['weight'] ?? '' ?>" placeholder="e.g., 25.5">
                                <?php if (isset($errors['weight'])): ?>
                                    <div class="invalid-feedback"><?= $errors['weight'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="color">Color/Markings</label>
                                <input type="text" class="form-control <?= isset($errors['color']) ? 'is-invalid' : '' ?>" 
                                       id="color" name="color" value="<?= $old['color'] ?? '' ?>">
                                <?php if (isset($errors['color'])): ?>
                                    <div class="invalid-feedback"><?= $errors['color'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microchip">Microchip Number</label>
                                <input type="text" class="form-control <?= isset($errors['microchip']) ? 'is-invalid' : '' ?>" 
                                       id="microchip" name="microchip" value="<?= $old['microchip'] ?? '' ?>">
                                <?php if (isset($errors['microchip'])): ?>
                                    <div class="invalid-feedback"><?= $errors['microchip'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo">Animal Photo</label>
                        <input type="file" class="form-control-file <?= isset($errors['photo']) ? 'is-invalid' : '' ?>" 
                               id="photo" name="photo" accept="image/*">
                        <small class="form-text text-muted">
                            Supported formats: JPG, PNG, GIF. Max size: 5MB
                        </small>
                        <?php if (isset($errors['photo'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['photo'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="notes">Special Notes/Medical History</label>
                        <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                  id="notes" name="notes" rows="4" placeholder="Any special care instructions, allergies, or medical history..."><?= $old['notes'] ?? '' ?></textarea>
                        <?php if (isset($errors['notes'])): ?>
                            <div class="invalid-feedback"><?= $errors['notes'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="text-right">
                        <a href="<?= url('/client/animals') ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Animal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>