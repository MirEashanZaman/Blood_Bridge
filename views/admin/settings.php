<div class="top-bar">
    <h2>Inventory Stock Alert Settings</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 800px;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-sliders"></i> Stock Threshold Configurations</span>
    </div>
    
    <form action="index.php?route=admin/settings" method="POST">
        <div class="table-container" style="margin-bottom: 1.5rem;">
            <table>
                <thead>
                    <tr>
                        <th>Blood Group</th>
                        <th>Critical Threshold (Red Alert)</th>
                        <th>Warning Threshold (Amber Alert)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blood_types_list as $bt): ?>
                        <tr>
                            <td>
                                <strong><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-size: 0.95rem; font-weight: 700; padding: 0.4rem 0.8rem;"><?= htmlspecialchars($bt) ?></span></strong>
                            </td>
                            <td>
                                <input type="number" name="critical[<?= htmlspecialchars($bt) ?>]" class="form-control" style="max-width: 150px;" min="0" required value="<?= (int)$config_map[$bt]['critical'] ?>">
                            </td>
                            <td>
                                <input type="number" name="warning[<?= htmlspecialchars($bt) ?>]" class="form-control" style="max-width: 150px;" min="0" required value="<?= (int)$config_map[$bt]['warning'] ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <button type="submit" name="save_settings" class="btn btn-primary">
            <i class="fa-solid fa-save"></i> Save Threshold Settings
        </button>
    </form>
</div>
