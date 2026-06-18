<?php
if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'technician')) {
    require_once __DIR__ . '/../../models/InventoryModel.php';
    
    try {
        $invModel = new InventoryModel($this->pdo);
        $configs = $invModel->getAlertConfig();
        $stocks = $invModel->getAvailableCountsGrouped();
        
        $critical_alerts = [];
        $warning_alerts = [];
        
        foreach ($configs as $config) {
            $bt = $config['blood_type'];
            $count = isset($stocks[$bt]) ? $stocks[$bt] : 0;
            
            if ($count <= $config['critical_threshold']) {
                $critical_alerts[] = "<strong>$bt</strong> ($count units)";
            } elseif ($count <= $config['warning_threshold']) {
                $warning_alerts[] = "<strong>$bt</strong> ($count units)";
            }
        }
        
        echo '<div class="alert-banner-container">';
        if (!empty($critical_alerts)) {
            echo '<div class="alert-banner critical" data-bloodtype="all" data-level="critical">';
            echo '<span><i class="fa-solid fa-triangle-exclamation"></i> <strong>CRITICAL STOCK ALERT:</strong> The following blood groups are extremely low: ' . implode(', ', $critical_alerts) . '.</span>';
            echo '<button class="alert-close">&times;</button>';
            echo '</div>';
        }
        if (!empty($warning_alerts)) {
            echo '<div class="alert-banner warning" data-bloodtype="all" data-level="warning">';
            echo '<span><i class="fa-solid fa-circle-exclamation"></i> <strong>WARNING:</strong> The following blood groups are running low: ' . implode(', ', $warning_alerts) . '.</span>';
            echo '<button class="alert-close">&times;</button>';
            echo '</div>';
        }
        echo '</div>';
    } catch (Exception $e) {
        // Fail silently if database is not ready or has connection issues
    }
}
?>
