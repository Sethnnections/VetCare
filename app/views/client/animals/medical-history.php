<?php
$animal = $animal ?? [];
$treatments = $treatments ?? [];
$vaccines = $vaccines ?? [];
$current_page = 'client_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-medical me-2"></i>
                        Medical History: <?php echo htmlspecialchars($animal['name'] ?? 'Animal'); ?>
                    </h4>
                    <div>
                        <a href="<?php echo url('/client/animals/' . ($animal['animal_id'] ?? '')); ?>" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Back to Animal
                        </a>
                        <a href="<?php echo url('/client/animals'); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-paw me-1"></i>All Animals
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

                    <!-- Animal Info Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-3">
                                    <h6 class="card-title">Animal Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($animal['name'] ?? ''); ?></p>
                                    <p class="mb-1"><strong>Species:</strong> <?php echo htmlspecialchars(ucfirst($animal['species'] ?? '')); ?></p>
                                    <p class="mb-1"><strong>Breed:</strong> <?php echo !empty($animal['breed']) ? htmlspecialchars($animal['breed']) : 'N/A'; ?></p>
                                    <p class="mb-0"><strong>Age:</strong> 
                                        <?php
                                        if (!empty($animal['birth_date'])) {
                                            $birthDate = new DateTime($animal['birth_date']);
                                            $today = new DateTime();
                                            $age = $today->diff($birthDate);
                                            
                                            if ($age->y > 0) {
                                                echo $age->y . ' year' . ($age->y > 1 ? 's' : '');
                                                if ($age->m > 0) {
                                                    echo ', ' . $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                                }
                                            } else {
                                                echo $age->m . ' month' . ($age->m > 1 ? 's' : '');
                                            }
                                        } else {
                                            echo 'Unknown';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body py-3">
                                    <h6 class="card-title">Medical Summary</h6>
                                    <p class="mb-1"><strong>Total Treatments:</strong> <?php echo count($treatments); ?></p>
                                    <p class="mb-1"><strong>Total Vaccinations:</strong> <?php echo count($vaccines); ?></p>
                                    <p class="mb-0"><strong>Status:</strong> 
                                        <span class="badge bg-<?php echo ($animal['status'] ?? 0) == 1 ? 'success' : 'secondary'; ?>">
                                            <?php echo ($animal['status'] ?? 0) == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Treatments Section -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-stethoscope me-2 text-primary"></i>
                                Treatment History
                                <span class="badge bg-primary ms-2"><?php echo count($treatments); ?></span>
                            </h5>
                            
                            <?php if (empty($treatments)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No treatment records found for this animal.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Diagnosis</th>
                                                <th>Treatment</th>
                                                <th>Medication</th>
                                                <th>Veterinarian</th>
                                                <th>Status</th>
                                                <th>Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($treatments as $treatment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?></strong>
                                                    <?php if (!empty($treatment['follow_up_date'])): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            Follow-up: <?php echo date('M j, Y', strtotime($treatment['follow_up_date'])); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($treatment['diagnosis'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($treatment['treatment_details'] ?? 'N/A'); ?></td>
                                                <td><?php echo !empty($treatment['medication_prescribed']) ? htmlspecialchars($treatment['medication_prescribed']) : 'None'; ?></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($treatment['veterinary_name'])) {
                                                        echo htmlspecialchars($treatment['veterinary_name']);
                                                    } else {
                                                        echo 'Veterinary Staff';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $treatment['status'] == 'completed' ? 'success' : 
                                                             ($treatment['status'] == 'ongoing' ? 'warning' : 
                                                             ($treatment['status'] == 'follow_up' ? 'info' : 'secondary')); 
                                                    ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $treatment['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($treatment['cost']) && $treatment['cost'] > 0): ?>
                                                        MWK <?php echo number_format($treatment['cost'], 2); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Vaccinations Section -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <h5 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-syringe me-2 text-success"></i>
                                Vaccination History
                                <span class="badge bg-success ms-2"><?php echo count($vaccines); ?></span>
                            </h5>
                            
                            <?php if (empty($vaccines)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No vaccination records found for this animal.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Vaccine Name</th>
                                                <th>Type</th>
                                                <th>Next Due</th>
                                                <th>Administered By</th>
                                                <th>Batch No.</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vaccines as $vaccine): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo date('M j, Y', strtotime($vaccine['vaccine_date'])); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></td>
                                                <td><?php echo !empty($vaccine['vaccine_type']) ? htmlspecialchars($vaccine['vaccine_type']) : 'N/A'; ?></td>
                                                <td>
                                                    <?php if (!empty($vaccine['next_due_date'])): ?>
                                                        <?php 
                                                        $nextDue = strtotime($vaccine['next_due_date']);
                                                        $today = time();
                                                        $daysUntilDue = ($nextDue - $today) / (60 * 60 * 24);
                                                        
                                                        if ($daysUntilDue < 0) {
                                                            echo '<span class="text-danger"><strong>' . date('M j, Y', $nextDue) . '</strong> (Overdue)</span>';
                                                        } elseif ($daysUntilDue <= 30) {
                                                            echo '<span class="text-warning"><strong>' . date('M j, Y', $nextDue) . '</strong> (Due soon)</span>';
                                                        } else {
                                                            echo '<span class="text-success">' . date('M j, Y', $nextDue) . '</span>';
                                                        }
                                                        ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not scheduled</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($vaccine['administered_by_name'])) {
                                                        echo htmlspecialchars($vaccine['administered_by_name']);
                                                    } else {
                                                        echo 'Veterinary Staff';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo !empty($vaccine['batch_number']) ? htmlspecialchars($vaccine['batch_number']) : 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $vaccine['status'] == 'completed' ? 'success' : 
                                                             ($vaccine['status'] == 'overdue' ? 'danger' : 'warning'); 
                                                    ?>">
                                                        <?php echo ucfirst($vaccine['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Print/Export Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Export Medical History</h6>
                                    <p class="text-muted mb-3">Download or print this medical history for your records.</p>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>Print
                                        </button>
                                        <button type="button" class="btn btn-outline-success" id="exportPdf">
                                            <i class="fas fa-file-pdf me-2"></i>Export as PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // PDF Export functionality (basic - would need a proper PDF library)
    document.getElementById('exportPdf').addEventListener('click', function() {
        alert('PDF export functionality would be implemented here with a proper PDF library.');
        // In a real implementation, you would use a library like jsPDF
        // or make an AJAX call to a server-side PDF generation endpoint
    });

    // Add print styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .btn, .card-header .btn, .alert, .export-section {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                background: white !important;
                color: black !important;
                border-bottom: 2px solid #000 !important;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>