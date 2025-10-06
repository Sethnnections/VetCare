<!-- Dashboard summery Start Here -->
<div class="row gutters-20">
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="dashboard-summery-one mg-b-20">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="item-icon bg-light-green ">
                        <i class="flaticon-classmates text-green"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Total Users</div>
                        <div class="item-number"><span class="counter" data-num="<?php 
                            $user = new User($db);
                            $stmt = $user->readAll();
                            echo $stmt->rowCount();
                        ?>"><?php echo $stmt->rowCount(); ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                        <div class="item-title">Veterinary Staff</div>
                        <div class="item-number"><span class="counter" data-num="<?php 
                            $vet_count = $db->query("SELECT COUNT(*) FROM users WHERE role='veterinary' AND is_active=1")->fetchColumn();
                            echo $vet_count;
                        ?>"><?php echo $vet_count; ?></span></div>
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
                        <i class="flaticon-couple text-orange"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Clients</div>
                        <div class="item-number"><span class="counter" data-num="<?php 
                            $client_count = $db->query("SELECT COUNT(*) FROM users WHERE role='client' AND is_active=1")->fetchColumn();
                            echo $client_count;
                        ?>"><?php echo $client_count; ?></span></div>
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
                        <i class="flaticon-money text-red"></i>
                    </div>
                </div>
                <div class="col-6">
                    <div class="item-content">
                        <div class="item-title">Active Users</div>
                        <div class="item-number"><span class="counter" data-num="<?php 
                            $active_count = $db->query("SELECT COUNT(*) FROM users WHERE is_active=1")->fetchColumn();
                            echo $active_count;
                        ?>"><?php echo $active_count; ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard summery End Here -->

<!-- User Management Section -->
<div class="row gutters-20" id="user-management">
    <div class="col-12">
        <div class="card dashboard-card-one pd-b-20">
            <div class="card-body">
                <div class="heading-layout1">
                    <div class="item-title">
                        <h3>User Management</h3>
                    </div>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">...</a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="register_veterinary.php"><i class="fas fa-user-plus"></i>Add Veterinary</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-redo-alt text-orange-peel"></i>Refresh</a>
                        </div>
                    </div>
                </div>



<!-- Add this quick action button -->
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="users.php" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="register_veterinary.php" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-user-plus me-2"></i>Add Veterinary
                    </a>
                </div>
                <!-- Add more quick actions as needed -->
            </div>
        </div>
    </div>
</div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $user = new User($db);
                            $stmt = $user->readAll();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $row['role'] == 'admin' ? 'danger' : 
                                             ($row['role'] == 'veterinary' ? 'info' : 'success'); 
                                    ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $row['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if($row['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" action="update_status.php" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                        <button type="submit" class="btn btn-sm btn-<?php echo $row['is_active'] ? 'warning' : 'success'; ?>">
                                            <?php echo $row['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>