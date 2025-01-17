<?php
session_start();
include('connection.php'); // Include your database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Check if expected POST data exists
$data = $_POST;
$itemId = isset($data['item_id']) ? (int)$data['item_id'] : 0;
$itemName = isset($data['item_name']) ? trim($data['item_name']) : '';
$itemDescription = isset($data['item_description']) ? trim($data['item_description']) : '';
$itemPrice = isset($data['item_price']) ? (float)$data['item_price'] : 0.0;
$itemQuantity = isset($data['item_quantity']) ? (int)$data['item_quantity'] : 0;

// Ensure required fields are not empty
if ($itemId === 0 || empty($itemName) || empty($itemDescription) || $itemPrice <= 0 || $itemQuantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

try {
    // Prepare the SQL statement to update the item
    $updateQuery = "UPDATE items SET item_name = ?, item_description = ?, price = ?, quantity = ? WHERE item_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssiii", $itemName, $itemDescription, $itemPrice, $itemQuantity, $itemId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update item: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error processing your update request: ' . $e->getMessage()]);
}

// Close the database connection
$conn->close();
?>