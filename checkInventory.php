<?php
include('./database/connection.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user']['id'];

try {
    $checkQuery = "SELECT item_name, quantity, item_id FROM items WHERE user_id = :user_id AND quantity < 10";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute(['user_id' => $user_id]);

    $lowStockItems = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

    // If requested via fetch, return JSON; otherwise, return the array
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => true, 'items' => $lowStockItems]);
        exit();
    } else {
        // Return array for PHP inclusion
        return $lowStockItems;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error checking inventory: ' . $e->getMessage()]);
    exit();
}
