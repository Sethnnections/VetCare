<?php
$animals = $animals ?? [];
$search = $search ?? '';
$species = $species ?? '';
$stats = $stats ?? [];
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
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-dog"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo count($stats['by_species'] ?? []); ?></div>
                                <div class="stat-label">Species</div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="<?php echo url('/animals'); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Search Animals</label>
                                            <input type="text" class="form-control" name="search" 
                                                   value="<?php echo htmlspecialchars($search); ?>" 
                                                   placeholder="Search by name, species, breed...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Species</label>
                                            <select class="form-control" name="species">
                                                <option value="">All Species</option>
                                                <?php if (!empty($stats['by_species'])): ?>
                                                    <?php foreach ($stats['by_species'] as $speciesItem): ?>
                                                    <option value="<?php echo htmlspecialchars($speciesItem['species']); ?>" 
                                                        <?php echo $species === $speciesItem['species'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($speciesItem['species']); ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Animals Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Animals List</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($animals)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No animals found</p>
                                    <a href="<?php echo url('/animals/create'); ?>" class="btn btn-primary">Add First Animal</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Species/Breed</th>
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
                                                    <div class="d-flex align-items-center">
                                                        <div class="animal-avatar me-3">
                                                            <i class="fas fa-paw"></i>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($animal['name']); ?></strong>
                                                            <?php if ($animal['microchip']): ?>
                                                                <br>
                                                                <small class="text-muted">Chip: <?php echo htmlspecialchars($animal['microchip']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($animal['species']); ?></strong>
                                                    <?php if ($animal['breed']): ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($animal['breed']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $clientName = 'Unknown';
                                                    if (isset($animal['client_name'])) {
                                                        $clientName = $animal['client_name'];
                                                    } elseif (isset($animal['client_first_name'])) {
                                                        $clientName = $animal['client_first_name'] . ' ' . $animal['client_last_name'];
                                                    }
                                                    echo htmlspecialchars($clientName);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($animal['birth_date']): ?>
                                                        <?php echo calculateAge($animal['birth_date']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Unknown</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $animal['status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                        <?php echo ucfirst($animal['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo url('/animals/' . $animal['animal_id']); ?>" 
                                                           class="btn btn-outline-primary" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/edit'); ?>" 
                                                           class="btn btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($animal['status'] == 'active'): ?>
                                                            <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" 
                                                               class="btn btn-outline-success" title="Add Treatment">
                                                                <i class="fas fa-stethoscope"></i>
                                                            </a>
                                                        <?php endif; ?>
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
    </div>
</div>

<style>
.animal-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
</style>