<div class="top-bar">
    <h2>Request Blood</h2>
    <div>
        <a href="index.php?route=patient/dashboard" class="btn btn-secondary"><i class="fa-solid fa-house-user"></i> My Portal</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-paper-plane"></i> Submit Request</span>
    </div>
    
    <form action="index.php?route=patient/request-blood" method="POST">
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
            <textarea name="notes" id="notes" class="form-control" rows="4" required placeholder="Reason..."></textarea>
        </div>

        <button type="submit" name="submit_request" class="btn btn-primary btn-block">
            <i class="fa-solid fa-save"></i> Submit Request
        </button>
    </form>
</div>
