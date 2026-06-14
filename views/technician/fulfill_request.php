<div class="top-bar">
    <h2>Fulfill Blood Requests</h2>
    <div>
        <a href="index.php?route=technician/dashboard" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($target_request): ?>
    <div class="panel" style="max-width: 800px; margin: 0 auto 2rem auto;">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-truck-ramp-box"></i> Fulfill Request #<?= $target_request['request_id'] ?></span>
            <a href="index.php?route=technician/fulfill-request" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Back to List</a>
        </div>
        
        <div style="background-color: #f8fafc; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid var(--border-color);">
            <p><strong>Requester:</strong> <?= htmlspecialchars($target_request['requester_name']) ?></p>
            <p><strong>Blood Group Needed:</strong> <span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($target_request['blood_type']) ?></span></p>
            <p><strong>Quantity Needed:</strong> <strong><?= (int)$target_request['units_needed'] ?> units</strong></p>
            <p><strong>Urgency:</strong> <span class="badge badge-<?= htmlspecialchars($target_request['urgency']) ?>"><?= htmlspecialchars(ucfirst($target_request['urgency'])) ?></span></p>
        </div>

        <form action="index.php?route=technician/fulfill-request" method="POST" id="fulfillmentForm">
            <input type="hidden" name="request_id" value="<?= $target_request['request_id'] ?>">
            
            <h3 style="font-size: 1rem; margin-bottom: 0.75rem; color: var(--secondary-color);"><i class="fa-solid fa-square-check"></i> Select Available Units (Select exactly <?= (int)$target_request['units_needed'] ?> unit(s))</h3>
            
            <?php if (empty($available_units)): ?>
                <div class="msg msg-error">No available units of blood group <?= htmlspecialchars($target_request['blood_type']) ?> in inventory!</div>
            <?php else: ?>
                <div class="table-container" style="margin-bottom: 1.5rem;">
                    <table>
                        <thead>
                            <tr>
                                <th style="cursor: default; width: 50px;">Select</th>
                                <th>Unit ID</th>
                                <th>Expiry Date</th>
                                <th>Collected Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($available_units as $unit): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="units[]" value="<?= $unit['unit_id'] ?>" class="unit-checkbox" style="transform: scale(1.2); cursor: pointer;">
                                    </td>
                                    <td>#<?= $unit['unit_id'] ?></td>
                                    <td><?= htmlspecialchars($unit['expiry_date']) ?></td>
                                    <td><?= htmlspecialchars($unit['collected_date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <button type="submit" name="submit_fulfillment" id="dispatchBtn" class="btn btn-primary btn-block" disabled>
                    <i class="fa-solid fa-truck-flatbed"></i> Confirm & Dispatch Units
                </button>
            <?php endif; ?>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.unit-checkbox');
        const dispatchBtn = document.getElementById('dispatchBtn');
        const requiredCount = <?= (int)$target_request['units_needed'] ?>;

        function updateCheckboxStatus() {
            const checkedCount = Array.from(checkboxes).filter(c => c.checked).length;
            
            if (checkedCount === requiredCount) {
                dispatchBtn.removeAttribute('disabled');
                checkboxes.forEach(c => {
                    if (!c.checked) c.setAttribute('disabled', 'disabled');
                });
            } else {
                dispatchBtn.setAttribute('disabled', 'disabled');
                checkboxes.forEach(c => c.removeAttribute('disabled'));
            }
        }

        checkboxes.forEach(c => {
            c.addEventListener('change', updateCheckboxStatus);
        });
    });
    </script>
<?php else: ?>
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-clock-rotate-left"></i> Approved Requests (Awaiting Fulfill/Dispatch)</span>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Requester</th>
                        <th>Blood Type</th>
                        <th>Units Needed</th>
                        <th>Urgency</th>
                        <th>Approved Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($approved_requests)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">No approved requests awaiting dispatch.</td></tr>
                    <?php else: ?>
                        <?php foreach ($approved_requests as $req): ?>
                            <tr>
                                <td>#<?= $req['request_id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($req['requester_name']) ?></strong>
                                    <br><span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($req['requester_contact']) ?></span>
                                </td>
                                <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($req['blood_type']) ?></span></td>
                                <td><strong><?= (int)$req['units_needed'] ?> units</strong></td>
                                <td><span class="badge badge-<?= htmlspecialchars($req['urgency']) ?>"><?= htmlspecialchars(ucfirst($req['urgency'])) ?></span></td>
                                <td><?= htmlspecialchars($req['requested_at']) ?></td>
                                <td>
                                    <a href="index.php?route=technician/fulfill-request&fulfill_id=<?= $req['request_id'] ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background-color: var(--success-color);">
                                        <i class="fa-solid fa-truck-ramp-box"></i> Allocate & Dispatch
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
