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
                        <i class="fas fa-clock me-2"></i>Pending Payment Verification
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
                                <i class="fas fa-check-circle fa-4x text-muted mb-3"></i>
                                <h4>No Pending Payments</h4>
                                <p class="text-muted">All payments have been verified.</p>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo formatDate($payment['payment_date'] ?? $payment['billing_date'], 'M j, Y'); ?></td>
                                        <td><?php echo htmlspecialchars($payment['client_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($payment['animal_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($payment['vet_name'] ?? 'N/A'); ?></td>
                                        <td><strong>MWK <?php echo number_format($payment['total_amount'], 2); ?></strong></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'] ?? 'unknown')); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo url('/billings/' . $payment['billing_id'] . '/download-slip'); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Download Deposit Slip">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="<?php echo url('/admin/payments/' . $payment['billing_id'] . '/verify'); ?>" 
                                                      method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Verify this payment?')">
                                                        <i class="fas fa-check"></i> Verify
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?php echo $payment['billing_id']; ?>">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal<?php echo $payment['billing_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="<?php echo url('/admin/payments/' . $payment['billing_id'] . '/reject'); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Payment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="rejection_notes" class="form-label">Rejection Reason</label>
                                                                    <textarea class="form-control" id="rejection_notes" name="rejection_notes" 
                                                                              rows="3" placeholder="Explain why this payment is being rejected..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Reject Payment</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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