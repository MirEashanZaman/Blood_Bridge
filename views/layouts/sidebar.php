<?php
$current_route = isset($_GET['route']) ? $_GET['route'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-droplet" style="color: var(--primary-color);"></i>
        <span>Blood Bridge</span>
    </div>
    
    <ul class="sidebar-menu">
        <?php if ($role === 'admin'): ?>
            <li class="<?= $current_route === 'admin/dashboard' ? 'active' : '' ?>">
                <a href="index.php?route=admin/dashboard">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="<?= $current_route === 'admin/inventory' ? 'active' : '' ?>">
                <a href="index.php?route=admin/inventory">
                    <i class="fa-solid fa-warehouse"></i> Blood Inventory
                </a>
            </li>
            <li class="<?= $current_route === 'admin/requests' ? 'active' : '' ?>">
                <a href="index.php?route=admin/requests">
                    <i class="fa-solid fa-paper-plane"></i> Blood Requests
                </a>
            </li>
            <li class="<?= $current_route === 'admin/donors' ? 'active' : '' ?>">
                <a href="index.php?route=admin/donors">
                    <i class="fa-solid fa-users"></i> Donors
                </a>
            </li>
            <li class="<?= $current_route === 'admin/users' ? 'active' : '' ?>">
                <a href="index.php?route=admin/users">
                    <i class="fa-solid fa-user-gear"></i> Manage Users
                </a>
            </li>
            <li class="<?= $current_route === 'admin/reports' ? 'active' : '' ?>">
                <a href="index.php?route=admin/reports">
                    <i class="fa-solid fa-file-contract"></i> Reports & Analytics
                </a>
            </li>
            <li class="<?= $current_route === 'admin/settings' ? 'active' : '' ?>">
                <a href="index.php?route=admin/settings">
                    <i class="fa-solid fa-sliders"></i> Settings
                </a>
            </li>
        <?php elseif ($role === 'technician'): ?>
            <li class="<?= $current_route === 'technician/dashboard' ? 'active' : '' ?>">
                <a href="index.php?route=technician/dashboard">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="<?= $current_route === 'technician/log-donation' ? 'active' : '' ?>">
                <a href="index.php?route=technician/log-donation">
                    <i class="fa-solid fa-hand-holding-medical"></i> Log Donation
                </a>
            </li>
            <li class="<?= $current_route === 'technician/manage-units' ? 'active' : '' ?>">
                <a href="index.php?route=technician/manage-units">
                    <i class="fa-solid fa-boxes-stacked"></i> Manage Units
                </a>
            </li>
            <li class="<?= $current_route === 'technician/fulfill-request' ? 'active' : '' ?>">
                <a href="index.php?route=technician/fulfill-request">
                    <i class="fa-solid fa-truck-ramp-box"></i> Fulfill Request
                </a>
            </li>
        <?php elseif ($role === 'donor'): ?>
            <li class="<?= $current_route === 'donor/dashboard' ? 'active' : '' ?>">
                <a href="index.php?route=donor/dashboard">
                    <i class="fa-solid fa-heart-pulse"></i> My Portal
                </a>
            </li>
            <li class="<?= $current_route === 'donor/history' ? 'active' : '' ?>">
                <a href="index.php?route=donor/history">
                    <i class="fa-solid fa-history"></i> Donation History
                </a>
            </li>
        <?php elseif ($role === 'patient'): ?>
            <li class="<?= $current_route === 'patient/dashboard' ? 'active' : '' ?>">
                <a href="index.php?route=patient/dashboard">
                    <i class="fa-solid fa-house-medical"></i> My Portal
                </a>
            </li>
            <li class="<?= $current_route === 'patient/request-blood' ? 'active' : '' ?>">
                <a href="index.php?route=patient/request-blood">
                    <i class="fa-solid fa-droplet"></i> Request Blood
                </a>
            </li>
            <li class="<?= $current_route === 'patient/my-requests' ? 'active' : '' ?>">
                <a href="index.php?route=patient/my-requests">
                    <i class="fa-solid fa-list-check"></i> My Requests
                </a>
            </li>
        <?php endif; ?>
    </ul>
    
    <div class="sidebar-user" style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-start;">
        <a href="index.php?route=profile" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-weight: 500; padding: 0;">
            <i class="fa-solid fa-user-circle" style="font-size: 1.15rem;"></i> <?= htmlspecialchars($user_name) ?>
        </a>
        <a href="index.php?route=logout" style="color: #E74C3C; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; padding: 0; margin-top: 0.25rem;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>
