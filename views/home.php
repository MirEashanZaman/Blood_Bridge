<header class="public-navbar">
    <a href="index.php?route=home" class="logo">
        <i class="fa-solid fa-droplet"></i> Blood Bridge
    </a>
    <nav class="nav-links">
        <a href="index.php?route=home"><i class="fa-solid fa-house"></i> Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
            $dashboard_route = 'login';
            if (isset($_SESSION['user_role'])) {
                $dashboard_route = $_SESSION['user_role'] . '/dashboard';
            }
            ?>
            <a href="index.php?route=<?= $dashboard_route ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-gauge"></i> Go to Dashboard</a>
        <?php else: ?>
            <a href="index.php?route=login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <a href="index.php?route=register" class="btn btn-primary" style="padding: 0.5rem 1rem; color: white;"><i class="fa-solid fa-user-plus"></i> Register</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero-section">
    <div class="hero-content">
        <h1>Connecting Donors, Saving Lives</h1>
        <p>Blood Bridge is a modern platform that brings together blood donors, medical technicians, and patients in need of immediate blood support. Register today to contribute to your community.</p>
        <div class="hero-ctas">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?route=<?= $dashboard_route ?>" class="btn btn-primary"><i class="fa-solid fa-gauge"></i> Access Dashboard</a>
            <?php else: ?>
                <a href="index.php?route=register" class="btn btn-primary"><i class="fa-solid fa-heart"></i> Register to Donate</a>
                <a href="index.php?route=login" class="btn btn-secondary" style="background-color: transparent; border: 2px solid white; color: white;"><i class="fa-solid fa-droplet"></i> Request Blood</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="public-container">
    <!-- Statistics Band -->
    <div class="stats-band">
        <div class="stats-card-public">
            <i class="fa-solid fa-users"></i>
            <div class="number"><?= number_format($total_donors) ?></div>
            <div class="label">Registered Donors</div>
        </div>
        <div class="stats-card-public">
            <i class="fa-solid fa-cubes"></i>
            <div class="number"><?= number_format($available_units) ?></div>
            <div class="label">Available Blood Units</div>
        </div>
        <div class="stats-card-public">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <div class="number">24/7</div>
            <div class="label">Emergency Support</div>
        </div>
    </div>

    <!-- Active Urgent Requests Section -->
    <h2 class="section-title-public">Recent Blood Requests</h2>
    
    <div class="requests-grid-public">
        <?php if (empty($recent_requests)): ?>
            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 2rem;">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; color: var(--success-color); margin-bottom: 1rem;"></i>
                <p>No active emergency blood requests at the moment. All requests fulfilled!</p>
            </div>
        <?php else: ?>
            <?php foreach ($recent_requests as $req): ?>
                <div class="request-card-public <?= htmlspecialchars($req['urgency']) ?>">
                    <div class="request-card-header">
                        <span class="blood-badge"><?= htmlspecialchars($req['blood_type']) ?></span>
                        <span class="badge badge-<?= htmlspecialchars($req['urgency']) ?>"><?= htmlspecialchars(ucfirst($req['urgency'])) ?></span>
                    </div>
                    <div class="request-details">
                        <p><i class="fa-solid fa-user"></i> <strong>Name:</strong> <?= htmlspecialchars($req['requester_name']) ?></p>
                        <p><i class="fa-solid fa-location-dot"></i> <strong>Needed:</strong> <?= (int)$req['units_needed'] ?> unit(s)</p>
                        <p><i class="fa-solid fa-clock"></i> <strong>Requested:</strong> <?= date('F j, Y', strtotime($req['requested_at'])) ?></p>
                        <?php if (!empty($req['notes'])): ?>
                            <p style="font-style: italic; font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem; background-color: #F8FAFC; padding: 0.5rem; border-radius: 4px;">
                                <?= htmlspecialchars($req['notes']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="index.php?route=login" class="btn btn-secondary btn-block" style="font-size: 0.8rem; padding: 0.5rem;"><i class="fa-solid fa-right-to-bracket"></i> Login to Help</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="public-footer">
    <div class="social-icons">
        <a href="#"><i class="fa-brands fa-facebook"></i></a>
        <a href="#"><i class="fa-brands fa-twitter"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-github"></i></a>
    </div>
    <p>&copy; <?= date('Y') ?> Blood Bridge. All rights reserved. Connecting hearts to save lives.</p>
</footer>
