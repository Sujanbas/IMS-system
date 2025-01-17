<?php
include './database/connection.php';

try {
    // Query the database for stock data
    $query = "SELECT item_name, quantity FROM items ORDER BY updated_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare response data
    $labels = [];
    $quantities = [];
    foreach ($items as $item) {
        $labels[] = $item['item_name'];
        $quantities[] = $item['quantity'];
    }

    // Return data as JSON
    echo json_encode(['labels' => $labels, 'quantities' => $quantities]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
