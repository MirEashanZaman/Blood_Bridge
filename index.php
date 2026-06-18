<?php
// Set session config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configurations
require_once 'config/db.php';

// Load controllers
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/TechnicianController.php';
require_once 'controllers/DonorController.php';
require_once 'controllers/PatientController.php';
require_once 'controllers/HomeController.php';

// Route resolution
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

try {
    switch ($route) {
        case 'home':
            $controller = new HomeController($pdo);
            $controller->index();
            break;
            
        case 'login':
            $controller = new AuthController($pdo);
            $controller->login();
            break;
            
        case 'register':
            $controller = new AuthController($pdo);
            $controller->register();
            break;
            
        case 'logout':
            $controller = new AuthController($pdo);
            $controller->logout();
            break;
            
        case 'profile':
            $controller = new AuthController($pdo);
            $controller->profile();
            break;
            
        case 'admin/dashboard':
            $controller = new AdminController($pdo);
            $controller->dashboard();
            break;
            
        case 'admin/inventory':
            $controller = new AdminController($pdo);
            $controller->inventory();
            break;
            
        case 'admin/requests':
            $controller = new AdminController($pdo);
            $controller->requests();
            break;
            
        case 'admin/donors':
            $controller = new AdminController($pdo);
            $controller->donors();
            break;
            
        case 'admin/users':
            $controller = new AdminController($pdo);
            $controller->users();
            break;
            
        case 'admin/reports':
            $controller = new AdminController($pdo);
            $controller->reports();
            break;
            
        case 'admin/settings':
            $controller = new AdminController($pdo);
            $controller->settings();
            break;
            
        case 'technician/dashboard':
            $controller = new TechnicianController($pdo);
            $controller->dashboard();
            break;
            
        case 'technician/log-donation':
            $controller = new TechnicianController($pdo);
            $controller->logDonation();
            break;
            
        case 'technician/manage-units':
            $controller = new TechnicianController($pdo);
            $controller->manageUnits();
            break;
            
        case 'technician/fulfill-request':
            $controller = new TechnicianController($pdo);
            $controller->fulfillRequest();
            break;
            
        case 'donor/dashboard':
            $controller = new DonorController($pdo);
            $controller->dashboard();
            break;
            
        case 'donor/history':
            $controller = new DonorController($pdo);
            $controller->history();
            break;
            
        case 'patient/dashboard':
            $controller = new PatientController($pdo);
            $controller->dashboard();
            break;
            
        case 'patient/request-blood':
            $controller = new PatientController($pdo);
            $controller->requestBlood();
            break;
            
        case 'patient/my-requests':
            $controller = new PatientController($pdo);
            $controller->myRequests();
            break;

        default:
            // Route not found, redirect to login
            header("HTTP/1.0 404 Not Found");
            header("Location: index.php?route=login");
            exit;
    }
} catch (Exception $e) {
    echo "<h2>Application Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
