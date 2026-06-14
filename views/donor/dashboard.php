<div class="top-bar">
    <h2>Donor Self-Service Portal</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-heart-pulse"></i> Eligibility Status</span>
        </div>
        
        <div style="text-align: center; padding: 2rem 1rem;">
            <?php if ($eligible): ?>
                <div style="font-size: 4rem; color: var(--success-color); margin-bottom: 1rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 style="font-size: 1.5rem; color: var(--success-color); margin-bottom: 0.5rem;">You are Eligible to Donate!</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">It has been more than 56 days since your last donation.</p>
                
                <div style="max-width: 400px; margin: 0 auto; background-color: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <form action="index.php?route=donor/dashboard" method="POST">
                        <div class="form-group">
                            <label for="intent_date">When do you plan to visit?</label>
                            <input type="date" name="intent_date" id="intent_date" class="form-control" required value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                        </div>
                        <button type="submit" name="submit_intent" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-calendar-check"></i> Submit Donation Intent
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="font-size: 4rem; color: var(--warning-color); margin-bottom: 1rem;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3 style="font-size: 1.5rem; color: var(--warning-color); margin-bottom: 0.5rem;">Cooldown Period Active</h3>
                <p style="color: var(--text-color); font-weight: 600; margin-bottom: 0.25rem;">Next eligible date: <?= htmlspecialchars($next_eligible) ?></p>
                <p style="font-size: 1.1rem; color: var(--primary-color); font-weight: 700; margin-bottom: 1rem;"><?= $days_left ?> days remaining</p>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background-color: #f8d7da; border-radius: 6px; color: #721c24; max-width: 400px; margin-left: auto; margin-right: auto; font-size: 0.9rem;">
                    <i class="fa-solid fa-ban"></i> Donation intent form is disabled during cooldown.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-circle-info"></i> Your Profile</span>
        </div>
        <div style="line-height: 2;">
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
            <p><strong>Blood Group:</strong> <span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['blood_type']) ?></span></p>
            <p><strong>Total Donations:</strong> <strong><?= count($donations) ?></strong></p>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-history"></i> Your Donation History</span>
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
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">You have not made any donations yet.</td></tr>
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
