<?php
// Database connection
include 'connection.php';

try {
    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO feedback (full_name, email, description) VALUES (:name, :email, :description)";
        $stmt = $conn->prepare($sql);

        // Bind values to the placeholders
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $_POST['description'], PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to feedback.php with a success message
            header("Location: ../feedback.php?status=success");
            exit();
        } else {
            // Redirect to feedback.php with an error message
            header("Location: ../feedback.php?status=false");
            exit();
        }
    }
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database Error: " . $e->getMessage());
    header("Location: ../feedback.php?status=false");
    exit();
}
?>
