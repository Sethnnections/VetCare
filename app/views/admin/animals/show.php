<?php
$animal = $animal ?? [];
$treatments = $treatments ?? [];
$vaccines = $vaccines ?? [];
$lastTreatment = $lastTreatment ?? [];
$nextVaccination = $nextVaccination ?? [];
$current_page = 'animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paw me-2"></i>Animal: <?php echo htmlspecialchars($animal['name']); ?>
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/edit'); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="<?php echo url('/animals'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Animal Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Animal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Name:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($animal['name']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Species/Breed:</div>
                                        <div class="col-sm-8">
                                            <?php echo htmlspecialchars($animal['species']); ?>
                                            <?php if ($animal['breed']): ?>
                                                 / <?php echo htmlspecialchars($animal['breed']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Gender:</div>
                                        <div class="col-sm-8"><?php echo ucfirst($animal['gender'] ?? 'Unknown'); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Age:</div>
                                        <div class="col-sm-8">
                                            <?php if ($animal['birth_date']): ?>
                                                <?php echo calculateAge($animal['birth_date']); ?> 
                                                (Born: <?php echo formatDate($animal['birth_date']); ?>)
                                            <?php else: ?>
                                                Unknown
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Color:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($animal['color'] ?? 'Not specified'); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Weight:</div>
                                        <div class="col-sm-8"><?php echo $animal['weight'] ? $animal['weight'] . ' kg' : 'Not specified'; ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4 fw-bold">Microchip:</div>
                                        <div class="col-sm-8"><?php echo htmlspecialchars($animal['microchip'] ?? 'Not chipped'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 fw-bold">Status:</div>
                                        <div class="col-sm-8">
                                            <span class="badge <?php echo $animal['status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($animal['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Owner Information</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($animal['client_name'])): ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold">Name:</div>
                                            <div class="col-sm-8"><?php echo htmlspecialchars($animal['client_name']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($animal['client_phone'])): ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold">Phone:</div>
                                            <div class="col-sm-8"><?php echo htmlspecialchars($animal['client_phone']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($animal['emergency_contact'])): ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-bold">Emergency Contact:</div>
                                            <div class="col-sm-8"><?php echo htmlspecialchars($animal['emergency_contact']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-stethoscope me-1"></i>Add Treatment
                                        </a>
                                        <a href="<?php echo url('/vaccines/create?animal_id=' . $animal['animal_id']); ?>" 
                                           class="btn btn-success">
                                            <i class="fas fa-syringe me-1"></i>Add Vaccination
                                        </a>
                                        <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/medical-history'); ?>" 
                                           class="btn btn-info">
                                            <i class="fas fa-file-medical me-1"></i>Medical History
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Treatments -->
                    <?php if (!empty($treatments)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Recent Treatments</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Diagnosis</th>
                                                    <th>Treatment</th>
                                                    <th>Status</th>
                                                    <th>Veterinary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($treatments, 0, 5) as $treatment): ?>
                                                <tr>
                                                    <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                                    <td><?php echo htmlspecialchars($treatment['diagnosis']); ?></td>
                                                    <td><?php echo htmlspecialchars($treatment['treatment_details']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $treatment['status'] == 'completed' ? 'success' : 'primary'; ?>">
                                                            <?php echo ucfirst($treatment['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($treatment['veterinary_name'] ?? 'Unknown'); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if (count($treatments) > 5): ?>
                                        <div class="text-center mt-3">
                                            <a href="<?php echo url('/animals/' . $animal['animal_id'] . '/medical-history'); ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                View All Treatments (<?php echo count($treatments); ?>)
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>