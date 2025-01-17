<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
include('database/connection.php');

// Assign the session user to a variable
$user = $_SESSION['user'];
$users = include('database/show-users.php');

// checkInventory.php returns an array of low-stock items for PHP to use
$lowStockItems = include('checkInventory.php');
// Fetch suppliers from the supplier table
$sql = "SELECT supplier_id, supplier_name FROM suppliers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
include './database/userData.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./CSS/userDashboard.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <title>User Dashboard</title>
</head>

<body>
    <div id="main-container">
        <!-- Sidebar -->
        <?php include './partials/sidebar.php';?>

        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>User Dashboard</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>
            <div class="content-area">
                <h2>User Main Content</h2>

                <div class="low-stock-notification">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        <?php 
                        if (!empty($lowStockItems) && is_array($lowStockItems)) {
                            echo '<p>There are ' . count($lowStockItems) . ' items with low stock found.</p>';
                        } else {
                            echo '<p>No low stock items found.</p>';
                        }
                        ?>
                    </span>
                    <button onclick="closeNotification(this)"><i class="fas fa-times"></i></button>
                </div>
                <div class="low-stock-list" id="notification-container">
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const userId = <?= json_encode($user['id']); ?>;

        fetch('checkInventory.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'user_id': userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                displayLowStockNotification(data.items);
            } else {
                console.log(data.message || 'No items with low stock found.');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    function displayLowStockNotification(items) {
        const notificationContainer = document.getElementById('notification-container');
        notificationContainer.innerHTML = '<h3>Low Stock Items</h3>';

        items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'item';

            itemDiv.innerHTML = `
                <p><strong>Item:</strong> ${item.item_name}</p>
                <p><strong>Quantity:</strong> ${item.quantity}</p>
                <button class="order-button" onclick="orderItem(${item.item_id})">Order Now</button>
            `;

            notificationContainer.appendChild(itemDiv);
        });
    }

    function orderItem(itemId) {
        // Redirect to the orderManagement.php page with the item ID as a query parameter
        window.location.href = `orderManagement.php?item_id=${itemId}`;
    }

    function closeNotification(button) {
        const notification = button.parentElement;
        notification.style.display = 'none';
    }
    </script>
</body>
</html>