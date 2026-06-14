<div class="top-bar">
    <h2>Donor Management</h2>
</div>

<div style="margin-bottom: 1.5rem;">
    <input type="text" class="form-control live-search" data-target-table="donorTable" placeholder="Search donors by name, email, phone, blood group...">
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-users"></i> Registered Donors</span>
    </div>
    
    <div class="table-container">
        <table id="donorTable">
            <thead>
                <tr>
                    <th>Donor ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Blood Group</th>
                    <th>Account Created</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $donor_found = false;
                foreach ($donors as $donor): 
                    if ($donor['role'] !== 'donor') continue;
                    $donor_found = true;
                ?>
                    <tr>
                        <td>#<?= $donor['user_id'] ?></td>
                        <td><strong><?= htmlspecialchars($donor['name']) ?></strong></td>
                        <td><?= htmlspecialchars($donor['email']) ?></td>
                        <td><?= htmlspecialchars($donor['phone']) ?></td>
                        <td>
                            <span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-size: 0.85rem; font-weight: 700;">
                                <?= htmlspecialchars($donor['blood_type']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($donor['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$donor_found): ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No donors registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
