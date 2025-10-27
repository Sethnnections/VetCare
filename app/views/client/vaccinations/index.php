<?php
$current_page = 'vaccinations_index';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
           
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-primary"><?php echo $stats['total_vaccinations'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Total Vaccinations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-success"><?php echo $stats['completed'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-warning"><?php echo $stats['upcoming'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Upcoming</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card dashboard-card card-stats">
                        <div class="card-body text-center">
                            <h3 class="text-danger"><?php echo $stats['overdue'] ?? 0; ?></h3>
                            <p class="text-muted mb-0">Overdue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Vaccinations Alert -->
            <?php
            $upcomingVaccinations = array_filter($vaccinations, function($v) {
                $status = $v['vaccine_status'] ?? $v['status'];
                $dueDate = strtotime($v['next_due_date']);
                return $status === 'scheduled' && $dueDate >= time() && $dueDate <= strtotime('+30 days');
            });
            ?>
            
            <?php if (!empty($upcomingVaccinations)): ?>
            <div class="alert alert-warning">
                <h5><i class="fas fa-bell me-2"></i>Upcoming Vaccinations</h5>
                <p class="mb-2">You have <?php echo count($upcomingVaccinations); ?> vaccination(s) scheduled in the next 30 days:</p>
                <ul class="mb-0">
                    <?php foreach ($upcomingVaccinations as $vaccination): ?>
                    <li>
                        <strong><?php echo $vaccination['animal_name']; ?></strong> - 
                        <?php echo $vaccination['vaccine_name']; ?> on 
                        <?php echo formatDate($vaccination['next_due_date']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Overdue Vaccinations Alert -->
            <?php
            $overdueVaccinations = array_filter($vaccinations, function($v) {
                $status = $v['vaccine_status'] ?? $v['status'];
                return $status === 'overdue' || (strtotime($v['next_due_date']) < time() && $status !== 'completed');
            });
            ?>
            
            <?php if (!empty($overdueVaccinations)): ?>
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Overdue Vaccinations</h5>
                <p class="mb-2">The following vaccinations are overdue:</p>
                <ul class="mb-0">
                    <?php foreach ($overdueVaccinations as $vaccination): ?>
                    <li>
                        <strong><?php echo $vaccination['animal_name']; ?></strong> - 
                        <?php echo $vaccination['vaccine_name']; ?> was due on 
                        <?php echo formatDate($vaccination['next_due_date']); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Vaccination Records -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-syringe me-2"></i>Vaccination History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($vaccinations)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-syringe fa-3x text-muted mb-3"></i>
                            <h5>No vaccination records found</h5>
                            <p class="text-muted">Your animals don't have any vaccination records yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Animal</th>
                                        <th>Vaccine</th>
                                        <th>Date Administered</th>
                                        <th>Next Due Date</th>
                                        <th>Status</th>
                                        <th>Veterinarian</th>
                                        <th>Certificate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vaccinations as $vaccination): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle me-3 flex-shrink-0">
                                                    <i class="fas fa-paw text-white"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo $vaccination['animal_name']; ?></strong>
                                                    <br><small class="text-muted"><?php echo $vaccination['species']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo $vaccination['vaccine_name']; ?></strong>
                                            <?php if ($vaccination['vaccine_type']): ?>
                                            <br><small class="text-muted"><?php echo $vaccination['vaccine_type']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo formatDate($vaccination['vaccine_date']); ?></td>
                                        <td>
                                            <span class="<?php echo strtotime($vaccination['next_due_date']) < time() ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo formatDate($vaccination['next_due_date']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'completed' => 'success',
                                                'scheduled' => 'warning',
                                                'overdue' => 'danger'
                                            ];
                                            $status = $vaccination['vaccine_status'] ?? $vaccination['status'];
                                            $badgeClass = $statusBadge[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $badgeClass; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $vaccination['administered_by_name'] ?? 'Not specified'; ?></td>
                                        <td>
                                            <?php if ($vaccination['vaccine_status'] === 'completed'): ?>
                                            <a href="<?php echo url('/vaccinations/' . $vaccination['vaccine_id'] . '/certificate'); ?>" 
                                               class="btn btn-sm btn-outline-success" target="_blank">
                                                <i class="fas fa-certificate me-1"></i>Certificate
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
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

            <!-- Vaccination Schedule -->
            <div class="card dashboard-card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar me-2"></i>Vaccination Schedule
                    </h5>
                </div>
                <div class="card-body">
                    <div id="vaccinationCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('vaccinationCalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: [
            <?php foreach ($vaccinations as $vaccination): ?>
            {
                title: '<?php echo $vaccination['animal_name'] . " - " . $vaccination['vaccine_name']; ?>',
                start: '<?php echo $vaccination['vaccine_date']; ?>',
                end: '<?php echo $vaccination['next_due_date']; ?>',
                color: '<?php echo (strtotime($vaccination['next_due_date']) < time()) ? "#dc3545" : "#28a745"; ?>',
                url: '<?php echo url('/vaccinations/' . $vaccination['vaccine_id']); ?>'
            },
            <?php endforeach; ?>
        ],
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.open(info.event.url, '_blank');
            }
        }
    });
    calendar.render();
});
</script>