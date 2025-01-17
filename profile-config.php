<?php
session_start();

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Assign the session user to a variable
$user = $_SESSION['user'];
include './database/userData.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./public/config-user.css">
    <title>Supplier</title>
</head>
<body>
    <div id="main-container">
         <!-- Sidebar -->
         <?php include './partials/sidebar.php';?>
        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>Update User Account</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>   
            <h3>Hello <?= htmlspecialchars($user['first_name']) ?>! You can update your account info here...</h3>
            <div class="content-area">
                <div class="user-form">
                    <h2>Profile Configuration</h2>
                    <form action="database/update-account.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>" />

                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
                        </div>

                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password">
                        </div>

                        <div class="form-group">
                            <label for="profile_pic">Profile Picture</label>
                            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="update-button">Update Profile</button>
                        </div>
                        <div class="notify">
                            <?php
                            if (isset($_GET['message'])) {
                                $message = htmlspecialchars($_GET['message']);
                                $success = isset($_GET['success']) && $_GET['success'] === 'true';

                                // Echo the message with the appropriate class (success or error)
                                echo "<div class='notification " . ($success ? "success" : "error") . "'>{$message}</div>";
                            }
                            ?>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
