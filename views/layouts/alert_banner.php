<?php
if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'technician')) {
    require_once __DIR__ . '/../../models/InventoryModel.php';
    
    try {
        $invModel = new InventoryModel($this->pdo);
        $configs = $invModel->getAlertConfig();
        $stocks = $invModel->getAvailableCountsGrouped();
        
        echo '<div class="alert-banner-container">';
        foreach ($configs as $config) {
            $bt = $config['blood_type'];
            $count = isset($stocks[$bt]) ? $stocks[$bt] : 0;
            
            if ($count <= $config['critical_threshold']) {
                echo '<div class="alert-banner critical" data-bloodtype="'.htmlspecialchars($bt).'" data-level="critical">';
                echo '<span><i class="fa-solid fa-triangle-exclamation"></i> <strong>CRITICAL STOCK:</strong> Blood type ' . htmlspecialchars($bt) . ' is extremely low! Only ' . $count . ' units available.</span>';
                echo '<button class="alert-close">&times;</button>';
                echo '</div>';
            } elseif ($count <= $config['warning_threshold']) {
                echo '<div class="alert-banner warning" data-bloodtype="'.htmlspecialchars($bt).'" data-level="warning">';
                echo '<span><i class="fa-solid fa-circle-exclamation"></i> <strong>WARNING:</strong> Blood type ' . htmlspecialchars($bt) . ' stock is low. ' . $count . ' units available.</span>';
                echo '<button class="alert-close">&times;</button>';
                echo '</div>';
            }
        }
        echo '</div>';
    } catch (Exception $e) {
        // Fail silently if database is not ready or has connection issues
    }
}
?>
