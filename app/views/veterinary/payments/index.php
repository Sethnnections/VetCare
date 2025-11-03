<?php
$payments = $payments ?? [];
$current_page = 'veterinary_payments';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Client Payments
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

                    <!-- Payments List -->
                    <?php if (empty($payments)): ?>
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                                <h4>No Payments Found</h4>
                                <p class="text-muted">No client payments are awaiting verification for your treatments.</p>
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
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo formatDate($payment['payment_date'], 'M j, Y'); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($payment['client_name']); ?>
                                            <?php if ($payment['client_phone']): ?>
                                                <br><small class="text-muted"><?php echo $payment['client_phone']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($payment['animal_name']); ?></td>
                                        <td>
                                            <strong>MWK <?php echo number_format($payment['total_amount'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Awaiting Verification</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/veterinary/payments/' . $payment['billing_id']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo url('/billings/' . $payment['billing_id'] . '/download-slip'); ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Download Slip" target="_blank">
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