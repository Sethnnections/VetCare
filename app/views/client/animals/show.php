<?php
$current_page = 'client_animals_view';
$title = $title ?? 'Animal: ' . htmlspecialchars($animal['name']);
?>

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title"><?= $title ?></h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/client/animals') ?>">My Animals</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($animal['name']) ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Animal Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                <?php if (!empty($animal['photo'])): ?>
                    <img src="<?= url('/uploads/animals/' . $animal['photo']) ?>" 
                         alt="<?= htmlspecialchars($animal['name']) ?>" 
                         class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-paw fa-3x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <h4><?= htmlspecialchars($animal['name']) ?></h4>
                <p class="text-muted"><?= ucfirst($animal['species']) ?> 
                    <?php if (!empty($animal['breed'])): ?>
                        • <?= htmlspecialchars($animal['breed']) ?>
                    <?php endif; ?>
                </p>
                
                <div class="row text-left mt-4">
                    <div class="col-12">
                        <p><strong>Gender:</strong> 
                            <span class="badge badge-<?= $animal['gender'] == 'male' ? 'info' : 'warning' ?>">
                                <?= ucfirst($animal['gender']) ?>
                            </span>
                        </p>
                        <p><strong>Age:</strong> <?= calculateAge($animal['birth_date']) ?></p>
                        <p><strong>Weight:</strong> 
                            <?= !empty($animal['weight']) ? $animal['weight'] . ' kg' : 'Not specified' ?>
                        </p>
                        <p><strong>Color:</strong> 
                            <?= !empty($animal['color']) ? htmlspecialchars($animal['color']) : 'Not specified' ?>
                        </p>
                        <?php if (!empty($animal['microchip'])): ?>
                            <p><strong>Microchip:</strong> <?= htmlspecialchars($animal['microchip']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="<?= url('/client/animals/' . $animal['animal_id'] . '/edit') ?>" 
                       class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Animal
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Medical History Summary -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Medical Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <small class="text-muted">Last Treatment</small>
                            <br>
                            <strong class="h4">
                                <?= !empty($lastTreatment) ? formatDate($lastTreatment['treatment_date']) : 'No treatments' ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning">
                            <small class="text-muted">Next Vaccination Due</small>
                            <br>
                            <strong class="h4">
                                <?= !empty($nextVaccination) ? formatDate($nextVaccination['next_due_date']) : 'No upcoming' ?>
                            </strong>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($animal['notes'])): ?>
                    <div class="mt-3">
                        <h6>Special Notes</h6>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($animal['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Treatments -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Recent Treatments</h4>
                <a href="<?= url('/treatments?animal=' . $animal['animal_id']) ?>" class="btn btn-sm btn-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($treatments)): ?>
                    <p class="text-muted">No treatments recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Diagnosis</th>
                                    <th>Veterinary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($treatments, 0, 5) as $treatment): ?>
                                    <tr>
                                        <td><?= formatDate($treatment['treatment_date']) ?></td>
                                        <td><?= strLimit($treatment['diagnosis'] ?? 'No diagnosis', 50) ?></td>
                                        <td><?= htmlspecialchars($treatment['veterinary_name'] ?? 'Unknown') ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $treatment['status'] == 'completed' ? 'success' : 
                                                ($treatment['status'] == 'ongoing' ? 'warning' : 'secondary')
                                            ?>">
                                                <?= ucfirst($treatment['status']) ?>
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

        <!-- Vaccination History -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Vaccination History</h4>
            </div>
            <div class="card-body">
                <?php if (empty($vaccines)): ?>
                    <p class="text-muted">No vaccinations recorded yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Date</th>
                                    <th>Next Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vaccines as $vaccine): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($vaccine['vaccine_name']) ?></td>
                                        <td><?= formatDate($vaccine['administered_date']) ?></td>
                                        <td><?= formatDate($vaccine['next_due_date']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $vaccine['status'] == 'completed' ? 'success' : 
                                                ($vaccine['status'] == 'overdue' ? 'danger' : 'warning')
                                            ?>">
                                                <?= ucfirst($vaccine['status']) ?>
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