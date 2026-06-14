<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../models/RequestModel.php';

class PatientController extends BaseController {
    private $userModel;
    private $inventoryModel;
    private $requestModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->inventoryModel = new InventoryModel($pdo);
        $this->requestModel = new RequestModel($pdo);
    }

    public function dashboard() {
        $this->checkRole('patient');

        $msg = '';
        $msg_class = '';
        $patient_id = $_SESSION['user_id'];

        // Profile Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $phone = trim($_POST['phone']);
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (!empty($phone)) {
                try {
                    $passwordHash = null;
                    
                    // Check if password change is attempted
                    if (!empty($old_password) || !empty($new_password) || !empty($confirm_password)) {
                        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                            throw new Exception("All password fields (Current, New, and Confirm) are required to change your password.");
                        }
                        
                        $currentUser = $this->userModel->getUserById($patient_id);
                        if (!password_verify($old_password, $currentUser['password'])) {
                            throw new Exception("Invalid current password entered.");
                        }
                        
                        if ($new_password !== $confirm_password) {
                            throw new Exception("The new password and confirmation password do not match.");
                        }
                        
                        if (strlen($new_password) < 6) {
                            throw new Exception("The new password must be at least 6 characters long.");
                        }
                        
                        $passwordHash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                    }
                    
                    // Fetch user's current name to keep it unchanged
                    $currentUser = $this->userModel->getUserById($patient_id);
                    $this->userModel->updateProfile($patient_id, $currentUser['name'], $phone, $passwordHash);
                    
                    $msg = "Profile updated successfully.";
                    $msg_class = "msg-success";
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $msg_class = "msg-error";
                }
            } else {
                $msg = "Phone number is required.";
                $msg_class = "msg-error";
            }
        }

        // Blood Request Submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
            $blood_type = $_POST['blood_type'];
            $units_needed = (int)$_POST['units_needed'];
            $urgency = $_POST['urgency'];
            $notes = trim($_POST['notes']);

            $user = $this->userModel->getUserById($patient_id);

            if (!empty($blood_type) && !empty($units_needed) && !empty($urgency)) {
                $this->requestModel->createRequest($user['name'], $user['phone'], $blood_type, $units_needed, $urgency, $patient_id, $notes);
                $msg = "Blood request submitted successfully.";
                $msg_class = "msg-success";
            } else {
                $msg = "Please fill in all required fields.";
                $msg_class = "msg-error";
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'stocks' => $this->inventoryModel->getAvailableCountsGrouped(),
            'requests' => $this->requestModel->getRequestsByPatient($patient_id),
            'current_user' => $this->userModel->getUserById($patient_id)
        ];

        $this->render('patient/dashboard', $data, 'Patient Portal');
    }

    public function requestBlood() {
        $this->checkRole('patient');

        $msg = '';
        $msg_class = '';
        $patient_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
            $blood_type = $_POST['blood_type'];
            $units_needed = (int)$_POST['units_needed'];
            $urgency = $_POST['urgency'];
            $notes = trim($_POST['notes']);

            $user = $this->userModel->getUserById($patient_id);

            if (!empty($blood_type) && !empty($units_needed) && !empty($urgency)) {
                $this->requestModel->createRequest($user['name'], $user['phone'], $blood_type, $units_needed, $urgency, $patient_id, $notes);
                $msg = "Blood request submitted successfully.";
                $msg_class = "msg-success";
            } else {
                $msg = "Please fill in all required fields.";
                $msg_class = "msg-error";
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'current_user' => $this->userModel->getUserById($patient_id)
        ];

        $this->render('patient/request_blood', $data, 'Request Blood');
    }

    public function myRequests() {
        $this->checkRole('patient');

        $patient_id = $_SESSION['user_id'];
        $data = [
            'requests' => $this->requestModel->getRequestsByPatient($patient_id)
        ];

        $this->render('patient/my_requests', $data, 'My Requests');
    }
}
?>
