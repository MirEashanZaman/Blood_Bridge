<div class="top-bar">
    <h2>Donor Self-Service Portal</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-heart-pulse"></i> Eligibility Status</span>
        </div>
        
        <div style="text-align: center; padding: 2rem 1rem;">
            <?php if ($eligible): ?>
                <div style="font-size: 4rem; color: var(--success-color); margin-bottom: 1rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 style="font-size: 1.5rem; color: var(--success-color); margin-bottom: 0.5rem;">You are Eligible to Donate!</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">It has been more than 56 days since your last donation. Thank you for saving lives!</p>
                
                <!-- Donation Intent Form with Pre-screening -->
                <div style="max-width: 500px; margin: 0 auto; background-color: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: left;">
                    <form action="index.php?route=donor/dashboard" method="POST">
                        <h4 style="margin-bottom: 0.75rem; color: var(--secondary-color); font-size: 0.95rem; font-weight: 600;"><i class="fa-solid fa-clipboard-question"></i> Medical Pre-Screening Checklist</h4>
                        
                        <div style="font-size: 0.8rem; line-height: 1.6; margin-bottom: 1rem; color: var(--text-muted);">
                            <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem; align-items: flex-start;">
                                <input type="checkbox" class="screen-q" style="margin-top: 0.2rem;">
                                <span>I have NOT had tattoos, piercings, or acupuncture in the past 6 months.</span>
                            </div>
                            <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem; align-items: flex-start;">
                                <input type="checkbox" class="screen-q" style="margin-top: 0.2rem;">
                                <span>I have NOT taken antibiotics or consumed alcohol in the last 48 hours.</span>
                            </div>
                            <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem; align-items: flex-start;">
                                <input type="checkbox" class="screen-q" style="margin-top: 0.2rem;">
                                <span>I am NOT currently pregnant, feeling unwell, or running a fever.</span>
                            </div>
                        </div>

                        <div class="form-group" style="display: flex; gap: 0.5rem; align-items: flex-start; background-color: #fff; padding: 0.75rem; border: 1px solid #dcdde1; border-radius: 6px; margin-bottom: 1rem;">
                            <input type="checkbox" name="screening_certify" id="screening_certify" value="1" disabled style="margin-top: 0.2rem;">
                            <label for="screening_certify" style="font-size: 0.8rem; font-weight: 600; cursor: pointer; margin-bottom: 0;">I certify that I meet all basic health requirements for blood donation.</label>
                        </div>

                        <div class="form-group">
                            <label for="intent_date">Planned Donation Date</label>
                            <input type="date" name="intent_date" id="intent_date" class="form-control" required value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" disabled>
                        </div>
                        <button type="submit" name="submit_intent" id="intentSubmitBtn" class="btn btn-primary btn-block" disabled>
                            <i class="fa-solid fa-calendar-check"></i> Submit Donation Intent
                        </button>
                    </form>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const screenQs = document.querySelectorAll('.screen-q');
                    const certifyCheck = document.getElementById('screening_certify');
                    const dateInput = document.getElementById('intent_date');
                    const submitBtn = document.getElementById('intentSubmitBtn');

                    function checkScreening() {
                        const allChecked = Array.from(screenQs).every(q => q.checked);
                        if (allChecked) {
                            certifyCheck.removeAttribute('disabled');
                        } else {
                            certifyCheck.setAttribute('disabled', 'disabled');
                            certifyCheck.checked = false;
                            dateInput.setAttribute('disabled', 'disabled');
                            submitBtn.setAttribute('disabled', 'disabled');
                        }
                    }

                    screenQs.forEach(q => q.addEventListener('change', checkScreening));

                    certifyCheck.addEventListener('change', () => {
                        if (certifyCheck.checked) {
                            dateInput.removeAttribute('disabled');
                            submitBtn.removeAttribute('disabled');
                        } else {
                            dateInput.setAttribute('disabled', 'disabled');
                            submitBtn.setAttribute('disabled', 'disabled');
                        }
                    });
                });
                </script>
            <?php else: ?>
                <div style="font-size: 4rem; color: var(--warning-color); margin-bottom: 1rem;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3 style="font-size: 1.5rem; color: var(--warning-color); margin-bottom: 0.5rem;">Cooldown Period Active</h3>
                <p style="color: var(--text-color); font-weight: 600; margin-bottom: 0.25rem;">Next eligible date: <?= htmlspecialchars($next_eligible) ?></p>
                <p style="font-size: 1.1rem; color: var(--primary-color); font-weight: 700; margin-bottom: 1rem;"><?= $days_left ?> days remaining</p>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background-color: #f8d7da; border-radius: 6px; color: #721c24; max-width: 400px; margin-left: auto; margin-right: auto; font-size: 0.9rem;">
                    <i class="fa-solid fa-ban"></i> Donation intent form is disabled during cooldown.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-circle-info"></i> Your Profile</span>
        </div>
        <div style="line-height: 2;">
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
            <p><strong>Blood Group:</strong> <span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['blood_type']) ?></span></p>
            <p><strong>Total Donations:</strong> <strong><?= count($donations) ?></strong></p>
        </div>
    </div>
</div>

<!-- Upcoming Intents Panel -->
<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-calendar-check"></i> Your Upcoming Appointments</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending_intents)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No upcoming appointments scheduled.</td></tr>
                <?php else: ?>
                    <?php foreach ($pending_intents as $intent): ?>
                        <tr>
                            <td>#<?= $intent['intent_id'] ?></td>
                            <td><strong><?= htmlspecialchars($intent['intent_date']) ?></strong></td>
                            <td><span class="badge badge-pending">Pending Visit</span></td>
                            <td><?= htmlspecialchars($intent['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-history"></i> Your Donation History</span>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Donation ID</th>
                    <th>Date</th>
                    <th>Volume</th>
                    <th>Blood Group</th>
                    <th>Logged By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)): ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">You have not made any donations yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($donations as $don): ?>
                        <tr>
                            <td>#<?= $don['donation_id'] ?></td>
                            <td><strong><?= htmlspecialchars($don['donation_date']) ?></strong></td>
                            <td><?= (int)$don['units_ml'] ?> ml</td>
                            <td><span class="badge badge-normal" style="background-color: #fce4ec; color: #c2185b; font-weight: 700;"><?= htmlspecialchars($don['blood_type']) ?></span></td>
                            <td><?= htmlspecialchars($don['tech_name'] ? $don['tech_name'] : 'System') ?></td>
                            <td><?= htmlspecialchars($don['notes'] ? $don['notes'] : '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
