<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/RequestModel.php';

class HomeController extends BaseController {
    private $inventoryModel;
    private $userModel;
    private $requestModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->inventoryModel = new InventoryModel($pdo);
        $this->userModel = new UserModel($pdo);
        $this->requestModel = new RequestModel($pdo);
    }

    public function index() {
        // Fetch stats
        $total_donors = $this->userModel->getTotalDonorsCount();
        $available_units = $this->inventoryModel->getTotalAvailableCount();
        
        // Fetch recent active blood requests
        $recent_requests = $this->requestModel->getRecentRequests(3);

        $data = [
            'total_donors' => $total_donors,
            'available_units' => $available_units,
            'recent_requests' => $recent_requests,
            'body_class' => 'public-body'
        ];

        $this->render('home', $data, 'Blood Bridge - Connecting Donors and Patients');
    }
}
?>
