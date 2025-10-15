<?php include 'app/views/layouts/header.php'; ?>
<?php include 'app/views/layouts/sidebar.php'; ?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">My Animals</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo Router::url('/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">My Animals</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">My Pets</h4>
                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addAnimalModal">
                    <i class="fas fa-plus"></i> Add New Animal
                </button>
            </div>
            <div class="card-body">
                <?php 
                // Using getFlashMessage() helper
                $flash = getFlashMessage();
                if ($flash): 
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo sanitize($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped" id="animalsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Species</th>
                                <th>Breed</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Weight</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($animals as $animal): ?>
                            <tr>
                                <td>
                                    <strong><?php echo sanitize($animal['name']); ?></strong>
                                    <?php if ($animal['microchip']): ?>
                                        <br><small class="text-muted">Chip: <?php echo sanitize($animal['microchip']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo sanitize(ucfirst($animal['species'])); ?></td>
                                <td><?php echo sanitize($animal['breed'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $animal['gender'] == 'male' ? 'primary' : ($animal['gender'] == 'female' ? 'danger' : 'secondary'); ?>">
                                        <?php echo sanitize(ucfirst($animal['gender'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    // Using calculateAge() helper
                                    echo calculateAge($animal['birth_date']) ?? 'Unknown';
                                    ?>
                                </td>
                                <td><?php echo $animal['weight'] ? $animal['weight'] . ' kg' : 'N/A'; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $animal['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo sanitize(ucfirst($animal['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo Router::url('/client/animals/' . $animal['animal_id']); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning edit-animal" 
                                                data-animal-id="<?php echo $animal['animal_id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-animal" 
                                                data-animal-id="<?php echo $animal['animal_id']; ?>" 
                                                data-animal-name="<?php echo sanitize($animal['name']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Animal Modal -->
<div class="modal fade" id="addAnimalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Animal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addAnimalForm">
                <div class="modal-body">
                    <!-- Using generateCsrfToken() helper -->
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Animal Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <!-- ... rest of form ... -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>