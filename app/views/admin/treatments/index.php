<?php
$current_page = 'admin_treatments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
            <div class="col-auto">
                <a href="<?php echo url('/treatments/create'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Treatment
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid mb-4">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div>
                <div class="stat-value"><?php echo $stats['total_treatments'] ?? 0; ?></div>
                <div class="stat-label">Total Treatments</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value"><?php echo $stats['completed_treatments'] ?? 0; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="stat-value"><?php echo $stats['ongoing_treatments'] ?? 0; ?></div>
                <div class="stat-label">Ongoing</div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value"><?php echo $stats['follow_up_treatments'] ?? 0; ?></div>
                <div class="stat-label">Follow-ups</div>
            </div>
        </div>
    </div>

    <!-- Treatment List -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>All Treatments
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search treatments..." value="<?php echo $search ?? ''; ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="ongoing" <?php echo ($status ?? '') == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="completed" <?php echo ($status ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="follow_up" <?php echo ($status ?? '') == 'follow_up' ? 'selected' : ''; ?>>Follow-up</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="<?php echo url('/treatments'); ?>" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="<?php echo url('/treatments/follow-ups'); ?>" class="btn btn-outline-warning">
                                    <i class="fas fa-bell me-2"></i>Follow-ups
                                </a>
                                <button class="btn btn-outline-info" onclick="exportTreatments()">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $flash = getFlashMessage();
                    if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($treatments)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                            <h5>No treatments found</h5>
                            <p class="text-muted">No treatments match your search criteria.</p>
                            <a href="<?php echo url('/treatments/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Treatment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Animal & Owner</th>
                                        <th>Veterinary</th>
                                        <th>Diagnosis</th>
                                        <th>Date</th>
                                        <th>Follow-up</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treatments as $treatment): ?>
                                    <tr>
                                        <td>#<?php echo $treatment['treatment_id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-paw text-white"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $treatment['animal_name']; ?></strong>
                                                    <br><small class="text-muted"><?php echo $treatment['client_full_name']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $treatment['vet_full_name']; ?></td>
                                        <td>
                                            <span title="<?php echo $treatment['diagnosis']; ?>">
                                                <?php echo strLimit($treatment['diagnosis'], 40); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                        <td>
                                            <?php if ($treatment['follow_up_date']): ?>
                                                <span class="badge bg-<?php 
                                                    echo $treatment['follow_up_status'] == 'overdue' ? 'danger' : 
                                                         ($treatment['follow_up_status'] == 'pending' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo formatDate($treatment['follow_up_date']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $treatment['treatment_status'] == 'completed' ? 'success' : 
                                                     ($treatment['treatment_status'] == 'ongoing' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($treatment['treatment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">MK<?php echo number_format($treatment['cost'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/edit'); ?>" 
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($treatment['treatment_status'] != 'completed'): ?>
                                                    <form method="POST" action="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/complete'); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Mark this treatment as completed?')">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                        <button type="submit" class="btn btn-outline-success" title="Complete">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                        <nav aria-label="Treatment pagination">
                            <ul class="pagination justify-content-center mt-4">
                                <li class="page-item <?php echo $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo url('/treatments?page=' . ($pagination['current_page'] - 1) . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')); ?>">
                                        Previous
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <?php if ($i == 1 || $i == $pagination['total_pages'] || ($i >= $pagination['current_page'] - 2 && $i <= $pagination['current_page'] + 2)): ?>
                                        <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo url('/treatments?page=' . $i . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php elseif ($i == $pagination['current_page'] - 3 || $i == $pagination['current_page'] + 3): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo url('/treatments?page=' . ($pagination['current_page'] + 1) . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')); ?>">
                                        Next
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center text-muted">
                                Showing <?php echo count($treatments); ?> of <?php echo $pagination['total']; ?> treatments
                            </div>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportTreatments() {
    const search = '<?php echo $search ?? ''; ?>';
    const status = '<?php echo $status ?? ''; ?>';
    const url = `<?php echo url('/api/treatments/export'); ?>?search=${encodeURIComponent(search)}&status=${status}`;
    window.open(url, '_blank');
}
</script>