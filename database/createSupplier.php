<?php
session_start();
// Ensure user is logged in and retrieve user ID from the session
if (!isset($_SESSION['user'])) {
    die("Error: User not logged in.");
}

$user = $_SESSION['user'];
$user_id = $user['id']; // Get the user_id from the session

$supplier_name = $_POST['supplier_name'];
$supplier_email = $_POST['supplier_email'];
$supplier_contact = $_POST['supplier_contact'];
$supplier_address = $_POST['supplier_address']; // Removed the extra space

include('connection.php');

// Adding items to the record
try {
    // Check if the supplier already exists in the 'suppliers' table
    $checkQuery = "SELECT COUNT(*) FROM suppliers WHERE supplier_name = :supplier_name";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute(['supplier_name' => $supplier_name]);
    $existingSupplierCount = $stmt->fetchColumn();

    if ($existingSupplierCount > 0) {
        // Supplier already exists, set response and redirect
        $response = [
            'success' => false,
            'message' => 'A supplier with this name already exists!'
        ];
    } else {
        // Prepare the insert statement
        $command = "INSERT INTO suppliers (supplier_name, supplier_email, supplier_contact, supplier_address, user_id, created_at, updated_at) 
                    VALUES (:supplier_name, :supplier_email, :supplier_contact, :supplier_address, :user_id, NOW(), NOW())"; // Added user_id to the query

        $stmt = $conn->prepare($command);
        $stmt->execute([
            'supplier_name' => $supplier_name,
            'supplier_email' => $supplier_email,
            'supplier_contact' => $supplier_contact,
            'supplier_address' => $supplier_address,
            'user_id' => $user_id // Include user_id
        ]);

        $response = [
            'success' => true,
            'message' => $supplier_name . ' has been successfully created!'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

$_SESSION['response'] = $response;
header('Location: ../supplier.php');
exit(); // It's a good practice to call exit after a redirect
?>
