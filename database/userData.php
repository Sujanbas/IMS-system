<?php
$user_id = $user['id'] ?? null;
$first_name = $_GET['first_name'] ?? $user['first_name'];
$last_name = $_GET['last_name'] ?? $user['last_name'];
$email = $_GET['email'] ?? $user['email'];
$phone = $_GET['phone'] ?? $user['phone'];

// Handle file upload if the user has provided a new profile picture
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
    $profile_pic = $_FILES['profile_pic']['name'];
    $target_dir = "../pics/";
    $target_file = $target_dir . basename($profile_pic);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
        // Update the profile_pic field in the database
        include('database/connection.php'); // Include the correct path to the connection file

        // Prepare the query to update the profile picture in the database
        $updateQuery = "UPDATE users SET profile_pic = :profile_pic WHERE id = :user_id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([
            'profile_pic' => $profile_pic,
            'user_id' => $user_id
        ]);

        // Update the profile_pic path for display in the profile
        $profile_pic_path = "./pics/" . htmlspecialchars($profile_pic);
    } else {
        // If the file couldn't be uploaded, set a default profile picture path
        $profile_pic_path = "./pics/user.jfif";
    }
} else {
    // If no profile picture is uploaded, use the current one from the database
    include('database/connection.php'); // Include the correct path to the connection file

    $stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_pic_path = !empty($dbUser['profile_pic']) ? "./pics/" . htmlspecialchars($dbUser['profile_pic']) : "./pics/user.jfif";
}

// Display user profile information
?>