<div class="top-bar">
    <h2>Patient Self-Service Portal</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <div>
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title"><i class="fa-solid fa-square-plus"></i> Submit Blood Request</span>
            </div>
            
            <form action="index.php?route=patient/dashboard" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="blood_type">Needed Blood Group <span style="color: var(--primary-color);">*</span></label>
                        <select name="blood_type" id="blood_type" class="form-control" required>
                            <option value="">Select Group</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                                <option value="<?= $bt ?>" <?= $current_user['blood_type'] === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="units_needed">Units Required <span style="color: var(--primary-color);">*</span></label>
                        <input type="number" name="units_needed" id="units_needed" class="form-control" min="1" max="10" required value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="urgency">Urgency Level <span style="color: var(--primary-color);">*</span></label>
                    <select name="urgency" id="urgency" class="form-control" required>
                        <option value="normal">Normal Priority</option>
                        <option value="urgent">Urgent</option>
                        <option value="critical">Critical / Life Threatening</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Reason / Clinical Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" required placeholder="Reason..."></textarea>
                </div>

                <button type="submit" name="submit_request" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title"><i class="fa-solid fa-list-check"></i> Track My Requests</span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Blood Type</th>
                            <th>Units</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                            <th>Rejection Reason / Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">You have not submitted any blood requests yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td>#<?= $req['request_id'] ?></td>
                                    <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($req['blood_type']) ?></span></td>
                                    <td><strong><?= (int)$req['units_needed'] ?> units</strong></td>
                                    <td><span class="badge badge-<?= htmlspecialchars($req['urgency']) ?>"><?= htmlspecialchars(ucfirst($req['urgency'])) ?></span></td>
                                    <td>
                                        <span class="badge badge-<?= htmlspecialchars($req['status']) ?>">
                                            <?= htmlspecialchars(ucfirst($req['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($req['requested_at']) ?></td>
                                    <td>
                                        <?php if ($req['status'] === 'rejected' && !empty($req['notes'])): ?>
                                            <span style="color: var(--danger-color); font-weight: 500;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($req['notes']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-style: italic;">None</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title"><i class="fa-solid fa-warehouse"></i> Available Blood Stock</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Available Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $bt => $count): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($bt) ?></strong></td>
                                <td>
                                    <span class="badge <?= $count > 5 ? 'badge-available' : ($count > 0 ? 'badge-warning' : 'badge-rejected') ?>">
                                        <?= $count ?> units
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <span class="panel-title"><i class="fa-solid fa-user-pen"></i> Update Profile</span>
            </div>
            
            <form action="index.php?route=patient/dashboard" method="POST">
                <div class="form-group">
                    <label>Blood Type (Needed)</label>
                    <input type="text" class="form-control" disabled value="<?= htmlspecialchars($current_user['blood_type']) ?>">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Contact Admin to modify blood type needed.</span>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($current_user['phone']) ?>">
                </div>

                <div style="background-color: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid var(--border-color); margin-bottom: 1.25rem;">
                    <h4 style="margin-bottom: 0.5rem; color: var(--secondary-color); font-size: 0.85rem;"><i class="fa-solid fa-lock"></i> Change Password</h4>
                    <div class="form-group">
                        <label for="old_password" style="font-size: 0.8rem;">Current Password</label>
                        <input type="password" name="old_password" id="old_password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label for="new_password" style="font-size: 0.8rem;">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••" minlength="6">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="confirm_password" style="font-size: 0.8rem;">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" minlength="6">
                    </div>
                </div>

                <button type="submit" name="update_profile" class="btn btn-secondary btn-block">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
