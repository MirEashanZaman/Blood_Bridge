<?php
class DonationModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function logDonation($donorId, $technicianId, $bloodType, $unitsMl, $donationDate, $notes) {
        $stmt = $this->db->prepare("
            INSERT INTO donations (donor_id, technician_id, blood_type, units_ml, donation_date, notes) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$donorId, $technicianId, $bloodType, $unitsMl, $donationDate, $notes]);
    }

    public function getLatestDonationEligibility($donorId) {
        $stmt = $this->db->prepare("SELECT MAX(next_eligible_date) FROM donations WHERE donor_id = ?");
        $stmt->execute([$donorId]);
        return $stmt->fetchColumn();
    }

    public function getDonationHistory($donorId) {
        $stmt = $this->db->prepare("
            SELECT d.*, u.name as tech_name 
            FROM donations d 
            LEFT JOIN users u ON d.technician_id = u.user_id 
            WHERE d.donor_id = ? 
            ORDER BY d.donation_date DESC
        ");
        $stmt->execute([$donorId]);
        return $stmt->fetchAll();
    }

    public function getRecentDonations($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT d.*, u.name as donor_name 
            FROM donations d 
            JOIN users u ON d.donor_id = u.user_id 
            ORDER BY d.donation_date DESC, d.donation_id DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyTrends($monthsLimit = 6) {
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(donation_date, '%M %Y') as month, COUNT(*) as count 
            FROM donations 
            WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH) 
            GROUP BY DATE_FORMAT(donation_date, '%Y-%m') 
            ORDER BY donation_date ASC
        ");
        $stmt->bindValue(1, $monthsLimit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createIntent($donorId, $intentDate) {
        $stmt = $this->db->prepare("
            INSERT INTO donation_intents (donor_id, intent_date, status) 
            VALUES (?, ?, 'pending')
        ");
        return $stmt->execute([$donorId, $intentDate]);
    }

    public function getPendingIntentsForDonor($donorId) {
        $stmt = $this->db->prepare("
            SELECT * FROM donation_intents 
            WHERE donor_id = ? AND status = 'pending' 
            ORDER BY intent_date ASC
        ");
        $stmt->execute([$donorId]);
        return $stmt->fetchAll();
    }

    public function getAllPendingIntents() {
        $stmt = $this->db->query("
            SELECT di.*, u.name as donor_name, u.blood_type 
            FROM donation_intents di 
            JOIN users u ON di.donor_id = u.user_id 
            WHERE di.status = 'pending' 
            ORDER BY di.intent_date ASC
        ");
        return $stmt->fetchAll();
    }

    public function getIntentById($intentId) {
        $stmt = $this->db->prepare("SELECT * FROM donation_intents WHERE intent_id = ?");
        $stmt->execute([$intentId]);
        return $stmt->fetch();
    }

    public function updateIntentStatus($intentId, $status) {
        $stmt = $this->db->prepare("UPDATE donation_intents SET status = ? WHERE intent_id = ?");
        return $stmt->execute([$status, $intentId]);
    }
}
?>
