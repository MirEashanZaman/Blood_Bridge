<div class="top-bar">
    <h2>My Blood Requests</h2>
    <div>
        <a href="index.php?route=patient/request-blood" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Request Blood</a>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-list-check"></i> Request Tracking</span>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Blood Group</th>
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
