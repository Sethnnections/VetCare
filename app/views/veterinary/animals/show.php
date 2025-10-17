<?php
$animal = $data['animal'] ?? [];
$treatments = $data['treatments'] ?? [];
$vaccines = $data['vaccines'] ?? [];
$lastTreatment = $data['lastTreatment'] ?? null;
$nextVaccination = $data['nextVaccination'] ?? null;
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Animal Details: <?php echo htmlspecialchars($animal['name']); ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('/veterinary/animals'); ?>">My Animals</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($animal['name']); ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Animal Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-paw fa-4x text-primary"></i>
                    <h4 class="mt-2"><?php echo htmlspecialchars($animal['name']); ?></h4>
                    <span class="badge bg-<?php echo $animal['status'] == 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($animal['status']); ?>
                    </span>
                </div>
                
                <table class="table table-sm">
                    <tr>
                        <td><strong>Species:</strong></td>
                        <td><?php echo ucfirst($animal['species']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Breed:</strong></td>
                        <td><?php echo htmlspecialchars($animal['breed'] ?? 'Mixed'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gender:</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $animal['gender'] == 'male' ? 'primary' : ($animal['gender'] == 'female' ? 'danger' : 'secondary'); ?>">
                                <?php echo ucfirst($animal['gender'] ?? 'Unknown'); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Age:</strong></td>
                        <td><?php echo calculateAge($animal['birth_date']) ?: 'Unknown'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Birth Date:</strong></td>
                        <td><?php echo $animal['birth_date'] ? formatDate($animal['birth_date']) : 'Unknown'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Color:</strong></td>
                        <td><?php echo htmlspecialchars($animal['color'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Weight:</strong></td>
                        <td><?php echo $animal['weight'] ? $animal['weight'] . ' kg' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Microchip:</strong></td>
                        <td><?php echo htmlspecialchars($animal['microchip'] ?? 'N/A'); ?></td>
                    </tr>
                </table>
                
                <?php if (!empty($animal['notes'])): ?>
                    <div class="mt-3">
                        <strong>Notes:</strong>
                        <p class="text-muted"><?php echo htmlspecialchars($animal['notes']); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="<?php echo url('/veterinary/animals/' . $animal['animal_id'] . '/edit'); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit Information
                    </a>
                    <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-stethoscope"></i> Add Treatment
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Last Treatment</h6>
                        <?php if ($lastTreatment): ?>
                            <h5><?php echo htmlspecialchars(substr($lastTreatment['diagnosis'], 0, 30)); ?>...</h5>
                            <small><?php echo formatDate($lastTreatment['treatment_date']); ?></small>
                        <?php else: ?>
                            <h5>No treatments</h5>
                            <small>No treatment history</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Next Vaccination</h6>
                        <?php if ($nextVaccination): ?>
                            <h5><?php echo htmlspecialchars($nextVaccination['vaccine_name']); ?></h5>
                            <small>Due: <?php echo formatDate($nextVaccination['next_due_date']); ?></small>
                        <?php else: ?>
                            <h5>No upcoming</h5>
                            <small>No vaccinations due</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Recent Treatments</h5>
                <a href="<?php echo url('/treatments/create?animal_id=' . $animal['animal_id']); ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> New Treatment
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($treatments)): ?>
                    <p class="text-muted">No treatment history found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($treatments, 0, 5) as $treatment): ?>
                                    <tr>
                                        <td><?php echo formatDate($treatment['treatment_date']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($treatment['diagnosis'], 0, 50)); ?>...</td>
                                        <td>
                                            <span class="badge bg-<?php echo $treatment['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($treatment['status']); ?>
                                            </span>
                                        </td>
                                        <td>MK<?php echo number_format($treatment['cost'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($treatments) > 5): ?>
                        <div class="text-center mt-2">
                            <a href="<?php echo url('/treatments?animal_id=' . $animal['animal_id']); ?>" class="btn btn-sm btn-outline-primary">
                                View All Treatments
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Vaccination History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($vaccines)): ?>
                    <p class="text-muted">No vaccination history found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Date</th>
                                    <th>Next Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($vaccines, 0, 5) as $vaccine): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></td>
                                        <td><?php echo formatDate($vaccine['vaccine_date']); ?></td>
                                        <td><?php echo $vaccine['next_due_date'] ? formatDate($vaccine['next_due_date']) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $vaccine['status'] == 'completed' ? 'success' : 'warning'; ?>">
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
    </div>
</div>