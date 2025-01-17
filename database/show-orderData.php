<?php
// Ensure session is started
include('connection.php');

// Initialize orders array to avoid undefined variable errors
$orders = [];

if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
    $id = $_SESSION['user']['id']; // Get user ID from session

    // Prepare and execute the query to fetch items for the logged-in user
    $stmt = $conn->prepare("
        SELECT orders.order_id, items.item_name, suppliers.supplier_name, orders.quantity, orders.order_date, orders.status
        FROM orders
        JOIN items ON orders.item_id = items.item_id
        JOIN suppliers ON orders.supplier_id = suppliers.supplier_id
        WHERE orders.user_id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);  // Bind user ID to the query
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    // Fetch all the orders for the logged-in user
    $orders = $stmt->fetchAll();
    return $orders;
} else {
    echo "User is not logged in.";
    $orders = [];
}
?>
