<?php
require_once 'includes/init.php';
requireLogin();
requireRole(['admin', 'veterinary']);

// Initialize controllers
require_once 'app/controllers/AnimalController.php';
require_once 'app/controllers/ClientController.php';
$animalController = new AnimalController();
$clientController = new ClientController();

$page_title = "Add New Animal";
$errors = [];
$old = [];

// Handle form submission
if ($_POST) {
    try {
        $result = $animalController->store();
        
        if (isset($result['success']) && $result['success']) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Animal added successfully!'];
            header("Location: animal_view.php?id=" . $result['animal_id']);
            exit();
        } else {
            $errors = $result['errors'] ?? [];
            $old = $result['old'] ?? [];
        }
    } catch (Exception $e) {
        $errors['general'] = 'An error occurred: ' . $e->getMessage();
        $old = $_POST;
    }
}

// Get clients for dropdown
$clients = $clientController->index(1, 1000); // Get all clients

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="bi bi-plus-circle me-2"></i>Add New Animal
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="animals.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Animals
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show">
                    <?php echo $_SESSION['flash_message']['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h5>Please fix the following errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Animal Form -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="animalForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Animal Name *</label>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                           id="name" name="name" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Owner *</label>
                                    <select class="form-control <?php echo isset($errors['client_id']) ? 'is-invalid' : ''; ?>" 
                                            id="client_id" name="client_id" required>
                                        <option value="">Select Client</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['client_id']; ?>" 
                                                <?php echo ($old['client_id'] ?? '') == $client['client_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($client['name'] . ' - ' . $client['phone']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['client_id'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['client_id']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="species" class="form-label">Species *</label>
                                    <select class="form-control <?php echo isset($errors['species']) ? 'is-invalid' : ''; ?>" 
                                            id="species" name="species" required>
                                        <option value="">Select Species</option>
                                        <option value="dog" <?php echo ($old['species'] ?? '') == 'dog' ? 'selected' : ''; ?>>Dog</option>
                                        <option value="cat" <?php echo ($old['species'] ?? '') == 'cat' ? 'selected' : ''; ?>>Cat</option>
                                        <option value="bird" <?php echo ($old['species'] ?? '') == 'bird' ? 'selected' : ''; ?>>Bird</option>
                                        <option value="rabbit" <?php echo ($old['species'] ?? '') == 'rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                                        <option value="other" <?php echo ($old['species'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['species'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['species']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="breed" class="form-label">Breed</label>
                                    <input type="text" class="form-control" id="breed" name="breed" 
                                           value="<?php echo htmlspecialchars($old['breed'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ($old['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($old['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="unknown" <?php echo ($old['gender'] ?? '') == 'unknown' ? 'selected' : ''; ?>>Unknown</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                           value="<?php echo htmlspecialchars($old['birth_date'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color/Markings</label>
                                    <input type="text" class="form-control" id="color" name="color" 
                                           value="<?php echo htmlspecialchars($old['color'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" step="0.1" class="form-control" id="weight" name="weight" 
                                           value="<?php echo htmlspecialchars($old['weight'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="microchip" class="form-label">Microchip Number</label>
                                    <input type="text" class="form-control <?php echo isset($errors['microchip']) ? 'is-invalid' : ''; ?>" 
                                           id="microchip" name="microchip" 
                                           value="<?php echo htmlspecialchars($old['microchip'] ?? ''); ?>">
                                    <?php if (isset($errors['microchip'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['microchip']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="1" <?php echo ($old['status'] ?? '1') == '1' ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($old['status'] ?? '') == '0' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($old['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="animals.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Add Animal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Auto-calculate age based on birth date
document.getElementById('birth_date').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    if (age > 0) {
        document.getElementById('age_display').textContent = age + ' years';
    }
});
</script>

<?php include 'includes/footer.php'; ?>