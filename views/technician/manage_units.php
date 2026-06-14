<div class="top-bar">
    <h2>Manage Blood Inventory Units</h2>
    <div>
        <a href="index.php?route=technician/manage-units&action=flag_expired" class="btn btn-secondary" style="background-color: #7f8c8d;"><i class="fa-solid fa-clock"></i> Auto-Flag Expired Units</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="margin-bottom: 1.5rem;">
    <input type="text" class="form-control live-search" data-target-table="techInventoryTable" placeholder="Search blood units by ID, type, status, or donor name...">
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-boxes-stacked"></i> All Stock Units</span>
    </div>
    
    <div class="table-container">
        <table id="techInventoryTable">
            <thead>
                <tr>
                    <th>Unit ID</th>
                    <th>Blood Group</th>
                    <th>Status</th>
                    <th>Collected Date</th>
                    <th>Expiry Date</th>
                    <th>Donor Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($units)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">No blood units registered.</td></tr>
                <?php else: ?>
                    <?php foreach ($units as $u): 
                        $is_expired = strtotime($u['expiry_date']) < time();
                    ?>
                        <tr>
                            <td>#<?= $u['unit_id'] ?></td>
                            <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700; font-size: 0.85rem;"><?= htmlspecialchars($u['blood_type']) ?></span></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($u['status']) ?>">
                                    <?= htmlspecialchars(ucfirst($u['status'])) ?>
                                </span>
                                <?php if ($is_expired && $u['status'] !== 'expired'): ?>
                                    <span style="color: var(--danger-color); font-size: 0.75rem; font-weight: 600; display: block; margin-top: 0.25rem;">
                                        <i class="fa-solid fa-clock"></i> Expired!
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($u['collected_date']) ?></td>
                            <td style="<?= $is_expired ? 'color: var(--danger-color); font-weight: 600;' : '' ?>"><?= htmlspecialchars($u['expiry_date']) ?></td>
                            <td>
                                <?php if ($u['source_donation_id']): ?>
                                    <?= htmlspecialchars($u['donor_name']) ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-style: italic;">Standalone</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <?php if ($u['status'] !== 'available'): ?>
                                        <a href="index.php?route=technician/manage-units&action=available&unit_id=<?= $u['unit_id'] ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background-color: var(--success-color);">
                                            Make Avail
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($u['status'] !== 'expired'): ?>
                                        <a href="index.php?route=technician/manage-units&action=expired&unit_id=<?= $u['unit_id'] ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background-color: var(--danger-color);">
                                            Expire
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u['status'] !== 'dispatched'): ?>
                                        <a href="index.php?route=technician/manage-units&action=dispatched&unit_id=<?= $u['unit_id'] ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background-color: #2980b9;">
                                            Dispatch
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
