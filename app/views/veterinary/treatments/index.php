<?php
$current_page = 'veterinary_treatments';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">My Treatments</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/veterinary/dashboard'); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Treatments</li>
                    </ol>
                </nav>
            </div>
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

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="quick-actions">
                <a href="<?php echo url('/treatments/follow-ups'); ?>" class="action-btn">
                    <i class="fas fa-bell text-warning"></i>
                    <span>Follow-ups</span>
                    <small class="text-muted">Manage scheduled follow-ups</small>
                </a>
                <a href="<?php echo url('/veterinary/animals'); ?>" class="action-btn">
                    <i class="fas fa-paw text-primary"></i>
                    <span>My Animals</span>
                    <small class="text-muted">View assigned animals</small>
                </a>
                <a href="<?php echo url('/appointments/today'); ?>" class="action-btn">
                    <i class="fas fa-calendar-day text-success"></i>
                    <span>Today's Appointments</span>
                    <small class="text-muted">View today's schedule</small>
                </a>
            </div>
        </div>
    </div>

    <!-- Treatment List -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Treatment Records
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search treatments..." value="<?php echo $search ?? ''; ?>">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <a href="<?php echo url('/treatments/follow-ups'); ?>" class="btn btn-outline-warning">
                                    <i class="fas fa-bell me-2"></i>Follow-ups
                                </a>
                                <a href="<?php echo url('/treatments?status=ongoing'); ?>" class="btn btn-outline-info">
                                    <i class="fas fa-clock me-2"></i>Ongoing
                                </a>
                                <a href="<?php echo url('/treatments?status=completed'); ?>" class="btn btn-outline-success">
                                    <i class="fas fa-check me-2"></i>Completed
                                </a>
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
                            <p class="text-muted">You haven't created any treatments yet.</p>
                            <a href="<?php echo url('/treatments/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Treatment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Animal & Owner</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment Date</th>
                                        <th>Follow-up</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treatments as $treatment): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $treatment['animal_name']; ?></strong>
                                            <br><small class="text-muted">Owner: <?php echo $treatment['client_full_name']; ?></small>
                                        </td>
                                        <td>
                                            <span title="<?php echo $treatment['diagnosis']; ?>">
                                                <?php echo strLimit($treatment['diagnosis'], 50); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                        <td>
                                            <?php if ($treatment['follow_up_date']): ?>
                                                <span class="badge bg-<?php 
                                                    echo $treatment['follow_up_status'] == 'overdue' ? 'danger' : 'warning'; 
                                                ?>">
                                                    <?php echo formatDate($treatment['follow_up_date']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
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
                                        <td>MK<?php echo number_format($treatment['cost'], 2); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id']); ?>" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/edit'); ?>" 
                                                   class="btn btn-outline-secondary" title="Edit Treatment">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($treatment['treatment_status'] != 'completed'): ?>
                                                    <form method="POST" action="<?php echo url('/treatments/' . $treatment['treatment_id'] . '/complete'); ?>" 
                                                          class="d-inline" onsubmit="return confirm('Mark this treatment as completed?')">
                                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                        <button type="submit" class="btn btn-outline-success" title="Mark Complete">
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
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo url('/treatments?page=' . $i . ($search ? '&search=' . urlencode($search) : '') . ($status ? '&status=' . $status : '')); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>