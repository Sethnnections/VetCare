<?php

class Vaccine extends Model
{
    protected $table = 'vaccines';
    protected $primaryKey = 'vaccine_id';
    protected $fillable = [
        'animal_id',
        'vaccine_name',
        'vaccine_type',
        'vaccine_date',
        'next_due_date',
        'administered_by',
        'batch_number',
        'manufacturer',
        'notes',
        'status'
    ];

    // Vaccine properties
    private $vaccineId;
    private $animalId;
    private $vaccineName;
    private $vaccineType;
    private $vaccineDate;
    private $nextDueDate;
    private $administeredBy;
    private $batchNumber;
    private $manufacturer;
    private $notes;
    private $status;
    private $createdAt;
    private $updatedAt;

    // Getters
    public function getVaccineId()
    {
        return $this->vaccineId;
    }
    public function getAnimalId()
    {
        return $this->animalId;
    }
    public function getVaccineName()
    {
        return $this->vaccineName;
    }
    public function getVaccineType()
    {
        return $this->vaccineType;
    }
    public function getVaccineDate()
    {
        return $this->vaccineDate;
    }
    public function getNextDueDate()
    {
        return $this->nextDueDate;
    }
    public function getAdministeredBy()
    {
        return $this->administeredBy;
    }
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    public function getNotes()
    {
        return $this->notes;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // Setters
    public function setVaccineId($vaccineId)
    {
        $this->vaccineId = $vaccineId;
    }
    public function setAnimalId($animalId)
    {
        $this->animalId = (int) $animalId;
    }
    public function setVaccineName($vaccineName)
    {
        $this->vaccineName = sanitize($vaccineName);
    }
    public function setVaccineType($vaccineType)
    {
        $this->vaccineType = sanitize($vaccineType);
    }
    public function setVaccineDate($vaccineDate)
    {
        $this->vaccineDate = $vaccineDate;
    }
    public function setNextDueDate($nextDueDate)
    {
        $this->nextDueDate = $nextDueDate;
    }
    public function setAdministeredBy($administeredBy)
    {
        $this->administeredBy = (int) $administeredBy;
    }
    public function setBatchNumber($batchNumber)
    {
        $this->batchNumber = sanitize($batchNumber);
    }
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = sanitize($manufacturer);
    }
    public function setNotes($notes)
    {
        $this->notes = sanitize($notes);
    }
    public function setStatus($status)
    {
        $this->status = sanitize($status);
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    // Business logic methods
    public function getVaccineWithDetails($vaccineId)
    {
        $sql = "SELECT v.*, 
                       a.name as animal_name, a.species, a.breed,
                       c.name as client_name, c.phone as client_phone,
                       u.name as veterinary_name
                FROM {$this->table} v
                JOIN animals a ON v.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.vaccine_id = :vaccine_id";
        return fetchOne($sql, ['vaccine_id' => $vaccineId]);
    }


    /**
     * Get vaccines by animal with administrator details 
     */
    public function getVaccinesByAnimal($animalId)
    {
        $sql = "SELECT v.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as administered_by_name
                FROM {$this->table} v 
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.animal_id = :animal_id 
                ORDER BY v.vaccine_date DESC";
        return fetchAll($sql, ['animal_id' => $animalId]);
    }


    /**
     * Get upcoming vaccinations using view
     */
    public function getUpcomingVaccinations($days = 30)
    {
        $sql = "SELECT * FROM vaccine_details_view 
                WHERE next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                AND vaccine_status = 'completed'
                ORDER BY next_due_date ASC";
        return fetchAll($sql, ['days' => $days]);
    }





    public function markAsCompleted($vaccineId)
    {
        return $this->update($vaccineId, ['status' => 'completed']);
    }

    public function createVaccine($vaccineData)
    {
        // Set default values
        $vaccineData['vaccine_date'] = $vaccineData['vaccine_date'] ?? getCurrentDate();
        $vaccineData['status'] = $vaccineData['status'] ?? 'scheduled';

        return $this->create($vaccineData);
    }

    public function getVaccinationHistory($animalId, $limit = 10)
    {
        $sql = "SELECT v.*, u.name as veterinary_name
                FROM {$this->table} v
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.animal_id = :animal_id
                ORDER BY v.vaccine_date DESC
                LIMIT :limit";

        $stmt = $this->query($sql, ['animal_id' => $animalId]);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getVaccinesByDateRange($startDate, $endDate, $veterinaryId = null)
    {
        $sql = "SELECT v.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name,
                       u.name as veterinary_name
                FROM {$this->table} v
                JOIN animals a ON v.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.vaccine_date BETWEEN :start_date AND :end_date";

        $params = ['start_date' => $startDate, 'end_date' => $endDate];

        if ($veterinaryId) {
            $sql .= " AND v.administered_by = :veterinary_id";
            $params['veterinary_id'] = $veterinaryId;
        }

        $sql .= " ORDER BY v.vaccine_date DESC";

        return fetchAll($sql, $params);
    }

    // Validation
    public function validate($data, $id = null)
    {
        $errors = [];

        // Required fields
        $required = ['animal_id', 'vaccine_name', 'vaccine_date'];
        $errors = array_merge($errors, validateRequired($required, $data));

        // Validate animal exists
        if (!empty($data['animal_id'])) {
            $animalModel = new Animal();
            if (!$animalModel->exists($data['animal_id'])) {
                $errors['animal_id'] = 'Invalid animal selected';
            }
        }

        // Validate veterinary exists
        if (!empty($data['administered_by'])) {
            $userModel = new User();
            $vet = $userModel->find($data['administered_by']);
            if (!$vet || $vet['role'] !== ROLE_VETERINARY) {
                $errors['administered_by'] = 'Invalid veterinary selected';
            }
        }

        // Validate vaccine date
        if (!empty($data['vaccine_date'])) {
            $vaccineDate = strtotime($data['vaccine_date']);
            if (!$vaccineDate) {
                $errors['vaccine_date'] = 'Invalid vaccine date';
            }
        }

        // Validate next due date
        if (!empty($data['next_due_date'])) {
            $nextDueDate = strtotime($data['next_due_date']);
            if (!$nextDueDate) {
                $errors['next_due_date'] = 'Invalid next due date';
            } elseif (isset($data['vaccine_date']) && $nextDueDate <= strtotime($data['vaccine_date'])) {
                $errors['next_due_date'] = 'Next due date must be after vaccine date';
            }
        }

        return $errors;
    }

    // Search vaccines
    public function searchVaccines($term)
    {
        $sql = "SELECT v.*, 
                       a.name as animal_name, a.species,
                       c.name as client_name,
                       u.name as veterinary_name
                FROM {$this->table} v
                JOIN animals a ON v.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON v.administered_by = u.user_id
                WHERE v.vaccine_name LIKE :term
                OR v.vaccine_type LIKE :term
                OR v.batch_number LIKE :term
                OR a.name LIKE :term
                OR c.name LIKE :term
                ORDER BY v.vaccine_date DESC";

        return fetchAll($sql, ['term' => "%{$term}%"]);
    }

    // Get vaccine statistics
    public function getStats()
    {
        $stats = [
            'total' => $this->count(),
            'scheduled' => $this->count(['status' => 'scheduled']),
            'completed' => $this->count(['status' => 'completed']),
            'overdue' => $this->countOverdue()
        ];

        // Get vaccines this month
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE vaccine_date BETWEEN :start AND :end";
        $thisMonth = fetchOne($sql, ['start' => $startOfMonth, 'end' => $endOfMonth]);
        $stats['this_month'] = $thisMonth['count'] ?? 0;

        // Get most common vaccines
        $sql = "SELECT vaccine_name, COUNT(*) as count FROM {$this->table} 
                GROUP BY vaccine_name ORDER BY count DESC LIMIT 5";
        $stats['common_vaccines'] = fetchAll($sql);

        return $stats;
    }



    // Load vaccine data into object properties
    public function load($vaccineData)
    {
        if (is_array($vaccineData)) {
            $this->setVaccineId($vaccineData['vaccine_id'] ?? null);
            $this->setAnimalId($vaccineData['animal_id'] ?? null);
            $this->setVaccineName($vaccineData['vaccine_name'] ?? '');
            $this->setVaccineType($vaccineData['vaccine_type'] ?? '');
            $this->setVaccineDate($vaccineData['vaccine_date'] ?? null);
            $this->setNextDueDate($vaccineData['next_due_date'] ?? null);
            $this->setAdministeredBy($vaccineData['administered_by'] ?? null);
            $this->setBatchNumber($vaccineData['batch_number'] ?? '');
            $this->setManufacturer($vaccineData['manufacturer'] ?? '');
            $this->setNotes($vaccineData['notes'] ?? '');
            $this->setStatus($vaccineData['status'] ?? 'scheduled');
            $this->setCreatedAt($vaccineData['created_at'] ?? null);
            $this->setUpdatedAt($vaccineData['updated_at'] ?? null);
        }
        return $this;
    }

    // Convert object to array
    public function toArray()
    {
        return [
            'vaccine_id' => $this->vaccineId,
            'animal_id' => $this->animalId,
            'vaccine_name' => $this->vaccineName,
            'vaccine_type' => $this->vaccineType,
            'vaccine_date' => $this->vaccineDate,
            'next_due_date' => $this->nextDueDate,
            'administered_by' => $this->administeredBy,
            'batch_number' => $this->batchNumber,
            'manufacturer' => $this->manufacturer,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->nextDueDate && strtotime($this->nextDueDate) < time() && !$this->isCompleted();
    }

    public function getStatusBadge()
    {
        switch ($this->status) {
            case 'scheduled':
                return '<span class="badge bg-warning">Scheduled</span>';
            case 'completed':
                return '<span class="badge bg-success">Completed</span>';
            case 'overdue':
                return '<span class="badge bg-danger">Overdue</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }


    /**
     * Get vaccines with detailed information including animal and client details
     */
    public function getVaccinesWithDetails($filters = [])
    {
        $sql = "SELECT vd.* FROM vaccine_details_view vd WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND vd.vaccine_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['administered_by'])) {
            $sql .= " AND vd.administered_by = ?";
            $params[] = $filters['administered_by'];
        }

        if (!empty($filters['vaccine_id'])) {
            $sql .= " AND vd.vaccine_id = ?";
            $params[] = $filters['vaccine_id'];
        }

        $sql .= " ORDER BY vd.vaccine_date DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Create a new vaccination record
     */
    public function createVaccination($data)
    {
        return $this->create($data);
    }

    /**
     * Update vaccination record
     */
    public function updateVaccination($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Get vaccination statistics
     */
    public function getVaccinationStats($userRole, $userId)
    {
        $stats = [
            'total_vaccinations' => $this->count(),
            'completed_vaccinations' => $this->count(['status' => 'completed']),
            'scheduled_vaccinations' => $this->count(['status' => 'scheduled']),
            'overdue_vaccinations' => $this->countOverdue(),
            'animals_vaccinated' => $this->getUniqueAnimalsCount(),
            'upcoming_vaccinations' => $this->countUpcoming()
        ];

        return $stats;
    }

    /**
     * Count overdue vaccinations
     */
    private function countOverdue()
    {
        $sql = "SELECT COUNT(*) as count FROM vaccines 
                WHERE next_due_date < CURDATE() AND status != 'completed'";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }

    /**
     * Count unique animals vaccinated
     */
    private function getUniqueAnimalsCount()
    {
        $sql = "SELECT COUNT(DISTINCT animal_id) as count FROM vaccines WHERE status = 'completed'";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }

    /**
     * Count upcoming vaccinations
     */
    private function countUpcoming()
    {
        $sql = "SELECT COUNT(*) as count FROM vaccines 
                WHERE next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                AND status = 'completed'";
        $result = fetchOne($sql);
        return $result['count'] ?? 0;
    }

    /**
     * Get vaccination calendar events
     */
    public function getVaccinationCalendarEvents($start, $end, $userId, $userRole)
    {
        $sql = "SELECT 
                    vaccine_id as id, 
                    CONCAT(animal_name, ' - ', vaccine_name) as title, 
                    vaccine_date as start,
                    next_due_date as end,
                    vaccine_status as status,
                    animal_name,
                    vaccine_type,
                    CONCAT(animal_name, ' - ', vaccine_name, ' (', vaccine_status, ')') as description
                FROM vaccine_details_view 
                WHERE (vaccine_date BETWEEN ? AND ? OR next_due_date BETWEEN ? AND ?)";

        $params = [$start, $end, $start, $end];

        // Role-based filtering
        if ($userRole === ROLE_VETERINARY) {
            $sql .= " AND administered_by = ?";
            $params[] = $userId;
        }

        return fetchAll($sql, $params);
    }



    /**
     * Get overdue vaccinations
     */
    public function getOverdueVaccinations($veterinaryId = null)
    {
        $sql = "SELECT * FROM vaccine_details_view 
                WHERE next_due_date < CURDATE()
                AND vaccine_status = 'completed'";

        $params = [];

        if ($veterinaryId) {
            $sql .= " AND administered_by = ?";
            $params[] = $veterinaryId;
        }

        $sql .= " ORDER BY next_due_date ASC";

        return fetchAll($sql, $params);
    }



    /**
     * Get animal vaccination history
     */
    public function getAnimalVaccinationHistory($animalId)
    {
        return $this->getVaccinesByAnimal($animalId);
    }

    /**
     * Get due vaccinations for an animal
     */
    public function getDueVaccinations($animalId, $days = 30)
    {
        $sql = "SELECT * FROM vaccines 
                WHERE animal_id = ? 
                AND next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND status = 'completed'
                ORDER BY next_due_date ASC";

        return fetchAll($sql, [$animalId, $days]);
    }

    /**
     * Get vaccine protocols by species
     */
    public function getVaccineProtocols($species)
    {
        $protocols = [
            'dog' => [
                'Rabies Vaccine' => [
                    'schedule' => 'Initial at 12-16 weeks, then annually or every 3 years',
                    'dosage' => '1.0 mL',
                    'route' => 'subcutaneous'
                ],
                'DHPP Vaccine' => [
                    'schedule' => 'Every 3-4 weeks from 6-16 weeks, then annually',
                    'dosage' => '1.0 mL',
                    'route' => 'subcutaneous'
                ],
                'Bordetella Vaccine' => [
                    'schedule' => 'Annually or every 6 months for high-risk dogs',
                    'dosage' => '1.0 mL',
                    'route' => 'intranasal'
                ]
            ],
            'cat' => [
                'FVRCP Vaccine' => [
                    'schedule' => 'Every 3-4 weeks from 6-16 weeks, then annually',
                    'dosage' => '1.0 mL',
                    'route' => 'subcutaneous'
                ],
                'Rabies Vaccine' => [
                    'schedule' => 'Initial at 12-16 weeks, then annually',
                    'dosage' => '1.0 mL',
                    'route' => 'subcutaneous'
                ]
            ]
        ];

        return $protocols[strtolower($species)] ?? [];
    }

    /**
     * Get vaccination certificate data
     */
    public function getVaccinationCertificate($vaccineId)
    {
        $sql = "SELECT v.*, a.name as animal_name, a.species, a.breed, a.birth_date, a.color,
                       CONCAT(u.first_name, ' ', u.last_name) as administered_by_name,
                       u.license_number,
                       c.name as client_name, c.phone as client_phone, c.email as client_email
                FROM vaccines v
                JOIN animals a ON v.animal_id = a.animal_id
                JOIN clients cl ON a.client_id = cl.client_id
                JOIN users u ON v.administered_by = u.user_id
                JOIN users c ON cl.user_id = c.user_id
                WHERE v.vaccine_id = ?";

        return fetchOne($sql, [$vaccineId]);
    }

    /**
     * Record adverse reaction
     */
    public function recordReaction($vaccineId, $reactionNotes, $severity)
    {
        $data = [
            'reaction_notes' => $reactionNotes,
            'status' => 'reaction_reported'
        ];

        return $this->update($vaccineId, $data);
    }

    /**
     * Verify vaccination
     */
    public function verifyVaccination($vaccineId, $verifiedBy, $verificationNotes = null)
    {
        $data = [
            'verified_by' => $verifiedBy,
            'verification_date' => date('Y-m-d H:i:s'),
            'status' => 'verified'
        ];

        if ($verificationNotes) {
            $data['notes'] = $verificationNotes;
        }

        return $this->update($vaccineId, $data);
    }



    /**
     * Get due vaccinations for reminder system
     */
    public function getDueVaccinationsForReminder($daysAhead = 7)
    {
        $sql = "SELECT vd.*, u.email as client_email, u.phone as client_phone,
                       CONCAT(u.first_name, ' ', u.last_name) as client_full_name
                FROM vaccine_details_view vd
                JOIN animals a ON vd.animal_id = a.animal_id
                JOIN clients c ON a.client_id = c.client_id
                JOIN users u ON c.user_id = u.user_id
                WHERE vd.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND vd.vaccine_status = 'completed'
                ORDER BY vd.next_due_date ASC";

        return fetchAll($sql, [$daysAhead]);
    }


























}
?>