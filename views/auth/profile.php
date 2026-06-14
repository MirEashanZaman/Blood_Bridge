<div class="top-bar">
    <h2>My Profile</h2>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-user-gear"></i> Edit Profile Details</span>
    </div>
    
    <form action="index.php?route=profile" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" class="form-control" disabled value="<?= htmlspecialchars($user['email']) ?>">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Email addresses cannot be modified.</span>
        </div>

        <div class="form-group">
            <label>Access Role</label>
            <input type="text" class="form-control" disabled value="<?= htmlspecialchars(ucfirst($user['role'])) ?>">
        </div>

        <?php if ($user['blood_type']): ?>
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" class="form-control" disabled value="<?= htmlspecialchars($user['blood_type']) ?>">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Blood group changes require administrator review.</span>
            </div>
        <?php endif; ?>

        <?php if ($user['hospital_location']): ?>
            <div class="form-group">
                <label>Hospital / Home Location</label>
                <input type="text" class="form-control" disabled value="<?= htmlspecialchars($user['hospital_location']) ?>">
            </div>
        <?php endif; ?>

        <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid var(--border-color);">

        <div class="form-group">
            <label for="name">Full Name <span style="color: var(--primary-color);">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required value="<?= htmlspecialchars($user['name']) ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number <span style="color: var(--primary-color);">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control" required value="<?= htmlspecialchars($user['phone']) ?>">
        </div>

        <div style="background-color: #f8fafc; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
            <h4 style="margin-bottom: 0.75rem; color: var(--secondary-color); font-size: 0.95rem;"><i class="fa-solid fa-lock"></i> Change Password (optional)</h4>
            <div class="form-group">
                <label for="old_password">Current Password</label>
                <input type="password" name="old_password" id="old_password" class="form-control" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••" minlength="6">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" minlength="6">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
        </button>
    </form>
</div>
