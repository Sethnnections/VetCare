<?php
$current_page = 'client_animals_edit';
$title = $title ?? 'Edit Animal: ' . htmlspecialchars($animal['name']);
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= $title ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/client/animals') ?>">My Animals</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/client/animals/' . $animal['animal_id']) ?>"><?= htmlspecialchars($animal['name']) ?></a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Animal Information</h4>
            </div>
            <div class="card-body">
                <form action="<?= url('/client/animals/' . $animal['animal_id'] . '/edit') ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <!-- Flash Messages -->
                    <?php if ($flash = getFlashMessage()): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $flash['message'] ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Current Photo Preview -->
                    <?php if (!empty($animal['photo'])): ?>
                        <div class="form-group">
                            <label>Current Photo</label>
                            <div>
                                <img src="<?= url('/uploads/animals/' . $animal['photo']) ?>" 
                                     alt="<?= htmlspecialchars($animal['name']) ?>" 
                                     class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Animal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" name="name" value="<?= $animal['name'] ?>" required>
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
                                    <option value="dog" <?= $animal['species'] == 'dog' ? 'selected' : '' ?>>Dog</option>
                                    <option value="cat" <?= $animal['species'] == 'cat' ? 'selected' : '' ?>>Cat</option>
                                    <option value="bird" <?= $animal['species'] == 'bird' ? 'selected' : '' ?>>Bird</option>
                                    <option value="rabbit" <?= $animal['species'] == 'rabbit' ? 'selected' : '' ?>>Rabbit</option>
                                    <option value="horse" <?= $animal['species'] == 'horse' ? 'selected' : '' ?>>Horse</option>
                                    <option value="other" <?= $animal['species'] == 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <?php if (isset($errors['species'])): ?>
                                    <div class="invalid-feedback"><?= $errors['species'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ... rest of form fields similar to create.php but with current values ... -->

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo">
                            <label class="form-check-label" for="remove_photo">
                                Remove current photo
                            </label>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="<?= url('/client/animals/' . $animal['animal_id']) ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Animal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>