<?php
abstract class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    protected function render($viewPath, $data = [], $pageTitle = 'Blood Bridge') {
        $base_dir = ""; // Since we route through index.php at root, base_dir is empty!
        $page_title = $pageTitle;
        extract($data);
        
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/' . $viewPath . '.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    protected function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    protected function checkRole($allowedRoles) {
        $this->checkAuth();
        $role = $_SESSION['role'];
        if (is_array($allowedRoles)) {
            if (!in_array($role, $allowedRoles)) {
                header("Location: index.php?route=login&error=unauthorized");
                exit;
            }
        } else {
            if ($role !== $allowedRoles) {
                header("Location: index.php?route=login&error=unauthorized");
                exit;
            }
        }
    }

    protected function redirect($route) {
        header("Location: index.php?route=" . $route);
        exit;
    }
}
?>
