<?php
class AdminAssignmentController extends Controller {
    private $animalModel;
    private $userModel;
    
    public function __construct() {
        $this->authorize(ROLE_ADMIN);
        $this->animalModel = new Animal();
        $this->userModel = new User();
    }
    
    /**
     * Show assignment management page
     */
    public function index() {
        try {
            $unassignedAnimals = $this->animalModel->getUnassignedAnimals();
            $veterinarians = $this->userModel->getUsersByRole('veterinary');
            $currentAssignments = $this->animalModel->getCurrentAssignments();
            
            $this->setTitle('Animal Assignment Management');
            $this->setData('unassignedAnimals', $unassignedAnimals);
            $this->setData('veterinarians', $veterinarians);
            $this->setData('currentAssignments', $currentAssignments);
            $this->view('admin/animals/animal-assignments', 'dashboard');
            
        } catch (Exception $e) {
            logError("Assignment management error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while loading assignment management');
            $this->redirect('/admin/animals');
        }
    }
    
    /**
     * Assign animal to veterinary
     */
    public function assign() {
        if (!$this->isPost()) {
            $this->redirect('/admin/animal-assignments');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animalId = $this->input('animal_id');
            $veterinaryId = $this->input('veterinary_id');
            $assignedBy = $_SESSION['user_id'];
            
            if (empty($animalId) || empty($veterinaryId)) {
                $this->setFlash('error', 'Please select both animal and veterinary');
                $this->redirect('/admin/animal-assignments');
                return;
            }
            
            $animal = $this->animalModel->find($animalId);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animal-assignments');
                return;
            }
            
            // Assign veterinary
            $assigned = $this->animalModel->assignToVeterinary($animalId, $veterinaryId);
            
            if ($assigned) {
                // Log assignment
                $this->animalModel->logAssignment($animalId, $veterinaryId, $assignedBy, 'assigned');
                
                $veterinary = $this->userModel->find($veterinaryId);
                $vetName = $veterinary['first_name'] . ' ' . $veterinary['last_name'];
                
                logActivity("Animal '{$animal['name']}' assigned to veterinary '{$vetName}' by admin");
                $this->setFlash('success', 'Animal assigned to veterinary successfully');
            } else {
                $this->setFlash('error', 'Failed to assign animal to veterinary');
            }
            
            $this->redirect('/admin/animal-assignments');
            
        } catch (Exception $e) {
            logError("Animal assignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while assigning animal');
            $this->redirect('/admin/animal-assignments');
        }
    }
    
    /**
     * Unassign animal from veterinary
     */
    public function unassign($animalId) {
        if (!$this->isPost()) {
            $this->redirect('/admin/animal-assignments');
            return;
        }
        
        try {
            $this->validateCsrf();
            
            $animal = $this->animalModel->find($animalId);
            if (!$animal) {
                $this->setFlash('error', 'Animal not found');
                $this->redirect('/admin/animal-assignments');
                return;
            }
            
            // Log the unassignment before removing
            if ($animal['assigned_veterinary']) {
                $this->animalModel->logAssignment($animalId, $animal['assigned_veterinary'], $_SESSION['user_id'], 'unassigned');
            }
            
            $unassigned = $this->animalModel->unassignFromVeterinary($animalId);
            
            if ($unassigned) {
                logActivity("Animal '{$animal['name']}' unassigned from veterinary by admin");
                $this->setFlash('success', 'Animal unassigned successfully');
            } else {
                $this->setFlash('error', 'Failed to unassign animal');
            }
            
            $this->redirect('/admin/animal-assignments');
            
        } catch (Exception $e) {
            logError("Animal unassignment error: " . $e->getMessage());
            $this->setFlash('error', 'An error occurred while unassigning animal');
            $this->redirect('/admin/animal-assignments');
        }
    }
}
?>