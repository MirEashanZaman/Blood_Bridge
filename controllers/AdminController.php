<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DonationModel.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../models/RequestModel.php';

class AdminController extends BaseController {
    private $userModel;
    private $donationModel;
    private $inventoryModel;
    private $requestModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->donationModel = new DonationModel($pdo);
        $this->inventoryModel = new InventoryModel($pdo);
        $this->requestModel = new RequestModel($pdo);
    }

    public function dashboard() {
        $this->checkRole('admin');

        $data = [
            'total_donors' => $this->userModel->getTotalDonorsCount(),
            'available_units' => $this->inventoryModel->getTotalAvailableCount(),
            'pending_requests' => $this->requestModel->getPendingRequestsCount(),
            'critical_alerts_count' => 0,
            'chart_data' => [],
            'blood_types_list' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'recent_donations' => $this->donationModel->getRecentDonations(10),
            'recent_requests' => $this->requestModel->getRecentRequests(10)
        ];

        // Process stock levels & alert configuration
        foreach ($data['blood_types_list'] as $bt) {
            $data['chart_data'][$bt] = 0;
        }

        $configs = $this->inventoryModel->getAlertConfig();
        $stocks = $this->inventoryModel->getAvailableCountsGrouped();
        foreach ($configs as $cfg) {
            $bt = $cfg['blood_type'];
            $count = isset($stocks[$bt]) ? $stocks[$bt] : 0;
            $data['chart_data'][$bt] = $count;
            if ($count <= (int)$cfg['critical_threshold']) {
                $data['critical_alerts_count']++;
            }
        }

        $this->render('admin/dashboard', $data, 'Admin Dashboard');
    }

    public function inventory() {
        $this->checkRole('admin');
        
        $msg = '';
        $msg_class = '';

        // Auto Flag Expired Action
        if (isset($_GET['action']) && $_GET['action'] === 'flag_expired') {
            $count = $this->inventoryModel->flagExpiredUnits();
            $msg = "Successfully flagged $count expired unit(s).";
            $msg_class = "msg-success";
        }

        // Add blood unit POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_unit'])) {
            $blood_type = $_POST['blood_type'];
            $collected_date = $_POST['collected_date'];
            $source_donation_id = !empty($_POST['source_donation_id']) ? (int)$_POST['source_donation_id'] : null;
            $status = $_POST['status'];

            if (!empty($blood_type) && !empty($collected_date) && !empty($status)) {
                $this->inventoryModel->addUnit($blood_type, $source_donation_id, $status, $collected_date, $_SESSION['user_id']);
                $msg = "Blood unit successfully added to inventory.";
                $msg_class = "msg-success";
            } else {
                $msg = "Please fill in all required fields.";
                $msg_class = "msg-error";
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'inventory_units' => $this->inventoryModel->getAllUnits(),
            'donations_dropdown' => $this->donationModel->getRecentDonations(50)
        ];

        $this->render('admin/inventory', $data, 'Blood Inventory');
    }

    public function requests() {
        $this->checkRole('admin');

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $request_id = (int)$_POST['request_id'];
            $action = $_POST['action'];

            if ($action === 'approve') {
                $this->requestModel->approveRequest($request_id, $_SESSION['user_id']);
                $msg = "Request #$request_id approved successfully.";
                $msg_class = "msg-success";
            } elseif ($action === 'reject') {
                $notes = trim($_POST['rejection_reason']);
                if (!empty($notes)) {
                    $this->requestModel->rejectRequest($request_id, $_SESSION['user_id'], $notes);
                    $msg = "Request #$request_id rejected successfully.";
                    $msg_class = "msg-success";
                } else {
                    $msg = "Please provide a rejection reason.";
                    $msg_class = "msg-error";
                }
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'stocks' => $this->inventoryModel->getAvailableCountsGrouped(),
            'requests' => $this->requestModel->getAllRequests()
        ];

        $this->render('admin/requests', $data, 'Manage Requests');
    }

    public function donors() {
        $this->checkRole('admin');

        $data = [
            'donors' => $this->userModel->getAllUsers() // The view filters for role = donor
        ];

        $this->render('admin/donors', $data, 'Manage Donors');
    }

    public function users() {
        $this->checkRole('admin');

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $blood_type = !empty($_POST['blood_type']) ? $_POST['blood_type'] : null;
            $phone = trim($_POST['phone']);
            $hospital_location = !empty($_POST['hospital_location']) ? trim($_POST['hospital_location']) : null;

            if (!empty($name) && !empty($email) && !empty($password) && !empty($role) && !empty($phone)) {
                if ($this->userModel->getUserByEmail($email)) {
                    $msg = "Email address is already in use.";
                    $msg_class = "msg-error";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $this->userModel->createUser($name, $email, $hashed_password, $role, $blood_type, $phone, $hospital_location);
                    $msg = "User account successfully created.";
                    $msg_class = "msg-success";
                }
            } else {
                $msg = "Please fill in all required fields.";
                $msg_class = "msg-error";
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'users' => $this->userModel->getAllUsers()
        ];

        $this->render('admin/users', $data, 'Manage Users');
    }

    public function reports() {
        $this->checkRole('admin');

        // Handle CSV exports before rendering layout
        if (isset($_GET['export'])) {
            $export_type = $_GET['export'];
            
            if ($export_type === 'expiry') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=expiring_units_' . date('Ymd') . '.csv');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Unit ID', 'Blood Type', 'Status', 'Collected Date', 'Expiry Date']);
                $units = $this->inventoryModel->getUnitsExpiringSoon(7);
                foreach ($units as $u) {
                    fputcsv($output, [$u['unit_id'], $u['blood_type'], $u['status'], $u['collected_date'], $u['expiry_date']]);
                }
                fclose($output);
                exit;
            } elseif ($export_type === 'inventory') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=blood_inventory_report_' . date('Ymd') . '.csv');
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Unit ID', 'Blood Type', 'Status', 'Collected Date', 'Expiry Date']);
                $units = $this->inventoryModel->getAllUnits(['status' => 'available']);
                foreach ($units as $u) {
                    fputcsv($output, [$u['unit_id'], $u['blood_type'], $u['status'], $u['collected_date'], $u['expiry_date']]);
                }
                fclose($output);
                exit;
            }
        }

        // Fetch data for charts
        $trends = $this->donationModel->getMonthlyTrends(6);
        $months = [];
        $donation_counts = [];
        foreach ($trends as $t) {
            $months[] = $t['month'];
            $donation_counts[] = (int)$t['count'];
        }

        $stocks = $this->inventoryModel->getAvailableCountsGrouped();
        $dist_types = array_keys($stocks);
        $dist_counts = array_values($stocks);

        $ratio = $this->requestModel->getFulfillRatio();

        $data = [
            'months' => $months,
            'donation_counts' => $donation_counts,
            'dist_types' => $dist_types,
            'dist_counts' => $dist_counts,
            'fulfilled_count' => $ratio['fulfilled'],
            'rejected_count' => $ratio['rejected'],
            'expiring_units' => $this->inventoryModel->getUnitsExpiringSoon(7)
        ];

        $this->render('admin/reports', $data, 'Reports & Analytics');
    }

    public function settings() {
        $this->checkRole('admin');

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
            $criticals = $_POST['critical'];
            $warnings = $_POST['warning'];
            
            try {
                $this->pdo->beginTransaction();
                foreach ($criticals as $bt => $crit_val) {
                    $warn_val = $warnings[$bt];
                    $this->inventoryModel->updateAlertConfig($bt, $crit_val, $warn_val);
                }
                $this->pdo->commit();
                $msg = "Alert configuration updated successfully.";
                $msg_class = "msg-success";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                $msg = "Error: " . $e->getMessage();
                $msg_class = "msg-error";
            }
        }

        $configs = $this->inventoryModel->getAlertConfig();
        $config_map = [];
        foreach ($configs as $cfg) {
            $config_map[$cfg['blood_type']] = [
                'critical' => $cfg['critical_threshold'],
                'warning' => $cfg['warning_threshold']
            ];
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'config_map' => $config_map,
            'blood_types_list' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
        ];

        $this->render('admin/settings', $data, 'Alert Settings');
    }
}
?>
