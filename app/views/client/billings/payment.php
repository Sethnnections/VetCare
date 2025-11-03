<?php
$billing = $billing ?? [];
$current_page = 'client_billings';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Make Payment - Bill #<?php echo $billing['billing_id']; ?>
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

                    <!-- Bill Summary -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Payment Summary</h5>
                        <p class="mb-1"><strong>Amount Due:</strong> MWK <?php echo number_format($billing['total_amount'], 2); ?></p>
                        <p class="mb-1"><strong>Animal:</strong> <?php echo htmlspecialchars($billing['animal_name']); ?></p>
                        <p class="mb-0"><strong>Due Date:</strong> <?php echo $billing['due_date'] ? formatDate($billing['due_date'], 'F j, Y') : 'Not specified'; ?></p>
                    </div>

                    <form action="<?php echo url('/client/billings/' . $billing['billing_id'] . '/process-payment'); ?>" 
                          method="POST" enctype="multipart/form-data" id="paymentForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cash">Cash Deposit</option>
                            </select>
                            <?php if (isset($errors['payment_method'])): ?>
                                <div class="text-danger small"><?php echo $errors['payment_method']; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Deposit Slip Upload -->
                        <div class="mb-4">
                            <label for="deposit_slip" class="form-label">Deposit Slip *</label>
                            <input type="file" class="form-control" id="deposit_slip" name="deposit_slip" 
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">
                                Upload a clear image or PDF of your deposit slip. Maximum file size: 5MB. 
                                Accepted formats: JPG, PNG, PDF.
                            </div>
                            <?php if (isset($errors['deposit_slip'])): ?>
                                <div class="text-danger small"><?php echo $errors['deposit_slip']; ?></div>
                            <?php endif; ?>
                            
                            <!-- File Preview -->
                            <div id="filePreview" class="mt-2 d-none">
                                <img id="previewImage" class="img-thumbnail" style="max-height: 200px;">
                                <div id="previewPDF" class="d-none">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        PDF file selected
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Payment Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about this payment..."></textarea>
                        </div>

                        <!-- Bank Details (Shown when bank transfer selected) -->
                        <div id="bankDetails" class="card mb-4 d-none">
                            <div class="card-header bg-primary text-white">
                                <h6 class="card-title mb-0">Bank Transfer Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Bank Name:</strong><br>
                                        National Bank of Malawi
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Account Name:</strong><br>
                                        Veterinary Clinic
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>Account Number:</strong><br>
                                        1000 1234 5678
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Branch:</strong><br>
                                        City Center
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <strong>Reference:</strong><br>
                                        <code>BILL-<?php echo $billing['billing_id']; ?>-<?php echo strtoupper(substr($billing['client_name'], 0, 3)); ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Money Details (Shown when mobile money selected) -->
                        <div id="mobileMoneyDetails" class="card mb-4 d-none">
                            <div class="card-header bg-success text-white">
                                <h6 class="card-title mb-0">Mobile Money Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Airtel Money:</strong><br>
                                        0882 279 994
                                    </div>
                                    <div class="col-md-6">
                                        <strong>TNM Mpamba:</strong><br>
                                        0992 920 181
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <strong>Reference:</strong><br>
                                        <code>BILL-<?php echo $billing['billing_id']; ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Important Information</h6>
                            <ul class="mb-0 small">
                                <li>Please ensure the deposit slip is clear and readable</li>
                                <li>Include the reference number in your payment</li>
                                <li>Payment verification may take 1-2 business days</li>
                                <li>Contact support if you encounter any issues</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/client/billings/' . $billing['billing_id']); ?>" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Submit Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');
    const mobileMoneyDetails = document.getElementById('mobileMoneyDetails');
    const depositSlip = document.getElementById('deposit_slip');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const previewPDF = document.getElementById('previewPDF');

    // Show/hide payment method details
    paymentMethod.addEventListener('change', function() {
        bankDetails.classList.add('d-none');
        mobileMoneyDetails.classList.add('d-none');
        
        if (this.value === 'bank_transfer') {
            bankDetails.classList.remove('d-none');
        } else if (this.value === 'mobile_money') {
            mobileMoneyDetails.classList.remove('d-none');
        }
    });

    // File preview
    depositSlip.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        filePreview.classList.remove('d-none');
        previewImage.classList.add('d-none');
        previewPDF.classList.add('d-none');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            previewPDF.classList.remove('d-none');
        }
    });

    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const method = paymentMethod.value;
        const file = depositSlip.files[0];

        if (!method) {
            e.preventDefault();
            alert('Please select a payment method');
            paymentMethod.focus();
            return;
        }

        if (!file) {
            e.preventDefault();
            alert('Please upload a deposit slip');
            depositSlip.focus();
            return;
        }

        // File size validation
        if (file.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('File size must be less than 5MB');
            return;
        }
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>