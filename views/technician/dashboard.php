<div class="top-bar">
    <h2>Lab Technician Workspace</h2>
    <div>
        <span class="badge badge-normal" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-clock"></i> Shift Active</span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-title">Available Blood Stock</span>
        <span class="stat-value" style="color: var(--success-color);"><?= $available_units ?></span>
        <span class="stat-desc">Total units in inventory</span>
    </div>
    <div class="stat-card">
        <span class="stat-title">Approved to Fulfill</span>
        <span class="stat-value" style="color: var(--primary-color);"><?= $pending_fulfillment ?></span>
        <span class="stat-desc">Awaiting unit dispatch</span>
    </div>
    <div class="stat-card">
        <span class="stat-title">Donations (Last 30 Days)</span>
        <span class="stat-value" style="color: var(--secondary-color);"><?= $recent_donations_count ?></span>
        <span class="stat-desc">Intakes logged this month</span>
    </div>
</div>

<div class="dashboard-grid">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-flask"></i> Laboratory Actions</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="stat-card" style="box-shadow: none; border: 1px solid var(--border-color); text-align: center; justify-content: center; align-items: center; padding: 2rem;">
                <i class="fa-solid fa-hand-holding-medical" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <a href="index.php?route=technician/log-donation" class="btn btn-primary btn-block">Log Donation</a>
            </div>
            
            <div class="stat-card" style="box-shadow: none; border: 1px solid var(--border-color); text-align: center; justify-content: center; align-items: center; padding: 2rem;">
                <i class="fa-solid fa-boxes-stacked" style="font-size: 2.5rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                <a href="index.php?route=technician/manage-units" class="btn btn-secondary btn-block">Manage Units</a>
            </div>
            
            <div class="stat-card" style="box-shadow: none; border: 1px solid var(--border-color); text-align: center; justify-content: center; align-items: center; padding: 2rem;">
                <i class="fa-solid fa-truck-ramp-box" style="font-size: 2.5rem; color: var(--success-color); margin-bottom: 1rem;"></i>
                <a href="index.php?route=technician/fulfill-request" class="btn btn-block" style="background-color: var(--success-color); color: white;">Fulfill Request</a>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-list-check"></i> Recent Logged Donations</span>
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
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No donations logged recently.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_donations as $don): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($don['donor_name']) ?></strong></td>
                                <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($don['blood_type']) ?></span></td>
                                <td><?= htmlspecialchars($don['donation_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
