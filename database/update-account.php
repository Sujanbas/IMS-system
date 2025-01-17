<?php
// Check if expected POST data exists
$data = $_POST;
$user_id = isset($data['user_id']) ? (int) $data['user_id'] : 0;
$first_name = isset($data['first_name']) ? $data['first_name'] : '';
$last_name = isset($data['last_name']) ? $data['last_name'] : '';
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? $data['password'] : '';
$phone = isset($data['phone']) ? $data['phone'] : '';

// Handle profile picture upload if provided
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
    $profile_pic = $_FILES['profile_pic']['name'];
    $target_dir = "../pics/";
    $target_file = $target_dir . basename($profile_pic);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
} else {
    $profile_pic = null;
}

// Ensure required fields are not empty
if ($user_id === 0 || empty($first_name) || empty($last_name) || empty($email)) {
    header("Location: profile-config.php?message=" . urlencode("Missing required fields.") . "&success=false");
    exit;
}

include('connection.php');

try {
    // Check if email already exists for a different user
    $emailCheckQuery = "SELECT id FROM users WHERE email = :email AND id != :user_id";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->execute(['email' => $email, 'user_id' => $user_id]);

    if ($stmt->fetch()) {
        header("Location: profile-config.php?message=" . urlencode("This email is already in use by another user.") . "&success=false");
        exit;
    }

    // Build query to update user information
    $updateQuery = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, updated_at = NOW()";
    $params = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'user_id' => $user_id
    ];

    // Update password only if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery .= ", password = :password";
        $params['password'] = $hashedPassword;
    }

    // Update profile picture if provided
    if ($profile_pic) {
        $updateQuery .= ", profile_pic = :profile_pic";
        $params['profile_pic'] = $profile_pic;
    }

    $updateQuery .= " WHERE id = :user_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute($params);

    header("Location: ../profile-config.php?message=" . urlencode("Account updated successfully for {$first_name} {$last_name}.") . "&success=true");

} catch (Exception $e) {
    header("Location: ../profile-config.php?message=" . urlencode("Error processing your update request: " . $e->getMessage()) . "&success=false");
}
exit;
?>
