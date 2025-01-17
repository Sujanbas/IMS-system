<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Assign the session user to a variable
$user = $_SESSION['user'];

include './database/userData.php';
$orders = include('database/show-orderData.php');

// Check if there's a success message to display
$orderSuccessMessage = "";
if (isset($_SESSION['order_success'])) {
    $orderSuccessMessage = $_SESSION['order_success'];
    unset($_SESSION['order_success']); // Clear the message after displaying it
}
$order_exists = "";
if (isset($_SESSION['order_exists'])) {
    $order_exists = $_SESSION['order_exists'];
    unset($_SESSION['order_exists']); // Clear the message after displaying it
}

// Fetch suppliers and items for the create order form
$sqlSuppliers = "SELECT supplier_id, supplier_name FROM suppliers";
$stmt = $conn->prepare($sqlSuppliers);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlItems = "SELECT item_id, item_name FROM items";
$stmt = $conn->prepare($sqlItems);
$stmt->execute(); 
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the item_id from the URL if it exists
$itemId = isset($_GET['item_id']) ? intval($_GET['item_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./public/order-management.css">
    <title>Order Management</title>
</head>
<body>
    <div id="main-container">
        <!-- Sidebar -->
        <?php include './partials/sidebar.php'; ?>
        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>Order Management</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <!-- Notification bar for successful order creation -->
            <?php if ($orderSuccessMessage): ?>
                <div class="notification-bar success">
                    <p><?= htmlspecialchars($orderSuccessMessage) ?></p>
                    <button onclick="this.parentElement.style.display='none'">Close</button>
                </div>
            <?php endif; ?>

            <?php if ($order_exists) : ?>
                <div class="notification-bar warning">
                    <p><?= htmlspecialchars($order_exists); ?></p>
                    <form action="database/createOrder.php" method="post" style="display:inline;">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit">Yes</button>
                    </form>
                    <form action="database/createOrder.php" method="post" style="display:inline;">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit">No</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="content-area">
                <!-- Create Order Form -->
                <div class="supplier-form">
                    <h2>Create New Order</h2>
                    <form id="orderForm" action="database/createOrder.php" method="POST">
                        <div>
                            <label for="item">Item</label>
                            <select name="item_id" id="item" required>
                                <option value="">Select an Item</option>
                                <?php foreach ($items as $item) : ?>
                                    <option value="<?= htmlspecialchars($item['item_id']) ?>" <?= ($itemId == $item['item_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($item['item_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div >
                            <label for="supplier">Supplier</label>
                            <select name="supplier_id" id="supplier" required>
                                <option value="">Select a Supplier</option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option value="<?= htmlspecialchars($supplier['supplier_id']) ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" required>
                        </div>

                        <div>
                            <button type="submit">Place Order</button>
                        </div>
                    </form>
                </div>
                <!-- Display Existing Orders -->
                <div class="supplier-table">
                    <h2>Existing Orders</h2>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Order number</th>
                                <th>Item Name</th>
                                <th>Supplier Name</th>
                                <th>Quantity</th>
                                <th>Order Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)) : ?>
                                <?php foreach($orders as $index => $order){ ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($order['item_name']) ?></td>
                                        <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                                        <td><?= htmlspecialchars($order['status']) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6">No orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>