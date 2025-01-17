<?php
// Get the current page's file name or URL
$current_page = basename($_SERVER['PHP_SELF']); // Get the current file name
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="profile">
        <h1>IMS</h1>
        <img src="<?= $profile_pic_path ?>" alt="User Image">
        <p>Hello <?= htmlspecialchars($user['first_name']) ?></p>
    </div>
    <ul>
        <li><a href="userDashboard.php" class="<?= $current_page == 'userDashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="itemDashboard.php" class="<?= $current_page == 'itemDashboard.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Items Management</a></li>
        <li><a href="orderManagement.php" class="<?= $current_page == 'orderManagement.php' ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Order Management</a></li>
        <li><a href="supplier.php" class="<?= $current_page == 'supplier.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> Supplier Management</a></li>
        <li><a href="profile-config.php" class="<?= $current_page == 'profile-config.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Profile Configuration</a></li>
        <li><a href="stats.php" class="<?= $current_page == 'stats.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Stats</a></li>
    </ul>
</div>
