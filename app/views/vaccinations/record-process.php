<?php
$current_page = 'vaccinations_record_process';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
          
            <div class="col-auto">
                <a href="<?php echo url('/vaccinations'); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <?php 
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Medical Process Steps -->
        <div class="col-lg-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-stethoscope me-2"></i>Vaccination Medical Protocol
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Process Steps -->
                    <div class="steps mb-4">
                        <div class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-label">Animal Selection</div>
                        </div>
                        <div class="step <?php echo $selectedAnimal ? 'completed' : 'active'; ?>">
                            <div class="step-number">2</div>
                            <div class="step-label">Health Assessment</div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-label">Vaccine Selection</div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-label">Administration</div>
                        </div>
                        <div class="step">
                            <div class="step-number">5</div>
                            <div class="step-label">Documentation</div>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo url('/vaccinations/record-process'); ?>" id="vaccinationProcessForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                        <!-- Step 1: Animal Selection -->
                        <div class="process-step" id="step1">
                            <h5 class="mb-3"><i class="fas fa-paw me-2 text-primary"></i>Step 1: Select Animal</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="animal_id" class="form-label">Animal *</label>
                                        <select class="form-control" id="animal_id" name="animal_id" required 
                                                onchange="loadAnimalData(this.value)">
                                            <option value="">Select Animal</option>
                                            <?php foreach ($animals as $animal): ?>
                                                <option value="<?php echo $animal['animal_id']; ?>" 
                                                    <?php echo ($selectedAnimal && $selectedAnimal['animal_id'] == $animal['animal_id']) ? 'selected' : ''; ?>>
                                                    <?php echo $animal['name']; ?> (<?php echo $animal['species']; ?>)
                                                    - <?php echo $animal['client_name'] ?? 'Client'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($errors['animal_id'])): ?>
                                            <div class="text-danger small"><?php echo $errors['animal_id']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <div id="animalQuickInfo" class="p-3 border rounded bg-light">
                                            <?php if ($selectedAnimal): ?>
                                                <h6><?php echo $selectedAnimal['animal_name']; ?></h6>
                                                <p class="mb-1"><?php echo $selectedAnimal['species']; ?> • <?php echo $selectedAnimal['breed']; ?></p>
                                                <p class="mb-1">Age: <?php echo calculateAge($selectedAnimal['birth_date']); ?></p>
                                                <p class="mb-0">Weight: <?php echo $selectedAnimal['weight'] ?? 'Unknown'; ?> kg</p>
                                            <?php else: ?>
                                                <p class="text-muted mb-0">Select an animal to view details</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($selectedAnimal && !empty($dueVaccinations)): ?>
                            <div class="alert alert-warning mt-3">
                                <h6><i class="fas fa-bell me-2"></i>Due Vaccinations</h6>
                                <ul class="mb-0">
                                    <?php foreach ($dueVaccinations as $due): ?>
                                        <li><?php echo $due['vaccine_name']; ?> - Due since <?php echo formatDate($due['next_due_date']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" onclick="showStep(2)">
                                    Continue to Health Assessment <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Health Assessment -->
                        <div class="process-step d-none" id="step2">
                            <h5 class="mb-3"><i class="fas fa-heartbeat me-2 text-success"></i>Step 2: Health Assessment</h5>
                            
                            <?php if ($selectedAnimal): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current_weight" class="form-label">Current Weight (kg) *</label>
                                        <input type="number" step="0.1" class="form-control" id="current_weight" 
                                               name="current_weight" value="<?php echo $selectedAnimal['weight'] ?? ''; ?>" 
                                               placeholder="Enter current weight" required>
                                        <?php if (isset($errors['current_weight'])): ?>
                                            <div class="text-danger small"><?php echo $errors['current_weight']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="animal_temperature" class="form-label">Temperature (°C)</label>
                                        <input type="number" step="0.1" class="form-control" id="animal_temperature" 
                                               name="animal_temperature" placeholder="37.5">
                                        <small class="text-muted">Normal range: 37.5-39.2°C</small>
                                        <?php if (isset($errors['animal_temperature'])): ?>
                                            <div class="text-danger small"><?php echo $errors['animal_temperature']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Health Status Check</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="health_good" name="health_good" required>
                                            <label class="form-check-label" for="health_good">
                                                Animal appears healthy and suitable for vaccination
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="no_allergies" name="no_allergies" required>
                                            <label class="form-check-label" for="no_allergies">
                                                No known allergies to vaccine components
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="no_illness" name="no_illness" required>
                                            <label class="form-check-label" for="no_illness">
                                                No signs of current illness or fever
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="health_notes" class="form-label">Health Assessment Notes</label>
                                <textarea class="form-control" id="health_notes" name="health_notes" rows="2" 
                                          placeholder="Any health observations..."></textarea>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="showStep(1)">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="showStep(3)">
                                    Continue to Vaccine Selection <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Vaccine Selection -->
                        <div class="process-step d-none" id="step3">
                            <h5 class="mb-3"><i class="fas fa-syringe me-2 text-warning"></i>Step 3: Vaccine Selection & Protocol</h5>
                            
                            <?php if ($selectedAnimal && !empty($vaccineProtocols)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vaccine_name" class="form-label">Vaccine Name *</label>
                                        <select class="form-control" id="vaccine_name" name="vaccine_name" required onchange="updateVaccineProtocol()">
                                            <option value="">Select Vaccine</option>
                                            <?php foreach ($vaccineProtocols as $category => $vaccines): ?>
                                                <optgroup label="<?php echo $category; ?>">
                                                    <?php foreach ($vaccines as $name => $protocol): ?>
                                                        <option value="<?php echo $name; ?>" data-protocol='<?php echo json_encode($protocol); ?>'>
                                                            <?php echo $name; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                            <option value="Other">Other (Specify)</option>
                                        </select>
                                        <input type="text" class="form-control mt-2 d-none" id="custom_vaccine" name="custom_vaccine" 
                                               placeholder="Enter custom vaccine name">
                                        <?php if (isset($errors['vaccine_name'])): ?>
                                            <div class="text-danger small"><?php echo $errors['vaccine_name']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vaccine_type" class="form-label">Vaccine Type</label>
                                        <select class="form-control" id="vaccine_type" name="vaccine_type">
                                            <option value="Core">Core Vaccine</option>
                                            <option value="Non-Core">Non-Core Vaccine</option>
                                            <option value="Rabies">Rabies</option>
                                            <option value="Combination">Combination</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Protocol Information -->
                            <div id="protocolInfo" class="alert alert-info d-none">
                                <h6><i class="fas fa-info-circle me-2"></i>Recommended Protocol</h6>
                                <div id="protocolDetails"></div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="dosage" class="form-label">Dosage *</label>
                                        <input type="text" class="form-control" id="dosage" name="dosage" 
                                               placeholder="e.g., 1.0 mL" required>
                                        <?php if (isset($errors['dosage'])): ?>
                                            <div class="text-danger small"><?php echo $errors['dosage']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="route" class="form-label">Route of Administration *</label>
                                        <select class="form-control" id="route" name="route" required>
                                            <option value="">Select Route</option>
                                            <option value="subcutaneous">Subcutaneous (SC)</option>
                                            <option value="intramuscular">Intramuscular (IM)</option>
                                            <option value="oral">Oral</option>
                                            <option value="intranasal">Intranasal</option>
                                            <option value="intradermal">Intradermal</option>
                                        </select>
                                        <?php if (isset($errors['route'])): ?>
                                            <div class="text-danger small"><?php echo $errors['route']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="site" class="form-label">Injection Site</label>
                                        <select class="form-control" id="site" name="site">
                                            <option value="">Select Site</option>
                                            <option value="right_scapular">Right Scapular</option>
                                            <option value="left_scapular">Left Scapular</option>
                                            <option value="right_thigh">Right Thigh</option>
                                            <option value="left_thigh">Left Thigh</option>
                                            <option value="neck">Neck</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="showStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="showStep(4)">
                                    Continue to Administration <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Administration -->
                        <div class="process-step d-none" id="step4">
                            <h5 class="mb-3"><i class="fas fa-user-md me-2 text-danger"></i>Step 4: Vaccine Administration</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vaccine_date" class="form-label">Vaccination Date *</label>
                                        <input type="datetime-local" class="form-control" id="vaccine_date" name="vaccine_date" 
                                               value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                        <?php if (isset($errors['vaccine_date'])): ?>
                                            <div class="text-danger small"><?php echo $errors['vaccine_date']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="next_due_date" class="form-label">Next Due Date *</label>
                                        <input type="date" class="form-control" id="next_due_date" name="next_due_date" 
                                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                        <?php if (isset($errors['next_due_date'])): ?>
                                            <div class="text-danger small"><?php echo $errors['next_due_date']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="batch_number" class="form-label">Batch Number *</label>
                                        <input type="text" class="form-control" id="batch_number" name="batch_number" 
                                               placeholder="e.g., A12345" required>
                                        <?php if (isset($errors['batch_number'])): ?>
                                            <div class="text-danger small"><?php echo $errors['batch_number']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="manufacturer" class="form-label">Manufacturer *</label>
                                        <input type="text" class="form-control" id="manufacturer" name="manufacturer" 
                                               placeholder="Vaccine manufacturer" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Administration Verification</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="verified_vaccine" name="verified_vaccine" required>
                                    <label class="form-check-label" for="verified_vaccine">
                                        Vaccine checked for correct type, dosage, and expiration date
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="proper_storage" name="proper_storage" required>
                                    <label class="form-check-label" for="proper_storage">
                                        Vaccine properly stored and handled according to manufacturer guidelines
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="site_prepped" name="site_prepped" required>
                                    <label class="form-check-label" for="site_prepped">
                                        Injection site properly prepared and disinfected
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="showStep(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="showStep(5)">
                                    Continue to Documentation <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 5: Documentation -->
                        <div class="process-step d-none" id="step5">
                            <h5 class="mb-3"><i class="fas fa-file-medical me-2 text-info"></i>Step 5: Documentation & Completion</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Clinical Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                                  placeholder="Any clinical observations, patient response, or additional information..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="reaction_notes" class="form-label">Reaction Monitoring</label>
                                        <textarea class="form-control" id="reaction_notes" name="reaction_notes" rows="3" 
                                                  placeholder="Record any immediate reactions observed..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="generate_certificate" name="generate_certificate" checked>
                                    <label class="form-check-label" for="generate_certificate">
                                        Generate vaccination certificate for client
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="client_informed" name="client_informed" required>
                                    <label class="form-check-label" for="client_informed">
                                        Client informed about vaccination and potential side effects
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="followup_scheduled" name="followup_scheduled">
                                    <label class="form-check-label" for="followup_scheduled">
                                        Schedule follow-up appointment if needed
                                    </label>
                                </div>
                            </div>
                            
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle me-2"></i>Ready to Complete</h6>
                                <p class="mb-0">All medical protocol steps have been completed. Review the information and submit to record the vaccination.</p>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="showStep(4)">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Complete Vaccination Record
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}
.step {
    text-align: center;
    flex: 1;
    position: relative;
}
.step:not(:last-child):after {
    content: '';
    position: absolute;
    top: 20px;
    left: 60%;
    width: 80%;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}
.step.completed:not(:last-child):after {
    background: #28a745;
}
.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    position: relative;
    z-index: 2;
}
.step.completed .step-number {
    background: #28a745;
    color: white;
}
.step.active .step-number {
    background: #007bff;
    color: white;
    border: 3px solid #b3d7ff;
}
.step-label {
    font-size: 0.9rem;
    font-weight: 500;
}
.process-step {
    padding: 20px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}
</style>

<script>
let currentStep = 1;

function showStep(step) {
    // Validate current step before proceeding
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Hide all steps
    document.querySelectorAll('.process-step').forEach(el => {
        el.classList.add('d-none');
    });
    
    // Show target step
    document.getElementById('step' + step).classList.remove('d-none');
    currentStep = step;
    
    // Update step indicators
    updateStepIndicators();
}

function validateStep(step) {
    // Basic validation for each step
    switch(step) {
        case 1:
            const animalId = document.getElementById('animal_id').value;
            if (!animalId) {
                alert('Please select an animal');
                return false;
            }
            return true;
            
        case 2:
            const weight = document.getElementById('current_weight').value;
            const healthChecks = [
                document.getElementById('health_good'),
                document.getElementById('no_allergies'), 
                document.getElementById('no_illness')
            ];
            
            if (!weight || weight <= 0) {
                alert('Please enter a valid current weight');
                return false;
            }
            
            for (let check of healthChecks) {
                if (!check.checked) {
                    alert('Please complete all health status checks');
                    return false;
                }
            }
            return true;
            
        // Add validation for other steps as needed
        default:
            return true;
    }
}

function updateStepIndicators() {
    // This would update the visual step indicators
    // Implementation depends on your specific UI design
}

function loadAnimalData(animalId) {
    if (!animalId) return;
    
    // Redirect to load animal data
    window.location.href = '<?php echo url('/vaccinations/record-process'); ?>?animal_id=' + animalId;
}

function updateVaccineProtocol() {
    const vaccineSelect = document.getElementById('vaccine_name');
    const selectedOption = vaccineSelect.options[vaccineSelect.selectedIndex];
    const protocolInfo = document.getElementById('protocolInfo');
    const protocolDetails = document.getElementById('protocolDetails');
    
    if (selectedOption.value === 'Other') {
        document.getElementById('custom_vaccine').classList.remove('d-none');
        protocolInfo.classList.add('d-none');
    } else {
        document.getElementById('custom_vaccine').classList.add('d-none');
        
        if (selectedOption.dataset.protocol) {
            const protocol = JSON.parse(selectedOption.dataset.protocol);
            protocolDetails.innerHTML = `
                <p><strong>Schedule:</strong> ${protocol.schedule}</p>
                <p><strong>Recommended Dosage:</strong> ${protocol.dosage}</p>
                <p><strong>Route:</strong> ${protocol.route}</p>
            `;
            
            // Auto-fill dosage and route
            document.getElementById('dosage').value = protocol.dosage;
            document.getElementById('route').value = protocol.route;
            
            protocolInfo.classList.remove('d-none');
        } else {
            protocolInfo.classList.add('d-none');
        }
    }
}

// Set default next due date (1 year from today)
document.addEventListener('DOMContentLoaded', function() {
    const nextDueDate = new Date();
    nextDueDate.setFullYear(nextDueDate.getFullYear() + 1);
    document.getElementById('next_due_date').value = nextDueDate.toISOString().split('T')[0];
    
    <?php if ($selectedAnimal): ?>
        showStep(2); // Start at step 2 if animal is already selected
    <?php endif; ?>
});
</script>