<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="animal_id" class="form-label">Animal <span class="text-danger">*</span></label>
            <select class="form-control" id="animal_id" name="animal_id" required>
                <option value="">Select Animal</option>
                <?php foreach ($animals as $animal): ?>
                <option value="<?php echo $animal['animal_id']; ?>" 
                    <?php echo (isset($vaccination['animal_id']) && $vaccination['animal_id'] == $animal['animal_id']) ? 'selected' : ''; ?>>
                    <?php echo $animal['name'] . ' (' . $animal['species'] . ')'; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="vaccine_name" class="form-label">Vaccine Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" 
                   value="<?php echo $vaccination['vaccine_name'] ?? ''; ?>" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="vaccine_type" class="form-label">Vaccine Type</label>
            <select class="form-control" id="vaccine_type" name="vaccine_type">
                <option value="">Select Type</option>
                <option value="core" <?php echo (isset($vaccination['vaccine_type']) && $vaccination['vaccine_type'] == 'core') ? 'selected' : ''; ?>>Core Vaccine</option>
                <option value="non-core" <?php echo (isset($vaccination['vaccine_type']) && $vaccination['vaccine_type'] == 'non-core') ? 'selected' : ''; ?>>Non-Core Vaccine</option>
                <option value="rabies" <?php echo (isset($vaccination['vaccine_type']) && $vaccination['vaccine_type'] == 'rabies') ? 'selected' : ''; ?>>Rabies</option>
                <option value="combination" <?php echo (isset($vaccination['vaccine_type']) && $vaccination['vaccine_type'] == 'combination') ? 'selected' : ''; ?>>Combination</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="vaccine_date" class="form-label">Vaccination Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="vaccine_date" name="vaccine_date" 
                   value="<?php echo $vaccination['vaccine_date'] ?? date('Y-m-d'); ?>" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="next_due_date" class="form-label">Next Due Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="next_due_date" name="next_due_date" 
                   value="<?php echo $vaccination['next_due_date'] ?? ''; ?>" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="batch_number" class="form-label">Batch Number</label>
            <input type="text" class="form-control" id="batch_number" name="batch_number" 
                   value="<?php echo $vaccination['batch_number'] ?? ''; ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="manufacturer" class="form-label">Manufacturer</label>
            <input type="text" class="form-control" id="manufacturer" name="manufacturer" 
                   value="<?php echo $vaccination['manufacturer'] ?? ''; ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-control" id="status" name="status" required>
                <option value="scheduled" <?php echo (isset($vaccination['status']) && $vaccination['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                <option value="completed" <?php echo (isset($vaccination['status']) && $vaccination['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="overdue" <?php echo (isset($vaccination['status']) && $vaccination['status'] == 'overdue') ? 'selected' : ''; ?>>Overdue</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="mb-3">
            <label for="dosage" class="form-label">Dosage</label>
            <input type="text" class="form-control" id="dosage" name="dosage" 
                   value="<?php echo $vaccination['dosage'] ?? ''; ?>" placeholder="e.g., 1 mL">
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="route" class="form-label">Route</label>
            <select class="form-control" id="route" name="route">
                <option value="">Select Route</option>
                <option value="subcutaneous" <?php echo (isset($vaccination['route']) && $vaccination['route'] == 'subcutaneous') ? 'selected' : ''; ?>>Subcutaneous</option>
                <option value="intramuscular" <?php echo (isset($vaccination['route']) && $vaccination['route'] == 'intramuscular') ? 'selected' : ''; ?>>Intramuscular</option>
                <option value="oral" <?php echo (isset($vaccination['route']) && $vaccination['route'] == 'oral') ? 'selected' : ''; ?>>Oral</option>
                <option value="intranasal" <?php echo (isset($vaccination['route']) && $vaccination['route'] == 'intranasal') ? 'selected' : ''; ?>>Intranasal</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="site" class="form-label">Site</label>
            <input type="text" class="form-control" id="site" name="site" 
                   value="<?php echo $vaccination['site'] ?? ''; ?>" placeholder="e.g., Right shoulder">
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="administered_by" class="form-label">Administered By</label>
            <select class="form-control" id="administered_by" name="administered_by">
                <option value="">Select Veterinarian</option>
                <?php foreach ($veterinarians as $vet): ?>
                <option value="<?php echo $vet['user_id']; ?>" 
                    <?php echo (isset($vaccination['administered_by']) && $vaccination['administered_by'] == $vet['user_id']) ? 'selected' : ''; ?>>
                    <?php echo $vet['full_name']; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $vaccination['notes'] ?? ''; ?></textarea>
</div>

<div class="mb-3">
    <label for="reaction_notes" class="form-label">Reaction Notes (if any)</label>
    <textarea class="form-control" id="reaction_notes" name="reaction_notes" rows="2"><?php echo $vaccination['reaction_notes'] ?? ''; ?></textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate next due date based on vaccine type
    const vaccineDateInput = document.getElementById('vaccine_date');
    const nextDueDateInput = document.getElementById('next_due_date');
    const vaccineTypeSelect = document.getElementById('vaccine_type');
    
    function calculateNextDueDate() {
        if (!vaccineDateInput.value) return;
        
        const vaccineDate = new Date(vaccineDateInput.value);
        let nextDueDate = new Date(vaccineDate);
        
        // Set default duration based on vaccine type
        let monthsToAdd = 12; // Default 1 year
        
        switch(vaccineTypeSelect.value) {
            case 'rabies':
                monthsToAdd = 36; // 3 years for rabies
                break;
            case 'core':
                monthsToAdd = 12; // 1 year
                break;
            case 'non-core':
                monthsToAdd = 12; // 1 year
                break;
            case 'combination':
                monthsToAdd = 12; // 1 year
                break;
        }
        
        nextDueDate.setMonth(nextDueDate.getMonth() + monthsToAdd);
        nextDueDateInput.value = nextDueDate.toISOString().split('T')[0];
    }
    
    vaccineDateInput.addEventListener('change', calculateNextDueDate);
    vaccineTypeSelect.addEventListener('change', calculateNextDueDate);
});
</script>