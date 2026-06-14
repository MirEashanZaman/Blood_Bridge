<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect if already logged in
        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            $this->redirectToDashboard($_SESSION['role']);
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (!empty($email) && !empty($password)) {
                $user = $this->userModel->getUserByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['blood_type'] = $user['blood_type'];

                    $this->redirectToDashboard($user['role']);
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Please fill in all fields.';
            }
        }

        $this->render('auth/login', ['error' => $error], 'Login');
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
            $this->redirectToDashboard($_SESSION['role']);
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $blood_type = $_POST['blood_type'];
            $phone = trim($_POST['phone']);
            $hospital_location = isset($_POST['hospital_location']) ? trim($_POST['hospital_location']) : null;

            if (!empty($name) && !empty($email) && !empty($password) && !empty($role) && !empty($blood_type) && !empty($phone)) {
                if ($role !== 'donor' && $role !== 'patient') {
                    $error = 'Invalid role selected.';
                } else {
                    if ($this->userModel->getUserByEmail($email)) {
                        $error = 'Email is already registered.';
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                        $this->userModel->createUser($name, $email, $hashed_password, $role, $blood_type, $phone, $hospital_location);
                        $success = 'Registration successful! You can now log in.';
                    }
                }
            } else {
                $error = 'Please fill in all required fields.';
            }
        }

        $this->render('auth/register', ['error' => $error, 'success' => $success], 'Register');
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $this->redirect('login');
    }

    public function profile() {
        $this->checkAuth();
        $userId = $_SESSION['user_id'];
        
        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $phone = trim($_POST['phone']);
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (!empty($name) && !empty($phone)) {
                try {
                    $passwordHash = null;
                    
                    // Check if password change is attempted
                    if (!empty($old_password) || !empty($new_password) || !empty($confirm_password)) {
                        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                            throw new Exception("All password fields (Current, New, and Confirm) are required to change your password.");
                        }
                        
                        $currentUser = $this->userModel->getUserById($userId);
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
                    
                    $this->userModel->updateProfile($userId, $name, $phone, $passwordHash);
                    
                    // Update session variable instantly
                    $_SESSION['name'] = $name;
                    
                    $msg = "Profile updated successfully.";
                    $msg_class = "msg-success";
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $msg_class = "msg-error";
                }
            } else {
                $msg = "Name and Phone fields are required.";
                $msg_class = "msg-error";
            }
        }

        $user = $this->userModel->getUserById($userId);
        $this->render('auth/profile', [
            'user' => $user,
            'msg' => $msg,
            'msg_class' => $msg_class
        ], 'My Profile');
    }

    private function redirectToDashboard($role) {
        switch ($role) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'technician':
                $this->redirect('technician/dashboard');
                break;
            case 'donor':
                $this->redirect('donor/dashboard');
                break;
            case 'patient':
                $this->redirect('patient/dashboard');
                break;
        }
    }
}
?>
