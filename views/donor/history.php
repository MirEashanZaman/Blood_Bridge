<div class="top-bar">
    <h2>My Donation History</h2>
    <div>
        <a href="index.php?route=donor/dashboard" class="btn btn-primary"><i class="fa-solid fa-house-user"></i> My Portal</a>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-history"></i> Logged Donations</span>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Donation ID</th>
                    <th>Date</th>
                    <th>Volume</th>
                    <th>Blood Group</th>
                    <th>Logged By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)): ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No donations logged yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($donations as $don): ?>
                        <tr>
                            <td>#<?= $don['donation_id'] ?></td>
                            <td><strong><?= htmlspecialchars($don['donation_date']) ?></strong></td>
                            <td><?= (int)$don['units_ml'] ?> ml</td>
                            <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($don['blood_type']) ?></span></td>
                            <td><?= htmlspecialchars($don['tech_name'] ? $don['tech_name'] : 'System') ?></td>
                            <td><?= htmlspecialchars($don['notes'] ? $don['notes'] : '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
