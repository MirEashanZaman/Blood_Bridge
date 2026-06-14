<div style="display: flex; min-height: 100vh; width: 100%; align-items: center; justify-content: center; background-color: var(--bg-color); padding: 2rem 0;">
    <div class="auth-container" style="max-width: 500px;">
        <div class="auth-header">
            <h1><i class="fa-solid fa-droplet"></i> Blood Bridge Registration</h1>
            <p>Join as a Donor or Patient</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="msg msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="msg msg-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="index.php?route=register" method="POST" id="regForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" required placeholder="017XXXXXXXX">
            </div>

            <div class="form-group">
                <label for="role">Register As</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="donor">Donor (I want to donate blood)</option>
                    <option value="patient">Patient (I need blood)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="blood_type" id="bloodTypeLabel">Your Blood Type</label>
                <select name="blood_type" id="blood_type" class="form-control" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="form-group" id="locationGroup" style="display: none;">
                <label for="hospital_location">Hospital / Home Location</label>
                <input type="text" name="hospital_location" id="hospital_location" class="form-control" placeholder="e.g. Dhaka Medical College Hospital">
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-user-plus"></i> Register
            </button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="index.php?route=login">Login here</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('role');
    const bloodTypeLabel = document.getElementById('bloodTypeLabel');
    const locationGroup = document.getElementById('locationGroup');
    const locationInput = document.getElementById('hospital_location');

    function toggleRoleFields() {
        if (roleSelect.value === 'patient') {
            bloodTypeLabel.textContent = 'Required Blood Type';
            locationGroup.style.display = 'block';
            locationInput.setAttribute('required', 'required');
        } else {
            bloodTypeLabel.textContent = 'Your Blood Type';
            locationGroup.style.display = 'none';
            locationInput.removeAttribute('required');
        }
    }

    roleSelect.addEventListener('change', toggleRoleFields);
    toggleRoleFields();
});
</script>
