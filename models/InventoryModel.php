<?php
class InventoryModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function addUnit($bloodType, $sourceDonationId, $status, $collectedDate, $storedBy) {
        $stmt = $this->db->prepare("
            INSERT INTO blood_inventory (blood_type, source_donation_id, status, collected_date, stored_by) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$bloodType, $sourceDonationId, $status, $collectedDate, $storedBy]);
    }

    public function getAllUnits($filters = []) {
        $query = "
            SELECT bi.*, u.name as technician_name, d.donation_date, donor.name as donor_name 
            FROM blood_inventory bi 
            LEFT JOIN users u ON bi.stored_by = u.user_id 
            LEFT JOIN donations d ON bi.source_donation_id = d.donation_id 
            LEFT JOIN users donor ON d.donor_id = donor.user_id 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['blood_type'])) {
            $query .= " AND bi.blood_type = ?";
            $params[] = $filters['blood_type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND bi.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['expiry'])) {
            if ($filters['expiry'] === 'expired') {
                $query .= " AND bi.expiry_date < CURDATE()";
            } elseif ($filters['expiry'] === 'expiring_soon') {
                $query .= " AND bi.expiry_date >= CURDATE() AND bi.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
            }
        }

        $query .= " ORDER BY bi.expiry_date ASC, bi.unit_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateUnitStatus($unitId, $status) {
        $stmt = $this->db->prepare("UPDATE blood_inventory SET status = ? WHERE unit_id = ?");
        return $stmt->execute([$status, $unitId]);
    }

    public function flagExpiredUnits() {
        $stmt = $this->db->prepare("UPDATE blood_inventory SET status = 'expired' WHERE expiry_date < CURDATE() AND status IN ('available', 'reserved')");
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getAvailableCountsGrouped() {
        $stmt = $this->db->query("SELECT blood_type, COUNT(*) as count FROM blood_inventory WHERE status = 'available' GROUP BY blood_type");
        $counts = [];
        foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt) {
            $counts[$bt] = 0;
        }
        while ($row = $stmt->fetch()) {
            $counts[$row['blood_type']] = (int)$row['count'];
        }
        return $counts;
    }

    public function getTotalAvailableCount() {
        return $this->db->query("SELECT COUNT(*) FROM blood_inventory WHERE status = 'available'")->fetchColumn();
    }

    public function getAlertConfig() {
        return $this->db->query("SELECT * FROM alert_config")->fetchAll();
    }

    public function updateAlertConfig($bloodType, $critical, $warning) {
        $stmt = $this->db->prepare("
            INSERT INTO alert_config (blood_type, critical_threshold, warning_threshold) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE critical_threshold = VALUES(critical_threshold), warning_threshold = VALUES(warning_threshold)
        ");
        return $stmt->execute([$bloodType, (int)$critical, (int)$warning]);
    }

    public function getUnitsExpiringSoon($days = 7) {
        $stmt = $this->db->prepare("
            SELECT bi.*, u.name as tech_name 
            FROM blood_inventory bi
            LEFT JOIN users u ON bi.stored_by = u.user_id 
            WHERE bi.expiry_date >= CURDATE() AND bi.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND bi.status = 'available'
            ORDER BY bi.expiry_date ASC
        ");
        $stmt->bindValue(1, $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableUnitsByType($bloodType) {
        $stmt = $this->db->prepare("
            SELECT bi.*, d.donation_date 
            FROM blood_inventory bi 
            LEFT JOIN donations d ON bi.source_donation_id = d.donation_id 
            WHERE bi.blood_type = ? AND bi.status = 'available'
            ORDER BY bi.expiry_date ASC
        ");
        $stmt->execute([$bloodType]);
        return $stmt->fetchAll();
    }

    public function dispatchUnits($unitIds) {
        $placeholders = implode(',', array_fill(0, count($unitIds), '?'));
        $stmt = $this->db->prepare("UPDATE blood_inventory SET status = 'dispatched' WHERE unit_id IN ($placeholders) AND status = 'available'");
        return $stmt->execute($unitIds);
    }
}
?>
