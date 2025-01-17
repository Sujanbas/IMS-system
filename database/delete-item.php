<?php
session_start();
include('connection.php'); // Ensure you have your database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Check if item_id is provided
if (isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $item_name = isset($_POST['item_name']) ? (string)$_POST['item_name'] : '';

    try {
        $command = "DELETE FROM items WHERE item_id = :item_id"; // Use item_id here
        $stmt = $conn->prepare($command);
        $stmt->bindParam(':item_id', $item_id);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'You have successfully deleted the item: ' . htmlspecialchars($item_name)
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error processing your delete request: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No item ID provided.'
    ]);
}

?>