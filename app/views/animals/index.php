<?php
$animals = $animals ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$species = $species ?? '';
$current_page = 'animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i>Animals
                    </h4>
                    <a href="<?php echo url('/animals/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add New Animal
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

                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search animals..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" class="d-flex">
                                <select name="species" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Species</option>
                                    <option value="dog" <?php echo $species == 'dog' ? 'selected' : ''; ?>>Dogs</option>
                                    <option value="cat" <?php echo $species == 'cat' ? 'selected' : ''; ?>>Cats</option>
                                    <option value="bird" <?php echo $species == 'bird' ? 'selected' : ''; ?>>Birds</option>
                                    <option value="rabbit" <?php echo $species == 'rabbit' ? 'selected' : ''; ?>>Rabbits</option>
                                    <option value="horse" <?php echo $species == 'horse' ? 'selected' : ''; ?>>Horses</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="stats-grid mb-4">
                        <div class="stat-card primary">
                            <div class="stat-icon">
                                <i class="fas fa-paw"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                                <div class="stat-label">Total Animals</div>
                            </div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
                                <div class="stat-label">Active Animals</div>
                            </div>
                        </div>
                        <div class="stat-card danger">
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['inactive'] ?? 0; ?></div>
                                <div class="stat-label">Inactive Animals</div>
                            </div>
                        </div>
                    </div>

                    <!-- Animals List -->
                    <?php if (empty($animals)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-paw fa-4x text-muted mb-3"></i>
                                <h4>No Animals Found</h4>
                                <p class="text-muted">
                                    <?php echo $search ? 'No animals match your search criteria.' : 'No animals have been registered yet.'; ?>
                                </p>
                                <a href="<?php echo url('/animals/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Add Your First Animal
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Species</th>
                                        <th>Breed</th>
                                        <th>Owner</th>
                                        <th>Age</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals as $animal): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo url('/animals/' . $animal['animal_id']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($animal['animal_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo ucfirst($animal['species']); ?></td>
                                        <td><?php echo htmlspecialchars($animal['breed'] ?? 'Mixed'); ?></td>
                                        <td><?php echo htmlspecialchars($animal['client_full_name'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <?php
                                            if (!empty($animal['birth_date'])) {
                                                echo calculateAge($animal['birth_date']);
                                            } else {
                                                echo 'Unknown';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $animal['animal_status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($animal['animal_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/animals/' . $animal['animal_id']); ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/edit'); ?>" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/medical-history'); ?>" class="btn btn-outline-info">
                                                    <i class="fas fa-file-medical"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>