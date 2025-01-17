<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Assign the session user to a variable
$user = $_SESSION['user'];
$item_id = $_GET['item_id'] ?? null;

include('./database/connection.php');

// Fetch suppliers from the supplier table
$sql = "SELECT supplier_id, supplier_name FROM suppliers";
$stmt = $conn->prepare($sql);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./CSS/order.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <title>Create Order</title>
</head>

<body>
    <div id="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="profile">
                <h1>IMS</h1>
                <img src="./pics/user.jfif" alt="User Image">
                <p>Hello <?= htmlspecialchars($user['first_name']) ?></p>
            </div>
            <ul>
                <li><a href="userDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="dashboard.php"><i class="fas fa-shopping-cart"></i> Items Management</a></li>
                <li><a href="#"><i class="fas fa-dollar-sign"></i> Revenue Management</a></li>
                <li><a href="supplier.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Supplier Management</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Configuration</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Stats</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>Create Order</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <!-- Main Content Area -->
            <div class="content-area">
                <div class="supplier-form">
                    <form action="database/createOrder.php" method="POST">
                        <!-- Hidden input to pass the item_id -->
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item_id) ?>">

                        <div>
                            <label for="supplier">Supplier Name</label>
                            <select id="supplier" name="supplier_id" required>
                                <option value="">-- Select Supplier --</option>
                                <?php
                                // Populate the dropdown with suppliers
                                if (count($suppliers) > 0) {
                                    foreach ($suppliers as $row) {
                                        echo "<option value='" . $row['supplier_id'] . "'>" . htmlspecialchars($row['supplier_name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No suppliers available</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" required>
                        </div>

                        <div>
                            <button type="submit">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
