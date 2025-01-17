<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

include('../database/connection.php');

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the required POST data is available
    if (isset($_POST['item_id'], $_POST['quantity'], $_POST['supplier_id'])) {
        $user_id = $user['id'];
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'];
        $supplier_id = $_POST['supplier_id'];
        $order_date = date('Y-m-d H:i:s');
        $status = 'pending';

        // Retrieve item name for the success message
        $sqlItem = "SELECT item_name FROM items WHERE item_id = :item_id";
        $stmtItem = $conn->prepare($sqlItem);
        $stmtItem->bindParam(':item_id', $item_id);
        $stmtItem->execute();
        $item = $stmtItem->fetch(PDO::FETCH_ASSOC);
        $item_name = $item['item_name'] ?? 'Unknown Item';

        // Check if the order already exists for this item and supplier
        $sqlCheck = "SELECT * FROM orders WHERE user_id = :user_id AND item_id = :item_id AND supplier_id = :supplier_id";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':user_id', $user_id);
        $stmtCheck->bindParam(':item_id', $item_id);
        $stmtCheck->bindParam(':supplier_id', $supplier_id);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // If order exists, set session variables and redirect to show confirmation form
            $_SESSION['order_exists'] = $item_name;
            $_SESSION['item_id'] = $item_id; // Save item ID for confirmation
            $_SESSION['quantity'] = $quantity; // Save quantity for confirmation
            $_SESSION['supplier_id'] = $supplier_id; // Save supplier ID for confirmation
            header('Location: ../orderManagement.php');
            exit();
        } else {
            // If order does not exist, proceed to insert the order
            $sql = "INSERT INTO orders (user_id, item_id, quantity, order_date, status, supplier_id) 
                    VALUES (:user_id, :item_id, :quantity, :order_date, :status, :supplier_id)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':order_date', $order_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':supplier_id', $supplier_id);

            if ($stmt->execute()) {
                $_SESSION['order_success'] = "Order placed successfully for " . htmlspecialchars($item_name);
                header('Location: ../orderManagement.php');
                exit();
            } else {
                echo "Error placing order.";
            }
        }
    } else {
        echo "Missing required form data.";
    }
}
?>
