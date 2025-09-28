<?php
$stats = $data['stats'];
$recentTreatments = $data['recentTreatments'];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Admin Dashboard</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['users']['total'] ?>
                            </div>
                            <div class="mt-2 text-muted">
                                <small>
                                    <?= $stats['users']['admin'] ?> Admin, 
                                    <?= $stats['users']['veterinary'] ?> Veterinary, 
                                    <?= $stats['users']['client'] ?> Client
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['clients']['total'] ?>
                            </div>
                            <div class="mt-2 text-muted">
                                <small>
                                    <?= $stats['clients']['active'] ?> Active, 
                                    <?= $stats['clients']['inactive'] ?> Inactive
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animals Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Animals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['animals']['total'] ?>
                            </div>
                            <div class="mt-2 text-muted">
                                <small>
                                    <?= $stats['animals']['active'] ?> Active, 
                                    <?= $stats['animals']['inactive'] ?> Inactive
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paw fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Treatments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Treatments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $stats['treatments']['total'] ?>
                            </div>
                            <div class="mt-2 text-muted">
                                <small>
                                    <?= $stats['treatments']['this_month'] ?> This Month
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Treatments -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Treatments (Last 7 Days)</h6>
                    <a href="<?= url('veterinary/treatments') ?>" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTreatments)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Animal</th>
                                        <th>Client</th>
                                        <th>Diagnosis</th>
                                        <th>Veterinary</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentTreatments as $treatment): ?>
                                        <tr>
                                            <td><?= formatDate($treatment['treatment_date']) ?></td>
                                            <td><?= $treatment['animal_name'] ?> (<?= $treatment['species'] ?>)</td>
                                            <td><?= $treatment['client_name'] ?></td>
                                            <td><?= str_limit($treatment['diagnosis'], 50) ?></td>
                                            <td><?= $treatment['veterinary_name'] ?></td>
                                            <td><?= formatCurrency($treatment['cost']) ?></td>
                                            <td>
                                                <?php 
                                                $badgeClass = '';
                                                switch ($treatment['status']) {
                                                    case 'completed': $badgeClass = 'success'; break;
                                                    case 'ongoing': $badgeClass = 'warning'; break;
                                                    case 'follow_up': $badgeClass = 'info'; break;
                                                    default: $badgeClass = 'secondary';
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= ucfirst($treatment['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No treatments found in the last 7 days</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= url('auth/register') ?>" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Add User
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= url('admin/clients') ?>" class="btn btn-success w-100">
                                <i class="fas fa-user-friends me-2"></i>Manage Clients
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= url('admin/medicines') ?>" class="btn btn-info w-100">
                                <i class="fas fa-pills me-2"></i>Manage Medicines
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= url('admin/reports') ?>" class="btn btn-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>