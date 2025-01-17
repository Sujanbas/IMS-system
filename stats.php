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
include './database/connection.php'; // Include your database connection

// Fetch inventory data
$query = "SELECT item_name, quantity, price FROM items WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT); // Assuming user ID is stored in session
$stmt->execute();

$items = [];
$quantities = [];
$prices = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $items[] = $row['item_name'];
    $quantities[] = $row['quantity'];
    $prices[] = $row['price'];
}

$stmt = null; // Close the statement
$conn = null; // Close the database connection

// Combine items, quantities, and prices into an associative array for sorting
$productData = [];
for ($i = 0; $i < count($items); $i++) {
    $productData[] = [
        'name' => $items[$i],
        'quantity' => $quantities[$i],
        'price' => $prices[$i]
    ];
}

// Sort by price (descending)
usort($productData, function($a, $b) {
    return $b['price'] <=> $a['price'];
});

// Get top 5 by price
$top5ByPrice = array_slice($productData, 0, 5);

// Sort by quantity (descending)
usort($productData, function($a, $b) {
    return $b['quantity'] <=> $a['quantity'];
});

// Get top 5 by quantity
$top5ByQuantity = array_slice($productData, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./public/stats.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Item Stats</title>
  
</head>
<body>
    <div id="main-container">
        <!-- Sidebar -->
        <?php include './partials/sidebar.php'; ?>
        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>Item Stats</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>   
            <h3>Hello <?= htmlspecialchars($user['first_name']) ?>! You can view your item stats here...</h3>
            <div class="content-area">
                <div class="chart-container">
                    <canvas id="stockChart"></canvas>
                </div>
                <div class="top-products">
                <div class="product-list">
                    <h4>Top 5 Products by Price</h4>
                    <ul>
                        <?php foreach ($top5ByPrice as $product): ?>
                            <li><?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="product-list">
                    <h4>Top 5 Products by Quantity</h4>
                    <ul>
                        <?php foreach ($top5ByQuantity as $product): ?>
                            <li><?= htmlspecialchars($product['name']) ?> - Quantity: <?= htmlspecialchars($product['quantity']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            </div>
        </div> 
    </div>

    <script>
        // Prepare data for Chart.js
        const labels = <?= json_encode($items) ?>;
        const data = {
            labels: labels,
            datasets: [{
                label: 'Item Quantity',
                data: <?= json_encode($quantities) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.8)', // Bright Blue
                borderColor: 'rgba(54, 162, 235, 1)', // Bright Blue
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar', // You can change this to 'line', 'pie', etc.
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#00ffcc' // Change the color of the Y-axis numbers
                        }
                    },
                    x: {
                        ticks: {
                            color: '#00ffcc' // Change the color of the X-axis item names
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#00ffcc' // Change the color of the legend text
                        }
                    }
                }
            }
        };

        // Render the chart
        const stockChart = new Chart(
            document.getElementById('stockChart'),
            config
        );
    </script>
</body>
</html>