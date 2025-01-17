<?php
    // Ensure session is started
    include('connection.php');

    // Check if user session data is set
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $id = $_SESSION['user']['id']; // Get user ID from session

        // Prepare and execute the query to fetch items for the logged-in user
        $stmt = $conn->prepare("SELECT * FROM suppliers WHERE user_id = :id ORDER BY created_at DESC");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $suppliers = $stmt->fetchAll();
        
        return $suppliers;
    } else {
        echo "User is not logged in.";
        $suppliers = [];
    }
?>
