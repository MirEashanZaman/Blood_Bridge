<div class="top-bar">
    <h2>Admin Dashboard</h2>
    <div>
        <span class="badge badge-normal" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-calendar"></i> <?= date('F d, Y') ?></span>
    </div>
</div>

<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="index.php?route=admin/inventory&action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Blood Unit</a>
    <a href="index.php?route=admin/requests" class="btn btn-secondary"><i class="fa-solid fa-clipboard-list"></i> Review Requests</a>
    <a href="index.php?route=admin/users" class="btn btn-secondary"><i class="fa-solid fa-user-plus"></i> Manage Users</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-title">Total Donors</span>
        <span class="stat-value"><?= $total_donors ?></span>
        <span class="stat-desc">Registered self-service donors</span>
    </div>
    <div class="stat-card">
        <span class="stat-title">Available Units</span>
        <span class="stat-value" style="color: var(--success-color);"><?= $available_units ?></span>
        <span class="stat-desc">Ready for dispatch</span>
    </div>
    <div class="stat-card">
        <span class="stat-title">Pending Requests</span>
        <span class="stat-value" style="color: var(--warning-color);"><?= $pending_requests ?></span>
        <span class="stat-desc">Awaiting admin review</span>
    </div>
    <div class="stat-card">
        <span class="stat-title">Critical Shortages</span>
        <span class="stat-value" style="color: var(--danger-color);"><?= $critical_alerts_count ?></span>
        <span class="stat-desc">Blood types below critical threshold</span>
    </div>
</div>

<div class="dashboard-grid">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-bar"></i> Inventory Levels by Blood Type</span>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-warehouse"></i> Quick Summary</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blood_types_list as $bt): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($bt) ?></strong></td>
                            <td>
                                <span class="badge <?= $chart_data[$bt] > 5 ? 'badge-available' : 'badge-rejected' ?>">
                                    <?= $chart_data[$bt] ?> units
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-hand-holding-droplet"></i> Recent Donations</span>
            <a href="index.php?route=admin/donors" style="color: var(--primary-color); font-size: 0.85rem; text-decoration: none; font-weight: 600;">View All</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_donations)): ?>
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No donations logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_donations as $donation): ?>
                            <tr>
                                <td><?= htmlspecialchars($donation['donor_name']) ?></td>
                                <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b;"><?= htmlspecialchars($donation['blood_type']) ?></span></td>
                                <td><?= htmlspecialchars($donation['donation_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-truck-medical"></i> Recent Requests</span>
            <a href="index.php?route=admin/requests" style="color: var(--primary-color); font-size: 0.85rem; text-decoration: none; font-weight: 600;">View All</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Requester</th>
                        <th>Type</th>
                        <th>Urgency</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_requests)): ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No requests logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_requests as $req): ?>
                            <tr>
                                <td><?= htmlspecialchars($req['requester_name']) ?></td>
                                <td><span class="badge badge-normal" style="background-color: #e8eaf6; color: #3f51b5;"><?= htmlspecialchars($req['blood_type']) ?></span></td>
                                <td><span class="badge badge-<?= htmlspecialchars($req['urgency']) ?>"><?= htmlspecialchars(ucfirst($req['urgency'])) ?></span></td>
                                <td><span class="badge badge-<?= htmlspecialchars($req['status']) ?>"><?= htmlspecialchars(ucfirst($req['status'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($chart_data)) ?>,
            datasets: [{
                label: 'Available Units',
                data: <?= json_encode(array_values($chart_data)) ?>,
                backgroundColor: 'rgba(192, 57, 43, 0.7)',
                borderColor: 'rgba(192, 57, 43, 1)',
                borderWidth: 1.5,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
