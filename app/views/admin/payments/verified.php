<?php
$payments = $payments ?? [];
$current_page = 'admin_payments';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Verified Payments
                    </h4>
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

                    <?php if (empty($payments)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                                <h4>No Verified Payments</h4>
                                <p class="text-muted">No payments have been verified yet.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Client</th>
                                        <th>Animal</th>
                                        <th>Veterinary</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Verified By</th>
                                        <th>Verified At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo formatDate($payment['payment_date'], 'M j, Y'); ?></td>
                                        <td><?php echo htmlspecialchars($payment['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['animal_name']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['vet_name'] ?? 'N/A'); ?></td>
                                        <td><strong>MWK <?php echo number_format($payment['total_amount'], 2); ?></strong></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                        <td><?php echo htmlspecialchars($payment['verified_by_name']); ?></td>
                                        <td><?php echo formatDateTime($payment['verified_at'], 'M j, Y g:i A'); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/billings/' . $payment['billing_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/billings/' . $payment['billing_id'] . '/invoice'); ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Download Invoice" target="_blank">
                                                    <i class="fas fa-download"></i>
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

<style>
.empty-state {
    padding: 40px 20px;
}
.empty-state i {
    opacity: 0.5;
}
</style>