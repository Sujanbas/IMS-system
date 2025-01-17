<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Assign the session user and supplier to a variable
$user = $_SESSION['user'];
$users = include('database/show-users.php');
$suppliers = include('database/show-suppliers.php');
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
    <link rel="stylesheet" href="./public/supplier.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <title>Supplier</title>
</head>

<body>
    <div id="main-container">
        <?php include './partials/sidebar.php';?>

        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>Create Supplier Account</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>
            <!-- Main Content Area -->
            <div class="content-area">
                <div class="supplier-form">
                    <form action="database/createSupplier.php" method="POST">
                        <div>
                            <label for="supplier-name">Supplier Name</label>
                            <input type="text" id="supplier-name" name="supplier_name" required>
                        </div>
                        <div>
                            <label for="supplier-email">Supplier Email</label>
                            <input type="email" id="supplier-email" name="supplier_email" required>
                        </div>
                        <div>
                            <label for="supplier-contact">Supplier Contact</label>
                            <input type="number" id="supplier-contact" name="supplier_contact" required>
                        </div>
                        <div>
                            <label for="supplier-address">Supplier Address</label>
                            <input type="text" id="supplier-address" name="supplier_address" required>
                        </div>
                        <div>
                            <button type="submit">Add Supplier</button>
                        </div>
                    </form>
                    <?php
                    if (isset($_SESSION['response'])) { 
                        $responseMessage = $_SESSION['response']['message'];
                        $is_success = $_SESSION['response']['success'];
                        ?>
                        <div class="error-message" id="error-message">
                            <p class="error_message <?= $is_success ? 'error_message_True' : 'error_message_False' ?>">
                                <?= $responseMessage ?>
                            </p>
                        </div>
                        <?php unset($_SESSION['response']); } ?>
                </div>

                <div class="supplier-table">
                    <h2>Supplier List</h2>
                    <input type="text" id="search-bar" placeholder="Search suppliers..." onkeyup="filterTable()">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="supplier-body">
                            <?php foreach ($suppliers as $index => $supplier) { ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_email']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_contact']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supplier_address']) ?></td>
                                    <td><?= date('M d, y @ h:i:s A', strtotime($supplier['created_at'])) ?></td>
                                    <td><?= date('M d, y @ h:i:s A', strtotime($supplier['updated_at'])) ?></td>
                                    <td>
                                        <button class="action-button edit-button" onclick="">
                                            <i class="fas fa-camera"></i> Edit
                                        </button> 
                                        <button href="updateSupplier.php" class="action-button delete-button">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    function filterTable() {
    let input = document.getElementById("search-bar").value.toLowerCase();
    let table = document.getElementById("supplier-body");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let rowContainsSearchText = false;
        let cells = rows[i].getElementsByTagName("td");

        for (let j = 0; j < cells.length; j++) {
            let cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().includes(input)) {
                rowContainsSearchText = true;
                break;
            }
        }

        rows[i].style.display = rowContainsSearchText ? "" : "none";
        }
    }
</script>
</html>
