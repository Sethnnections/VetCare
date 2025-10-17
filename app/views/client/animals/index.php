<?php
$current_page = 'client_animals';
$title = $title ?? 'My Animals';
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= $title ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">My Animals</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <!-- Flash Messages -->
        <?php if ($flash = getFlashMessage()): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Animals Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-primary">
                                <i class="fas fa-paw"></i>
                            </span>
                            <div class="dash-count">
                                <h3><?= $stats['total'] ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Animals</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-success">
                                <i class="fas fa-heartbeat"></i>
                            </span>
                            <div class="dash-count">
                                <h3><?= $stats['active'] ?? 0 ?></h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Active Animals</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Animal Button -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">My Pets</h3>
                </div>
                <div class="col-auto">
                    <a href="<?= url('/client/animals/add') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Animal
                    </a>
                </div>
            </div>
        </div>

        <!-- Animals List -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($animals)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-paw"></i>
                        </div>
                        <h2>No Animals Found</h2>
                        <p class="empty-state-desc">
                            You haven't added any animals yet. Start by adding your first pet.
                        </p>
                        <a href="<?= url('/client/animals/add') ?>" class="btn btn-primary mt-4">
                            <i class="fas fa-plus"></i> Add Your First Animal
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Species/Breed</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Weight</th>
                                    <th>Last Treatment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($animals as $animal): ?>
                                    <tr>
                                        <td>
                                            <div class="avatar-sm">
                                                <?php if (!empty($animal['photo'])): ?>
                                                    <img class="avatar-img rounded-circle" 
                                                         src="<?= url('/uploads/animals/' . $animal['photo']) ?>" 
                                                         alt="<?= htmlspecialchars($animal['name']) ?>">
                                                <?php else: ?>
                                                    <div class="avatar-img rounded-circle bg-light text-center">
                                                        <i class="fas fa-paw fa-2x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="<?= url('/client/animals/' . $animal['animal_id']) ?>">
                                                    <?= htmlspecialchars($animal['name']) ?>
                                                </a>
                                            </h2>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($animal['species']) ?>
                                            <?php if (!empty($animal['breed'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($animal['breed']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= calculateAge($animal['birth_date']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $animal['gender'] == 'male' ? 'info' : 'warning' ?>">
                                                <?= ucfirst($animal['gender']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($animal['weight'])): ?>
                                                <?= $animal['weight'] ?> kg
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($animal['last_treatment'])): ?>
                                                <?= formatDate($animal['last_treatment']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No treatments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a href="<?= url('/client/animals/' . $animal['animal_id']) ?>" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= url('/client/animals/' . $animal['animal_id'] . '/edit') ?>" 
                                                   class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="<?= url('/client/animals/' . $animal['animal_id'] . '/delete') ?>" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this animal?')">
                                                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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