<div class="top-bar">
    <h2>Reports & Analytics</h2>
    <div>
        <a href="index.php?route=admin/reports&export=inventory" class="btn btn-secondary"><i class="fa-solid fa-file-csv"></i> Export Inventory to CSV</a>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-line"></i> Donation Trends (Last 6 Months)</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-pie"></i> Available Stock Distribution</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="distributionChart"></canvas>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-simple"></i> Fulfill vs Reject Ratio</span>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="ratioChart"></canvas>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-hourglass-half"></i> Units Expiring in Next 7 Days</span>
        <a href="index.php?route=admin/reports&export=expiry" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-download"></i> Export Expiry CSV</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>Blood Type</th>
                    <th>Collected Date</th>
                    <th>Expiry Date</th>
                    <th>Logged By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expiring_units)): ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No blood units expiring within the next 7 days.</td></tr>
                <?php else: ?>
                    <?php foreach ($expiring_units as $unit): ?>
                        <tr>
                            <td>#<?= $unit['unit_id'] ?></td>
                            <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($unit['blood_type']) ?></span></td>
                            <td><?= htmlspecialchars($unit['collected_date']) ?></td>
                            <td style="color: var(--warning-color); font-weight: 600;"><?= htmlspecialchars($unit['expiry_date']) ?></td>
                            <td><?= htmlspecialchars($unit['tech_name'] ? $unit['tech_name'] : 'System') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('trendsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Donations',
                data: <?= json_encode($donation_counts) ?>,
                borderColor: 'rgba(192, 57, 43, 1)',
                backgroundColor: 'rgba(192, 57, 43, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('distributionChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($dist_types) ?>,
            datasets: [{
                data: <?= json_encode($dist_counts) ?>,
                backgroundColor: [
                    '#e74c3c', '#3498db', '#2ecc71', '#f1c40f',
                    '#9b59b6', '#34495e', '#1abc9c', '#e67e22'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('ratioChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Fulfilled', 'Rejected'],
            datasets: [{
                data: [<?= (int)$fulfilled_count ?>, <?= (int)$rejected_count ?>],
                backgroundColor: ['#2ecc71', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
