<div style="display: flex; min-height: 100vh; width: 100%; align-items: center; justify-content: center; background-color: var(--bg-color);">
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fa-solid fa-droplet"></i> Blood Bridge</h1>
            <p>Blood Bank & Donor Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="msg msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <div class="msg msg-error">Unauthorized access. Please log in first.</div>
        <?php endif; ?>

        <form action="index.php?route=login" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="index.php?route=register">Register here</a></p>
        </div>
    </div>
</div>
