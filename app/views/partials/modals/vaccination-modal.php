<div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vaccinationModalLabel">Record Vaccination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vaccinationForm" method="POST" action="<?php echo url('/vaccinations/store'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_animal_id" class="form-label">Animal <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_animal_id" name="animal_id" required>
                                    <option value="">Select Animal</option>
                                    <?php foreach ($animals as $animal): ?>
                                    <option value="<?php echo $animal['animal_id']; ?>">
                                        <?php echo $animal['name'] . ' (' . $animal['species'] . ')'; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_vaccine_name" class="form-label">Vaccine Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_vaccine_name" name="vaccine_name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_vaccine_date" class="form-label">Vaccination Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="modal_vaccine_date" name="vaccine_date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_next_due_date" class="form-label">Next Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="modal_next_due_date" name="next_due_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="modal_notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="vaccinationForm" class="btn btn-primary">Save Vaccination</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate next due date for modal
    const modalVaccineDate = document.getElementById('modal_vaccine_date');
    const modalNextDueDate = document.getElementById('modal_next_due_date');
    
    if (modalVaccineDate && modalNextDueDate) {
        modalVaccineDate.addEventListener('change', function() {
            if (this.value) {
                const vaccineDate = new Date(this.value);
                const nextDueDate = new Date(vaccineDate);
                nextDueDate.setFullYear(nextDueDate.getFullYear() + 1);
                modalNextDueDate.value = nextDueDate.toISOString().split('T')[0];
            }
        });
        
        // Trigger calculation on load
        if (modalVaccineDate.value) {
            modalVaccineDate.dispatchEvent(new Event('change'));
        }
    }
});
</script>