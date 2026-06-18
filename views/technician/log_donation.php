<div class="top-bar">
    <h2>Log Donor Donation</h2>
    <div>
        <a href="index.php?route=technician/dashboard" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 650px; margin: 0 auto;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-file-medical"></i> Donation Intake Form</span>
    </div>
    
    <form action="index.php?route=technician/log-donation" method="POST" id="donationForm">
        <?php if (!empty($selected_intent)): ?>
            <input type="hidden" name="intent_id" value="<?= (int)$selected_intent['intent_id'] ?>">
            <div class="msg msg-success" style="font-size: 0.85rem; padding: 0.5rem 1rem; margin-bottom: 1rem;">
                <i class="fa-solid fa-calendar-check"></i> Fulfilling appointment intent <strong>#<?= (int)$selected_intent['intent_id'] ?></strong>.
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="donor_select">Donor <span style="color: var(--primary-color);">*</span></label>
            <select name="donor_id" id="donor_select" class="form-control" required>
                <option value="">Select Donor...</option>
                <?php foreach ($donors as $d): 
                    if ($d['role'] !== 'donor') continue;
                    
                    $eligible = true;
                    if (!empty($d['next_eligible_date']) && strtotime($d['next_eligible_date']) > time()) {
                        $eligible = false;
                    }
                    
                    $selected = (!empty($selected_intent) && (int)$selected_intent['donor_id'] === (int)$d['user_id']) ? 'selected' : '';
                ?>
                    <option value="<?= $d['user_id'] ?>" 
                            <?= $selected ?>
                             data-bloodtype="<?= htmlspecialchars($d['blood_type'] ?? '') ?>" 
                             data-eligible="<?= $eligible ? 'true' : 'false' ?>" 
                             data-eligibledate="<?= htmlspecialchars($d['next_eligible_date'] ?? '') ?>">
                        <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['blood_type'] ?? '') ?>) 
                        <?= !$eligible ? ' - [Ineligible until ' . htmlspecialchars($d['next_eligible_date'] ?? '') . ']' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="eligibilityWarning" class="msg msg-error" style="display: none; font-weight: 600;"></div>

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
            <label for="units_ml">Volume Donated (ml) <span style="color: var(--primary-color);">*</span></label>
            <input type="number" name="units_ml" id="units_ml" class="form-control" required value="450" step="50" min="100">
            <span style="font-size: 0.8rem; color: var(--text-muted);">Standard unit size is 450ml.</span>
        </div>

        <div class="form-group">
            <label for="donation_date">Donation Date <span style="color: var(--primary-color);">*</span></label>
            <input type="date" name="donation_date" id="donation_date" class="form-control" required value="<?= !empty($selected_intent) ? htmlspecialchars($selected_intent['intent_date']) : date('Y-m-d') ?>">
        </div>

        <div class="form-group">
            <label for="notes">Lab Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Notes..."></textarea>
        </div>

        <button type="submit" name="log_donation" id="submitBtn" class="btn btn-primary btn-block">
            <i class="fa-solid fa-heart"></i> Complete Donation Intake
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const donorSelect = document.getElementById('donor_select');
    const bloodTypeSelect = document.getElementById('blood_type');
    const warningDiv = document.getElementById('eligibilityWarning');
    const submitBtn = document.getElementById('submitBtn');

    donorSelect.addEventListener('change', () => {
        const selectedOption = donorSelect.options[donorSelect.selectedIndex];
        
        if (!selectedOption.value) {
            warningDiv.style.display = 'none';
            submitBtn.removeAttribute('disabled');
            return;
        }

        const bloodType = selectedOption.getAttribute('data-bloodtype');
        const eligible = selectedOption.getAttribute('data-eligible');
        const eligibleDate = selectedOption.getAttribute('data-eligibledate');

        if (bloodType) {
            bloodTypeSelect.value = bloodType;
        }

        if (eligible === 'false') {
            warningDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> <strong>CRITICAL COOLDOWN ALERT:</strong> This donor is currently ineligible. Next donation eligibility date is <strong>${eligibleDate}</strong>.`;
            warningDiv.style.display = 'block';
            submitBtn.setAttribute('disabled', 'disabled');
        } else {
            warningDiv.style.display = 'none';
            submitBtn.removeAttribute('disabled');
        }
    });

    // Auto-trigger on page load if pre-selected
    if (donorSelect.value) {
        donorSelect.dispatchEvent(new Event('change'));
    }
});
</script>
