<?php
$current_page = 'vaccinations_index';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            
            <div class="col-auto">
                <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Record Vaccination
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-success rounded-circle">
                                        <i class="fas fa-syringe text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0"><?php echo $stats['total_vaccinations'] ?? 0; ?></h5>
                                    <p class="text-muted mb-0">Total Vaccinations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-warning rounded-circle">
                                        <i class="fas fa-clock text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0"><?php echo $stats['scheduled'] ?? 0; ?></h5>
                                    <p class="text-muted mb-0">Scheduled</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-danger rounded-circle">
                                        <i class="fas fa-exclamation-triangle text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0"><?php echo $stats['overdue'] ?? 0; ?></h5>
                                    <p class="text-muted mb-0">Overdue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-info rounded-circle">
                                        <i class="fas fa-calendar text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0"><?php echo $stats['due_soon'] ?? 0; ?></h5>
                                    <p class="text-muted mb-0">Due Soon</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo $search ?? ''; ?>" placeholder="Search by vaccine, animal, or client...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="completed" <?php echo ($status ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="scheduled" <?php echo ($status ?? '') == 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="overdue" <?php echo ($status ?? '') == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?php echo $date_from ?? ''; ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="<?php echo url('/admin/vaccinations'); ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vaccinations Table -->
            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Vaccination Records
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($vaccinations)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                            <h5>No vaccination records found</h5>
                            <p class="text-muted">Get started by recording a new vaccination.</p>
                            <a href="<?php echo url('/vaccinations/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Record Vaccination
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vaccine</th>
                                        <th>Animal</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Next Due</th>
                                        <th>Status</th>
                                        <th>Veterinary</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vaccinations as $vaccination): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-3 flex-shrink-0">
                                                    <i class="fas fa-syringe text-white"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $vaccination['vaccine_name']; ?></strong>
                                                    <?php if ($vaccination['vaccine_type']): ?>
                                                    <br><small class="text-muted"><?php echo $vaccination['vaccine_type']; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo $vaccination['animal_name']; ?></strong>
                                            <br><small class="text-muted"><?php echo $vaccination['species']; ?></small>
                                        </td>
                                        <td><?php echo $vaccination['client_full_name'] ?? 'N/A'; ?></td>
                                        <td><?php echo formatDate($vaccination['vaccine_date']); ?></td>
                                        <td>
                                            <span class="<?php echo strtotime($vaccination['next_due_date']) < time() ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo formatDate($vaccination['next_due_date']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'completed' => 'success',
                                                'scheduled' => 'warning',
                                                'overdue' => 'danger',
                                                'verified' => 'info'
                                            ];
                                            $status = $vaccination['vaccine_status'] ?? $vaccination['status'];
                                            $badgeClass = $statusBadge[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $vaccination['administered_by_name'] ?? 'N/A'; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/edit'); ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/certificate'); ?>" 
                                                   class="btn btn-sm btn-outline-success" title="Certificate" target="_blank">
                                                    <i class="fas fa-certificate"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $pagination['prev_url']; ?>">Previous</a>
                                </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $pagination['base_url'] . '&page=' . $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $pagination['next_url']; ?>">Next</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const statusSelect = document.getElementById('status');
    const dateFromInput = document.getElementById('date_from');
    
    [statusSelect, dateFromInput].forEach(element => {
        if (element) {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>