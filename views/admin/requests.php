<div class="top-bar">
    <h2>Blood Request Pipeline</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-code-pull-request"></i> Active Requests</span>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Requester</th>
                    <th>Blood Type</th>
                    <th>Units</th>
                    <th>Urgency</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Available Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="9" style="text-align: center; color: var(--text-muted);">No blood requests submitted.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $req): ?>
                        <?php 
                        $bt = $req['blood_type'];
                        $stock = isset($stocks[$bt]) ? $stocks[$bt] : 0;
                        $low_stock_warning = ($stock < $req['units_needed'] && $req['status'] === 'pending');
                        ?>
                        <tr>
                            <td>#<?= $req['request_id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($req['requester_name']) ?></strong>
                                <?php if ($req['patient_id']): ?>
                                    <br><span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-user-injured"></i> Registered Patient</span>
                                <?php endif; ?>
                                <br><span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($req['requester_contact']) ?></span>
                            </td>
                            <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-size: 0.85rem; font-weight: 700;"><?= htmlspecialchars($bt) ?></span></td>
                            <td><strong><?= (int)$req['units_needed'] ?> units</strong></td>
                            <td><span class="badge badge-<?= htmlspecialchars($req['urgency']) ?>"><?= htmlspecialchars(ucfirst($req['urgency'])) ?></span></td>
                            <td><?= htmlspecialchars($req['requested_at']) ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($req['status']) ?>">
                                    <?= htmlspecialchars(ucfirst($req['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $stock >= $req['units_needed'] ? 'badge-available' : 'badge-rejected' ?>">
                                    <?= $stock ?> units
                                </span>
                                <?php if ($low_stock_warning): ?>
                                    <div style="color: var(--danger-color); font-size: 0.75rem; margin-top: 0.25rem; font-weight: 600;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Insufficient Stock!
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req['status'] === 'pending'): ?>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <form action="index.php?route=admin/requests" method="POST" onsubmit="return <?= $low_stock_warning ? "confirm('Warning: Available inventory is less than requested. Approve anyway?')" : "true" ?>;">
                                            <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background-color: var(--success-color);">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <button class="btn btn-secondary" onclick="triggerReject(<?= $req['request_id'] ?>)" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background-color: var(--danger-color);">
                                            <i class="fa-solid fa-xmark"></i> Reject
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                                        Processed by: <?= htmlspecialchars($req['processor_name'] ? $req['processor_name'] : 'System') ?>
                                        <?php if (!empty($req['notes'])): ?>
                                            <br><em>Notes: <?= htmlspecialchars($req['notes']) ?></em>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div class="panel" style="width: 100%; max-width: 450px; margin: auto; border-top: 5px solid var(--danger-color);">
        <div class="panel-header">
            <span class="panel-title">Reject Blood Request</span>
            <button onclick="closeRejectModal()" style="border: none; background: transparent; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
        <form action="index.php?route=admin/requests" method="POST">
            <input type="hidden" name="request_id" id="reject_request_id" value="">
            <input type="hidden" name="action" value="reject">
            <div class="form-group">
                <label for="rejection_reason">Rejection Reason / Notes</label>
                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="Reason for rejection..."></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background-color: var(--danger-color);">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
function triggerReject(reqId) {
    document.getElementById('reject_request_id').value = reqId;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
