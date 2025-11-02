<?php
$animals = $animals ?? [];
$veterinarians = $veterinarians ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12 mx-auto">
            <div class="dashboard-card fade-in">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comment-medical me-2"></i>Submit Feedback
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo url('/feedback/store'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <!-- Veterinary Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="veterinary_id" class="form-label">Veterinary (Optional)</label>
                                <select class="form-select <?php echo isset($errors['veterinary_id']) ? 'is-invalid' : ''; ?>" 
                                        id="veterinary_id" name="veterinary_id">
                                    <option value="">Select Veterinary</option>
                                    <?php foreach ($veterinarians as $vet): ?>
                                        <option value="<?php echo $vet['user_id']; ?>" 
                                                <?php echo ($old['veterinary_id'] ?? '') == $vet['user_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['veterinary_id'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['veterinary_id']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Animal Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="animal_id" class="form-label">Animal (Optional)</label>
                                <select class="form-select <?php echo isset($errors['animal_id']) ? 'is-invalid' : ''; ?>" 
                                        id="animal_id" name="animal_id">
                                    <option value="">Select Animal</option>
                                    <?php foreach ($animals as $animal): ?>
                                        <option value="<?php echo $animal['animal_id']; ?>" 
                                                <?php echo ($old['animal_id'] ?? '') == $animal['animal_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($animal['name'] . ' (' . $animal['species'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['animal_id'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['animal_id']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                           <?php echo ($old['rating'] ?? '') == $i ? 'checked' : ''; ?>
                                           class="d-none" required>
                                    <label for="star<?php echo $i; ?>" class="star-label">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <?php if (isset($errors['rating'])): ?>
                                <div class="text-danger small"><?php echo $errors['rating']; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Comments -->
                        <div class="mb-3">
                            <label for="comments" class="form-label">Comments</label>
                            <textarea class="form-control <?php echo isset($errors['comments']) ? 'is-invalid' : ''; ?>" 
                                      id="comments" name="comments" rows="5" 
                                      placeholder="Share your experience, suggestions, or concerns..."><?php echo htmlspecialchars($old['comments'] ?? ''); ?></textarea>
                            <?php if (isset($errors['comments'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['comments']; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Treatment Reference (Optional) -->
                        <div class="mb-3">
                            <label for="treatment_id" class="form-label">Related Treatment (Optional)</label>
                            <input type="number" class="form-control <?php echo isset($errors['treatment_id']) ? 'is-invalid' : ''; ?>" 
                                   id="treatment_id" name="treatment_id" 
                                   value="<?php echo htmlspecialchars($old['treatment_id'] ?? ''); ?>"
                                   placeholder="Treatment ID if applicable">
                            <?php if (isset($errors['treatment_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['treatment_id']; ?></div>
                            <?php endif; ?>
                            <div class="form-text">If this feedback is related to a specific treatment, enter the treatment ID.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo url('/client/feedback'); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Feedback
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.star-label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-label:hover,
.star-label:hover ~ .star-label {
    color: #ffc107;
}

.rating-input input:checked ~ .star-label {
    color: #ffc107;
}

.rating-input input:checked + .star-label {
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star rating interaction
    const stars = document.querySelectorAll('.star-label');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.htmlFor.replace('star', '');
            document.querySelectorAll('.star-label').forEach(s => {
                if (s.htmlFor.replace('star', '') <= rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });

    // Auto-populate current rating
    const currentRating = <?php echo json_encode($old['rating'] ?? 0); ?>;
    if (currentRating > 0) {
        document.querySelectorAll('.star-label').forEach(star => {
            if (star.htmlFor.replace('star', '') <= currentRating) {
                star.style.color = '#ffc107';
            }
        });
    }
});
</script>