<?php
$animal = $animal ?? [];
$medicationHistory = $medicationHistory ?? [];
$activeMedications = $activeMedications ?? [];
$current_page = 'admin_animals';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-pills me-2"></i>Medication History: <?php echo htmlspecialchars($animal['name']); ?>
                    </h4>
                    <div class="btn-group">
                        <a href="<?php echo url('/admin/animals/' . $animal['animal_id']); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Animal
                        </a>
                        <a href="<?php echo url('/admin/animals'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i>All Animals
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Active Medications -->
                    <?php if (!empty($activeMedications)): ?>
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i>Active Medications
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($activeMedications as $medication): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card medication-card active">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($medication['diagnosis']); ?></h6>
                                            <p class="card-text">
                                                <strong>Medication:</strong> 
                                                <?php echo htmlspecialchars($medication['medication_prescribed']); ?>
                                            </p>
                                            <div class="d-flex justify-content-between text-muted">
                                                <small>
                                                    <i class="fas fa-user-md me-1"></i>
                                                    <?php echo htmlspecialchars($medication['vet_first_name'] . ' ' . $medication['vet_last_name']); ?>
                                                </small>
                                                <small>
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo formatDate($medication['treatment_date']); ?>
                                                </small>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge bg-<?php echo $medication['status'] == 'ongoing' ? 'primary' : 'info'; ?>">
                                                    <?php echo ucfirst($medication['status']); ?>
                                                </span>
                                                <?php if ($medication['follow_up_date']): ?>
                                                <span class="badge bg-warning">
                                                    Follow-up: <?php echo formatDate($medication['follow_up_date']); ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Medication History -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Complete Medication History</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($medicationHistory)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No medication history found</p>
                                </div>
                            <?php else: ?>
                                <div class="timeline">
                                    <?php foreach ($medicationHistory as $record): ?>
                                    <div class="timeline-item <?php echo $record['type']; ?>">
                                        <div class="timeline-marker">
                                            <?php if ($record['type'] == 'treatment'): ?>
                                                <i class="fas fa-stethoscope"></i>
                                            <?php else: ?>
                                                <i class="fas fa-syringe"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <h6 class="mb-1">
                                                    <?php if ($record['type'] == 'treatment'): ?>
                                                        <?php echo htmlspecialchars($record['diagnosis']); ?>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($record['vaccine_name']); ?> Vaccination
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php echo formatDateTime($record['created_at']); ?>
                                                    • By <?php echo htmlspecialchars($record['vet_first_name'] . ' ' . $record['vet_last_name']); ?>
                                                </small>
                                            </div>
                                            <div class="timeline-body">
                                                <?php if ($record['type'] == 'treatment' && $record['medication_prescribed']): ?>
                                                    <p class="mb-1"><strong>Medication:</strong> <?php echo htmlspecialchars($record['medication_prescribed']); ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if ($record['type'] == 'treatment' && $record['treatment_details']): ?>
                                                    <p class="mb-1"><strong>Details:</strong> <?php echo htmlspecialchars($record['treatment_details']); ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if ($record['type'] == 'vaccine'): ?>
                                                    <p class="mb-1">
                                                        <strong>Type:</strong> <?php echo htmlspecialchars($record['vaccine_type'] ?? 'N/A'); ?>
                                                        <?php if ($record['batch_number']): ?>
                                                            • <strong>Batch:</strong> <?php echo htmlspecialchars($record['batch_number']); ?>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if ($record['next_due_date']): ?>
                                                        <p class="mb-0">
                                                            <strong>Next Due:</strong> 
                                                            <span class="<?php echo strtotime($record['next_due_date']) < time() ? 'text-danger' : 'text-success'; ?>">
                                                                <?php echo formatDate($record['next_due_date']); ?>
                                                            </span>
                                                        </p>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="timeline-footer">
                                                <span class="badge bg-<?php echo $record['type'] == 'treatment' ? 'primary' : 'success'; ?>">
                                                    <?php echo ucfirst($record['type']); ?>
                                                </span>
                                                <?php if ($record['type'] == 'treatment'): ?>
                                                    <span class="badge bg-<?php echo $record['status'] == 'completed' ? 'success' : 
                                                                          ($record['status'] == 'ongoing' ? 'primary' : 'warning'); ?>">
                                                        <?php echo ucfirst($record['status']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.medication-card {
    border-left: 4px solid #ffc107;
    transition: var(--transition);
}

.medication-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.medication-card.active {
    border-left-color: #28a745;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 30px;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.timeline-item.treatment .timeline-marker {
    background-color: var(--primary-blue);
}

.timeline-item.vaccine .timeline-marker {
    background-color: var(--primary-red);
}

.timeline-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 3px solid var(--primary-blue);
}

.timeline-item.vaccine .timeline-content {
    border-left-color: var(--primary-red);
}

.timeline-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.timeline-body {
    margin-bottom: 10px;
}

.timeline-footer {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}
</style>