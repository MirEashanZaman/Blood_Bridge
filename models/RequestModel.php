<?php
class RequestModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function createRequest($requesterName, $requesterContact, $bloodType, $unitsNeeded, $urgency, $patientId = null, $notes = null) {
        $stmt = $this->db->prepare("
            INSERT INTO blood_requests (requester_name, requester_contact, blood_type, units_needed, urgency, status, patient_id, notes) 
            VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)
        ");
        return $stmt->execute([$requesterName, $requesterContact, $bloodType, $unitsNeeded, $urgency, $patientId, $notes]);
    }

    public function getAllRequests() {
        $stmt = $this->db->query("
            SELECT r.*, u.name as processor_name, p.name as patient_name 
            FROM blood_requests r 
            LEFT JOIN users u ON r.processed_by = u.user_id 
            LEFT JOIN users p ON r.patient_id = p.user_id 
            ORDER BY r.urgency = 'critical' DESC, r.urgency = 'urgent' DESC, r.requested_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getRequestsByPatient($patientId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name as processor_name 
            FROM blood_requests r 
            LEFT JOIN users u ON r.processed_by = u.user_id 
            WHERE r.patient_id = ? 
            ORDER BY r.requested_at DESC
        ");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll();
    }

    public function getRequestById($requestId) {
        $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE request_id = ?");
        $stmt->execute([$requestId]);
        return $stmt->fetch();
    }

    public function approveRequest($requestId, $processedBy) {
        $stmt = $this->db->prepare("UPDATE blood_requests SET status = 'approved', processed_by = ? WHERE request_id = ? AND status = 'pending'");
        return $stmt->execute([$processedBy, $requestId]);
    }

    public function rejectRequest($requestId, $processedBy, $notes) {
        $stmt = $this->db->prepare("UPDATE blood_requests SET status = 'rejected', processed_by = ?, notes = ? WHERE request_id = ? AND status = 'pending'");
        return $stmt->execute([$processedBy, $notes, $requestId]);
    }

    public function fulfillRequest($requestId) {
        $stmt = $this->db->prepare("UPDATE blood_requests SET status = 'fulfilled' WHERE request_id = ?");
        return $stmt->execute([$requestId]);
    }

    public function getPendingRequestsCount() {
        return $this->db->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'pending'")->fetchColumn();
    }

    public function getApprovedRequests() {
        $stmt = $this->db->query("
            SELECT r.*, p.name as patient_name 
            FROM blood_requests r 
            LEFT JOIN users p ON r.patient_id = p.user_id 
            WHERE r.status = 'approved' 
            ORDER BY r.urgency = 'critical' DESC, r.urgency = 'urgent' DESC, r.requested_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getRecentRequests($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM blood_requests 
            ORDER BY requested_at DESC, request_id DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFulfillRatio() {
        $fulfilled = $this->db->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'fulfilled'")->fetchColumn();
        $rejected = $this->db->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'rejected'")->fetchColumn();
        return [
            'fulfilled' => (int)$fulfilled,
            'rejected' => (int)$rejected
        ];
    }
}
?>
