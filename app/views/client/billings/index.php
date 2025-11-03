<?php
$billings = $billings ?? [];
$stats = $stats ?? [];
$current_page = 'client_billings';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>My Bills & Payments
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/client/billings?status=pending'); ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-clock me-1"></i>Pending
                        </a>
                        <a href="<?php echo url('/client/billings?status=paid'); ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-check me-1"></i>Paid
                        </a>
                        <a href="<?php echo url('/client/billings'); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>All
                        </a>
                    </div>
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
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['total_bills'] ?? 0; ?></div>
                                <div class="stat-label">Total Bills</div>
                            </div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['paid_bills'] ?? 0; ?></div>
                                <div class="stat-label">Paid Bills</div>
                            </div>
                        </div>
                        <div class="stat-card warning">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?php echo $stats['pending_bills'] ?? 0; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <div class="stat-value">MWK <?php echo number_format($stats['total_amount'] ?? 0, 2); ?></div>
                                <div class="stat-label">Total Amount</div>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Bills Alert -->
                    <?php if (!empty($overdue_bills)): ?>
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills</h5>
                        <p>You have <?php echo count($overdue_bills); ?> overdue bill(s). Please make payment as soon as possible.</p>
                    </div>
                    <?php endif; ?>

                    <!-- Bills List -->
                    <?php if (empty($billings)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                                <h4>No Bills Found</h4>
                                <p class="text-muted">You don't have any bills at the moment.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bill Date</th>
                                        <th>Animal</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($billings as $billing): 
                                        $isOverdue = $billing['due_date'] && strtotime($billing['due_date']) < time() && $billing['payment_status'] === 'pending';
                                    ?>
                                    <tr class="<?php echo $isOverdue ? 'table-danger' : ''; ?>">
                                        <td><?php echo formatDate($billing['billing_date'], 'M j, Y'); ?></td>
                                        <td><?php echo htmlspecialchars($billing['animal_name']); ?></td>
                                        <td>
                                            <?php if ($billing['treatment_id']): ?>
                                                <span class="text-muted">Treatment</span>
                                            <?php else: ?>
                                                <span class="text-muted">Service</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong>MWK <?php echo number_format($billing['total_amount'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($billing['due_date']): ?>
                                                <span class="<?php echo $isOverdue ? 'text-danger fw-bold' : ''; ?>">
                                                    <?php echo formatDate($billing['due_date'], 'M j, Y'); ?>
                                                    <?php if ($isOverdue): ?>
                                                        <br><small class="text-danger">Overdue</small>
                                                    <?php endif; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'pending' => 'bg-warning',
                                                'paid' => 'bg-success',
                                                'verified' => 'bg-info',
                                                'cancelled' => 'bg-danger'
                                            ];
                                            ?>
                                            <span class="badge <?php echo $statusBadge[$billing['payment_status']] ?? 'bg-secondary'; ?>">
                                                <?php echo ucfirst($billing['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/client/billings/' . $billing['billing_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/billings/' . $billing['billing_id'] . '/invoice'); ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Download Invoice" target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <?php if ($billing['payment_status'] === 'pending'): ?>
                                                <a href="<?php echo url('/client/billings/' . $billing['billing_id'] . '/payment'); ?>" 
                                                   class="btn btn-sm btn-success" title="Make Payment">
                                                    <i class="fas fa-credit-card"></i> Pay
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                        <nav aria-label="Billing pagination">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo url('/client/billings?page=' . $i . ($status ? '&status=' . $status : '')); ?>">
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

<style>
.empty-state {
    padding: 40px 20px;
}
.empty-state i {
    opacity: 0.5;
}
.table td {
    vertical-align: middle;
}
.stat-card.info .stat-icon {
    background-color: #17a2b8;
}
</style>