<div class="top-bar">
    <h2>Blood Inventory Management</h2>
    <div>
        <a href="index.php?route=admin/inventory&action=flag_expired" class="btn btn-secondary" style="background-color: #7f8c8d;"><i class="fa-solid fa-clock"></i> Auto-Flag Expired Units</a>
        <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
            <a href="index.php?route=admin/inventory" class="btn btn-primary"><i class="fa-solid fa-list"></i> View Inventory</a>
        <?php else: ?>
            <a href="index.php?route=admin/inventory&action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Blood Unit</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
    <div class="panel" style="max-width: 600px; margin: 0 auto;">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-square-plus"></i> Add New Blood Unit</span>
        </div>
        <form action="index.php?route=admin/inventory&action=add" method="POST">
            <div class="form-group">
                <label for="blood_type">Blood Type <span style="color: var(--primary-color);">*</span></label>
                <select name="blood_type" id="blood_type" class="form-control" required>
                    <option value="">Select Blood Group</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                        <option value="<?= $bt ?>"><?= $bt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="source_donation_id">Source Donation (Optional)</label>
                <select name="source_donation_id" id="source_donation_id" class="form-control">
                    <option value="">Standalone / No Linked Donation</option>
                    <?php foreach ($donations_dropdown as $don): ?>
                        <option value="<?= $don['donation_id'] ?>">
                            ID: <?= $don['donation_id'] ?> | <?= htmlspecialchars($don['donor_name']) ?> (<?= $don['blood_type'] ?>) - <?= $don['donation_date'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="collected_date">Collected Date <span style="color: var(--primary-color);">*</span></label>
                <input type="date" name="collected_date" id="collected_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="status">Initial Status <span style="color: var(--primary-color);">*</span></label>
                <select name="status" id="status" class="form-control" required>
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            <button type="submit" name="add_unit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-save"></i> Save Blood Unit
            </button>
        </form>
    </div>
<?php else: ?>
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-filter"></i> Real-time Filters</span>
        </div>
        <form id="liveFilterForm" data-target-table="inventoryTable" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;" onsubmit="return false;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="filter_blood_type">Blood Type</label>
                <select id="filter_blood_type" class="form-control">
                    <option value="">All Blood Groups</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                        <option value="<?= $bt ?>"><?= $bt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="filter_status">Status</label>
                <select id="filter_status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach (['available', 'reserved', 'dispatched', 'expired'] as $st): ?>
                        <option value="<?= $st ?>"><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="filter_expiry">Expiry Status</label>
                <select id="filter_expiry" class="form-control">
                    <option value="">All Units</option>
                    <option value="expired">Expired Units</option>
                    <option value="expiring_soon">Expiring in 7 Days</option>
                </select>
            </div>

            <div>
                <button type="button" id="resetFiltersBtn" class="btn btn-secondary btn-block" style="background-color: #bdc3c7; color: #333;"><i class="fa-solid fa-rotate-left"></i> Reset Filters</button>
            </div>
        </form>
    </div>

    <div style="margin-bottom: 1rem;">
        <input type="text" class="form-control live-search" data-target-table="inventoryTable" placeholder="Type here to search inventory list...">
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-list-check"></i> Stock Units</span>
        </div>
        <div class="table-container">
            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>Unit ID</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                        <th>Collected Date</th>
                        <th>Expiry Date</th>
                        <th>Donor</th>
                        <th>Stored By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inventory_units)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">No blood units found matching the criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inventory_units as $unit): ?>
                            <tr data-blood-type="<?= htmlspecialchars($unit['blood_type']) ?>" data-status="<?= htmlspecialchars($unit['status']) ?>" data-expiry-date="<?= htmlspecialchars($unit['expiry_date']) ?>">
                                <td>#<?= $unit['unit_id'] ?></td>
                                <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-size: 0.85rem; font-weight: 700;"><?= htmlspecialchars($unit['blood_type']) ?></span></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($unit['status']) ?>">
                                        <?= htmlspecialchars(ucfirst($unit['status'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($unit['collected_date']) ?></td>
                                <td>
                                    <?php 
                                    $is_expired = strtotime($unit['expiry_date']) < time();
                                    $style = $is_expired ? 'color: var(--danger-color); font-weight: 600;' : '';
                                    ?>
                                    <span style="<?= $style ?>"><?= htmlspecialchars($unit['expiry_date']) ?></span>
                                </td>
                                <td>
                                    <?php if ($unit['source_donation_id']): ?>
                                        <?= htmlspecialchars($unit['donor_name']) ?> (Donation #<?= $unit['source_donation_id'] ?>)
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-style: italic;">Standalone Intake</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($unit['technician_name'] ? $unit['technician_name'] : 'System') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
