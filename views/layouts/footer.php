<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php if (isset($_SESSION['user_id'])): ?>
    </div><!-- End main-content -->
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
