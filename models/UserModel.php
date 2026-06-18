<?php
class UserModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createUser($name, $email, $passwordHash, $role, $bloodType, $phone, $hospitalLocation = null) {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, blood_type, phone, hospital_location) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $email, $passwordHash, $role, $bloodType, $phone, $hospitalLocation]);
    }

    public function getAllUsers() {
        $stmt = $this->db->query("
            SELECT u.*, (SELECT MAX(d.next_eligible_date) FROM donations d WHERE d.donor_id = u.user_id) as next_eligible_date 
            FROM users u 
            ORDER BY u.role ASC, u.name ASC
        ");
        return $stmt->fetchAll();
    }

    public function getTotalDonorsCount() {
        return $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'donor'")->fetchColumn();
    }

    public function updateProfile($userId, $name, $phone, $passwordHash = null) {
        if ($passwordHash) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, phone = ?, password = ? WHERE user_id = ?");
            return $stmt->execute([$name, $phone, $passwordHash, $userId]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, phone = ? WHERE user_id = ?");
            return $stmt->execute([$name, $phone, $userId]);
        }
    }
}
?>
