<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DonationModel.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../models/RequestModel.php';

class TechnicianController extends BaseController {
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
        $this->checkRole('technician');

        $data = [
            'available_units' => $this->inventoryModel->getTotalAvailableCount(),
            'pending_fulfillment' => count($this->requestModel->getApprovedRequests()),
            'recent_donations_count' => count($this->donationModel->getRecentDonations(30)),
            'recent_donations' => $this->donationModel->getRecentDonations(5),
            'pending_intents' => $this->donationModel->getAllPendingIntents()
        ];

        $this->render('technician/dashboard', $data, 'Technician Workspace');
    }

    public function logDonation() {
        $this->checkRole('technician');

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_donation'])) {
            $donor_id = (int)$_POST['donor_id'];
            $blood_type = $_POST['blood_type'];
            $units_ml = (int)$_POST['units_ml'];
            $donation_date = $_POST['donation_date'];
            $notes = trim($_POST['notes']);
            $intent_id = !empty($_POST['intent_id']) ? (int)$_POST['intent_id'] : null;

            if (!empty($donor_id) && !empty($blood_type) && !empty($units_ml) && !empty($donation_date)) {
                $next_eligible = $this->donationModel->getLatestDonationEligibility($donor_id);

                if ($next_eligible && strtotime($next_eligible) > strtotime($donation_date)) {
                    $msg = "Error: Donor is ineligible on selected date. Next eligibility is: $next_eligible";
                    $msg_class = "msg-error";
                } else {
                    try {
                        $this->pdo->beginTransaction();
                        
                        // 1. Log Donation
                        $this->donationModel->logDonation($donor_id, $_SESSION['user_id'], $blood_type, $units_ml, $donation_date, $notes);
                        $new_donation_id = $this->pdo->lastInsertId();

                        // 2. Add to Inventory
                        $this->inventoryModel->addUnit($blood_type, $new_donation_id, 'available', $donation_date, $_SESSION['user_id']);

                        // 3. Mark intent completed if applicable
                        if ($intent_id) {
                            $this->donationModel->updateIntentStatus($intent_id, 'completed');
                        }

                        $this->pdo->commit();
                        $msg = "Donation logged and blood unit auto-created in inventory successfully.";
                        $msg_class = "msg-success";
                    } catch (Exception $e) {
                        $this->pdo->rollBack();
                        $msg = "Error: " . $e->getMessage();
                        $msg_class = "msg-error";
                    }
                }
            } else {
                $msg = "Please fill in all required fields.";
                $msg_class = "msg-error";
            }
        }

        // Fetch pre-selected intent if passed
        $selected_intent = null;
        if (isset($_GET['intent_id'])) {
            $intent_id = (int)$_GET['intent_id'];
            $selected_intent = $this->donationModel->getIntentById($intent_id);
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'donors' => $this->userModel->getAllUsers(),
            'selected_intent' => $selected_intent
        ];

        $this->render('technician/log_donation', $data, 'Log Donation');
    }

    public function manageUnits() {
        $this->checkRole('technician');

        $msg = '';
        $msg_class = '';

        if (isset($_GET['action']) && isset($_GET['unit_id'])) {
            $unit_id = (int)$_GET['unit_id'];
            $action = $_GET['action'];
            
            if (in_array($action, ['available', 'expired', 'dispatched'])) {
                $this->inventoryModel->updateUnitStatus($unit_id, $action);
                $msg = "Unit #$unit_id updated to " . ucfirst($action) . " successfully.";
                $msg_class = "msg-success";
            }
        }

        if (isset($_GET['action']) && $_GET['action'] === 'flag_expired') {
            $count = $this->inventoryModel->flagExpiredUnits();
            $msg = "Successfully flagged $count expired unit(s).";
            $msg_class = "msg-success";
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'units' => $this->inventoryModel->getAllUnits()
        ];

        $this->render('technician/manage_units', $data, 'Manage Units');
    }

    public function fulfillRequest() {
        $this->checkRole('technician');

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_fulfillment'])) {
            $request_id = (int)$_POST['request_id'];
            $selected_units = isset($_POST['units']) ? $_POST['units'] : [];

            $request = $this->requestModel->getRequestById($request_id);

            if (!$request) {
                $msg = "Error: Request not found.";
                $msg_class = "msg-error";
            } elseif (count($selected_units) !== (int)$request['units_needed']) {
                $msg = "Error: You must select exactly " . $request['units_needed'] . " unit(s).";
                $msg_class = "msg-error";
            } else {
                try {
                    $this->pdo->beginTransaction();
                    
                    // Dispatch selected units
                    $this->inventoryModel->dispatchUnits($selected_units);
                    
                    // Fulfill request
                    $this->requestModel->fulfillRequest($request_id);

                    $this->pdo->commit();
                    $msg = "Request #$request_id successfully fulfilled.";
                    $msg_class = "msg-success";
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    $msg = "Fulfillment failed: " . $e->getMessage();
                    $msg_class = "msg-error";
                }
            }
        }

        $target_request = null;
        $available_units = [];
        if (isset($_GET['fulfill_id'])) {
            $fulfill_id = (int)$_GET['fulfill_id'];
            $target_request = $this->requestModel->getRequestById($fulfill_id);
            if ($target_request) {
                $available_units = $this->inventoryModel->getAvailableUnitsByType($target_request['blood_type']);
            }
        }

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'approved_requests' => $this->requestModel->getApprovedRequests(),
            'target_request' => $target_request,
            'available_units' => $available_units
        ];

        $this->render('technician/fulfill_request', $data, 'Fulfill Requests');
    }
}
?>
