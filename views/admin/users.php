<div class="top-bar">
    <h2>User Management</h2>
    <div>
        <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
            <a href="index.php?route=admin/users" class="btn btn-primary"><i class="fa-solid fa-list"></i> View All Users</a>
        <?php else: ?>
            <a href="index.php?route=admin/users&action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create User</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="msg <?= $msg_class ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
    <div class="panel" style="max-width: 600px; margin: 0 auto;">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-user-plus"></i> Create User Account</span>
        </div>
        <form action="index.php?route=admin/users&action=add" method="POST">
            <div class="form-group">
                <label for="name">Full Name <span style="color: var(--primary-color);">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Jane Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address <span style="color: var(--primary-color);">*</span></label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="jane@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: var(--primary-color);">*</span></label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••" minlength="6">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number <span style="color: var(--primary-color);">*</span></label>
                <input type="text" name="phone" id="phone" class="form-control" required placeholder="017XXXXXXXX">
            </div>

            <div class="form-group">
                <label for="role">Role <span style="color: var(--primary-color);">*</span></label>
                <select name="role" id="role_select" class="form-control" required>
                    <option value="technician">Lab Technician</option>
                    <option value="admin">Administrator</option>
                    <option value="donor">Donor</option>
                    <option value="patient">Patient</option>
                </select>
            </div>

            <div class="form-group" id="blood_type_group">
                <label for="blood_type">Blood Type</label>
                <select name="blood_type" id="blood_type" class="form-control">
                    <option value="">N/A</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt): ?>
                        <option value="<?= $bt ?>"><?= $bt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="location_group" style="display: none;">
                <label for="hospital_location">Hospital / Home Location (Patient Only)</label>
                <input type="text" name="hospital_location" id="hospital_location" class="form-control" placeholder="e.g. Apollo Hospital">
            </div>

            <button type="submit" name="add_user" class="btn btn-primary btn-block">
                <i class="fa-solid fa-save"></i> Save User
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('role_select');
        const locationGroup = document.getElementById('location_group');

        function toggleFields() {
            if (roleSelect.value === 'patient') {
                locationGroup.style.display = 'block';
            } else {
                locationGroup.style.display = 'none';
            }
        }
        roleSelect.addEventListener('change', toggleFields);
    });
    </script>
<?php else: ?>
    <div style="margin-bottom: 1.5rem;">
        <input type="text" class="form-control live-search" data-target-table="usersTable" placeholder="Search users by name, email, phone, role...">
    </div>

    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-users-gear"></i> System Accounts</span>
        </div>
        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Blood Type</th>
                        <th>Location</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?= $u['user_id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td>
                                <span class="badge" style="background-color: 
                                    <?= $u['role'] === 'admin' ? '#ebf5fb; color: #2e86c1;' : 
                                       ($u['role'] === 'technician' ? '#f4ecf7; color: #884ea0;' : 
                                       ($u['role'] === 'donor' ? '#fdefe8; color: #e67e22;' : '#e8f8f5; color: #117a65;')) ?>;">
                                    <?= htmlspecialchars(ucfirst($u['role'])) ?>
                                </span>
                            </td>
                            <td><?= $u['blood_type'] ? htmlspecialchars($u['blood_type']) : '<span style="color: var(--text-muted);">N/A</span>' ?></td>
                            <td><?= $u['hospital_location'] ? htmlspecialchars($u['hospital_location']) : '<span style="color: var(--text-muted); font-style: italic;">None</span>' ?></td>
                            <td><?= htmlspecialchars($u['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
