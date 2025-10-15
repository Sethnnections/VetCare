<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Veterinary Dashboard</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ul>
        </div>
    </div>
</div>

<!-- Dashboard Summary Start Here -->
<div class="row gutters-20">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="dashboard-summery-one mg-b-20">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="item-icon bg-light-blue">
                        <i class="flaticon-multiple-users-silhouette text-blue"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">My Patients</div>
                        <div class="item-number"><span class="counter" data-num="<?php echo $stats['my_patients']; ?>"><?php echo $stats['my_patients']; ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="dashboard-summery-one mg-b-20">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="item-icon bg-light-green">
                        <i class="flaticon-books text-green"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Today's Treatments</div>
                        <div class="item-number"><span class="counter" data-num="<?php echo $stats['today_treatments']; ?>"><?php echo $stats['today_treatments']; ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="dashboard-summery-one mg-b-20">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="item-icon bg-light-yellow">
                        <i class="flaticon-calendar text-orange"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Follow-ups</div>
                        <div class="item-number"><span class="counter" data-num="<?php echo $stats['follow_ups']; ?>"><?php echo $stats['follow_ups']; ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="dashboard-summery-one mg-b-20">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="item-icon bg-light-red">
                        <i class="flaticon-open-book text-red"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Vaccinations Due</div>
                        <div class="item-number"><span class="counter" data-num="<?php echo $stats['vaccinations_due']; ?>"><?php echo $stats['vaccinations_due']; ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard Summary End Here -->

<!-- Quick Actions Section -->
<div class="row gutters-20">
    <div class="col-12">
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Quick Actions</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <a href="/treatments/create" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-stethoscope me-2"></i>New Treatment
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/vaccines/create" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-syringe me-2"></i>Record Vaccination
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/animals" class="btn btn-info w-100 mb-3">
                            <i class="fas fa-paw me-2"></i>View Animals
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/appointments" class="btn btn-warning w-100 mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>Appointments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Treatments Section -->
<div class="row gutters-20">
    <div class="col-lg-8">
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Recent Treatments</h3>
                    </div>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">...</a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/treatments"><i class="fas fa-list"></i>View All</a>
                            <a class="dropdown-item" href="/treatments/create"><i class="fas fa-plus"></i>Add New</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Animal</th>
                                <th>Client</th>
                                <th>Diagnosis</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTreatments as $treatment): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($treatment['animal_name']); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($treatment['species']); ?> - <?php echo htmlspecialchars($treatment['breed']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($treatment['client_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(substr($treatment['diagnosis'], 0, 50)) . '...'; ?></td>
                                <td><?php echo date('M j, Y', strtotime($treatment['treatment_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $treatment['status'] == 'completed' ? 'success' : 
                                             ($treatment['status'] == 'follow_up' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $treatment['status'])); ?>
                                    </span>
                                </td>
                                <td>MK <?php echo number_format($treatment['cost'] ?? 0, 2); ?></td>
                                <td>
                                    <a href="/treatments/<?php echo $treatment['treatment_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recentTreatments)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No recent treatments found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Follow-ups & Vaccinations Section -->
    <div class="col-lg-4">
        <!-- Upcoming Follow-ups Section -->
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Upcoming Follow-ups</h3>
                    </div>
                </div>
                <div class="follow-ups-list">
                    <?php foreach ($upcomingFollowUps as $followUp): ?>
                    <div class="media align-items-center mb-3">
                        <div class="media-body">
                            <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($followUp['animal_name']); ?></h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars(substr($followUp['diagnosis'], 0, 30)) . '...'; ?></p>
                            <small class="text-warning">
                                <i class="fas fa-calendar-day"></i> 
                                Due: <?php echo date('M j, Y', strtotime($followUp['follow_up_date'])); ?>
                            </small>
                            <br>
                            <small class="text-muted">Client: <?php echo htmlspecialchars($followUp['client_name'] ?? 'N/A'); ?></small>
                        </div>
                        <a href="/treatments/<?php echo $followUp['treatment_id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($upcomingFollowUps)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>No upcoming follow-ups</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Vaccinations Due Section -->
        <div class="card dashboard-card-one pd-b-20 mt-3">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Vaccinations Due Soon</h3>
                    </div>
                </div>
                <div class="vaccinations-list">
                    <?php foreach ($vaccinationsDue as $vaccine): ?>
                    <div class="media align-items-center mb-3">
                        <div class="media-body">
                            <h5 class="mt-0 mb-1"><?php echo htmlspecialchars($vaccine['animal_name']); ?></h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></p>
                            <small class="text-danger">
                                <i class="fas fa-clock"></i> 
                                Due: <?php echo date('M j, Y', strtotime($vaccine['next_due_date'])); ?>
                            </small>
                            <br>
                            <small class="text-muted">Client: <?php echo htmlspecialchars($vaccine['client_name'] ?? 'N/A'); ?></small>
                        </div>
                        <a href="/vaccines/<?php echo $vaccine['vaccine_id']; ?>" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-syringe"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($vaccinationsDue)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>No vaccinations due soon</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Treatment Statistics Section -->
<div class="row gutters-20">
    <div class="col-12">
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Treatment Statistics</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="stat-item">
                            <h2 class="text-primary"><?php echo $stats['ongoing_treatments']; ?></h2>
                            <p class="text-muted">Ongoing Treatments</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stat-item">
                            <h2 class="text-success"><?php echo $stats['completed_treatments']; ?></h2>
                            <p class="text-muted">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stat-item">
                            <h2 class="text-warning"><?php echo $stats['follow_up_required']; ?></h2>
                            <p class="text-muted">Follow-up Required</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="stat-item">
                            <h2 class="text-info">MK <?php echo number_format($stats['total_revenue'], 2); ?></h2>
                            <p class="text-muted">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emergency Contacts Section -->
<div class="row gutters-20">
    <div class="col-12">
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>Emergency Contacts</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="emergency-contact text-center p-3 border rounded">
                            <i class="fas fa-ambulance fa-2x text-danger mb-2"></i>
                            <h5>Emergency Vet</h5>
                            <p class="mb-1">Dr. John Smith</p>
                            <p class="mb-1"><strong>+265 888 123 456</strong></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="emergency-contact text-center p-3 border rounded">
                            <i class="fas fa-hospital fa-2x text-primary mb-2"></i>
                            <h5>24/7 Clinic</h5>
                            <p class="mb-1">Blantyre Animal Hospital</p>
                            <p class="mb-1"><strong>+265 888 234 567</strong></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="emergency-contact text-center p-3 border rounded">
                            <i class="fas fa-phone-alt fa-2x text-success mb-2"></i>
                            <h5>Poison Control</h5>
                            <p class="mb-1">Emergency Hotline</p>
                            <p class="mb-1"><strong>+265 888 345 678</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>