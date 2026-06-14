<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/DonationModel.php';

class DonorController extends BaseController {
    private $donationModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->donationModel = new DonationModel($pdo);
    }

    public function dashboard() {
        $this->checkRole('donor');

        $donor_id = $_SESSION['user_id'];
        $next_eligible = $this->donationModel->getLatestDonationEligibility($donor_id);

        $eligible = true;
        $days_left = 0;
        if ($next_eligible) {
            $next_eligible_time = strtotime($next_eligible);
            if ($next_eligible_time > time()) {
                $eligible = false;
                $days_left = ceil(($next_eligible_time - time()) / 86400);
            }
        }

        $msg = '';
        $msg_class = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_intent'])) {
            if (!$eligible) {
                $msg = "You are currently ineligible to donate.";
                $msg_class = "msg-error";
            } else {
                $intent_date = $_POST['intent_date'];
                if (!empty($intent_date)) {
                    $msg = "Thank you! Your intent to donate on " . htmlspecialchars($intent_date) . " has been logged.";
                    $msg_class = "msg-success";
                } else {
                    $msg = "Please select a date.";
                    $msg_class = "msg-error";
                }
            }
        }

        $donations = $this->donationModel->getDonationHistory($donor_id);

        $data = [
            'msg' => $msg,
            'msg_class' => $msg_class,
            'eligible' => $eligible,
            'next_eligible' => $next_eligible,
            'days_left' => $days_left,
            'donations' => $donations
        ];

        $this->render('donor/dashboard', $data, 'Donor Portal');
    }

    public function history() {
        $this->checkRole('donor');

        $donor_id = $_SESSION['user_id'];
        $data = [
            'donations' => $this->donationModel->getDonationHistory($donor_id)
        ];

        $this->render('donor/history', $data, 'Donation History');
    }
}
?>
