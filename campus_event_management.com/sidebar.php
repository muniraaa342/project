<div class="sidebar">
    <a href="index.php">Dashboard</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin.php">Admin Panel</a>
    <?php endif; ?>
    <a href="logout.php" class="logout">Logout</a>
</div>
