<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin', 'veterinary']);

// Initialize Animal Controller
require_once 'app/controllers/AnimalController.php';
$animalController = new AnimalController();

$page_title = "Animal Management";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="bi bi-heart me-2"></i>Animal Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="animal_create.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Add New Animal
                    </a>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search animals..." 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="species" class="form-select">
                                <option value="">All Species</option>
                                <option value="dog" <?php echo ($_GET['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                <option value="cat" <?php echo ($_GET['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                <option value="bird" <?php echo ($_GET['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                <option value="rabbit" <?php echo ($_GET['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                <option value="other" <?php echo ($_GET['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Animals Table -->
            <div class="card">
                <div class="card-body">
                    <?php
                    // Handle search and pagination
                    $search = $_GET['search'] ?? '';
                    $species = $_GET['species'] ?? '';
                    $page = $_GET['page'] ?? 1;
                    
                    // Get animals data
                    if ($search) {
                        $animals = $animalController->search($search);
                    } else {
                        $animals = $animalController->index($page);
                    }
                    
                    // Filter by species if specified
                    if ($species) {
                        $animals = array_filter($animals, function($animal) use ($species) {
                            return strtolower($animal['species']) === strtolower($species);
                        });
                    }
                    ?>

                    <?php if (empty($animals)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-heart-break display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No animals found</h4>
                            <p class="text-muted">Get started by adding your first animal.</p>
                            <a href="animal_create.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add Animal
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Species/Breed</th>
                                        <th>Client</th>
                                        <th>Age</th>
                                        <th>Last Treatment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals as $animal): 
                                        $animalModel = new Animal();
                                        $animalWithClient = $animalModel->getAnimalWithClient($animal['animal_id']);
                                        $lastTreatment = $animalModel->getLastTreatment($animal['animal_id']);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($animal['name']); ?></strong>
                                            <?php if($animal['gender'] == 'male'): ?>
                                                <i class="bi bi-gender-male text-primary"></i>
                                            <?php elseif($animal['gender'] == 'female'): ?>
                                                <i class="bi bi-gender-female text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($animal['species']); ?></span>
                                            <?php if($animal['breed']): ?>
                                                <br><small class="text-muted"><?php echo $animal['breed']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($animalWithClient['client_name'])): ?>
                                                <a href="client_view.php?id=<?php echo $animal['client_id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($animalWithClient['client_name']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $animalObj = new Animal();
                                            $animalObj->load($animal);
                                            echo $animalObj->getAge() ?: 'Unknown';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($lastTreatment): ?>
                                                <small>
                                                    <?php echo date('M j, Y', strtotime($lastTreatment['treatment_date'])); ?>
                                                    <br><span class="text-muted"><?php echo substr($lastTreatment['diagnosis'], 0, 30); ?>...</span>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">No treatments</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($animal['status'] == STATUS_ACTIVE): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="animal_view.php?id=<?php echo $animal['animal_id']; ?>" class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="animal_edit.php?id=<?php echo $animal['animal_id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if($_SESSION['role'] === 'admin'): ?>
                                                <a href="animal_delete.php?id=<?php echo $animal['animal_id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Animal pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&species=<?php echo urlencode($species); ?>">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link">Page <?php echo $page; ?></span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&species=<?php echo urlencode($species); ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $animalController->stats()['total'] ?? 0; ?></h4>
                                    <p class="mb-0">Total Animals</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-heart display-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $animalController->stats()['active'] ?? 0; ?></h4>
                                    <p class="mb-0">Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-check-circle display-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $animalController->stats()['by_species'][0]['count'] ?? 0; ?></h4>
                                    <p class="mb-0">Dogs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-bug display-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?php echo $animalController->stats()['by_species'][1]['count'] ?? 0; ?></h4>
                                    <p class="mb-0">Cats</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-stars display-6"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>