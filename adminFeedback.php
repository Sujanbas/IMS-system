<?php
    session_start();

    // If the user is not logged in, redirect to login page
    if(!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }

    // Assign the session user to a variable
    $user = $_SESSION['user'];
    $users = include('database/show-users.php');
    $current_page = basename($_SERVER['PHP_SELF']);
    include './database/connection.php';

    // Fetch all feedback from the database
    $sql = "SELECT * FROM feedback ORDER BY submitted_at DESC"; // Assuming 'submitted_at' is a column in the feedback table
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="./CSS/sidebar.css">
        <link rel="stylesheet" href="./public/admin.css">
        <title>Feedback List</title>
    </head>

    <body>
          <!-- Side bar -->
          <div class="sidebar">
                <div class="profile">
                    <h1>IMS</h1>
                    <img src="./pics/user.jfif" alt="User Image">
                    <p>Hello <?= htmlspecialchars($user['first_name']) ?></p>
                </div>
                <ul>
                    <li><a href="adminDashboard.php" class="<?= $current_page == 'adminDashboard.php' ? 'active' : '' ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="admin.php" class="<?= $current_page == 'admin.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> User Management</a></li>
                    <li><a href="adminFeedback.php" class="<?= $current_page == 'adminFeedback.php' ? 'active' : '' ?>"><i class="fas fa-comment"></i>Users Feedback</a></li> 
                </ul>
         </div>

        <div class="main-content">
        <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a>  <h2>Feedback Management</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
        </div>
          
        <div class="content-placeholder">
            <h2>User Feedback</h2>

            <?php if ($result->rowCount() > 0): ?>
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Feedback</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                                <td><?= date("Y-m-d H:i:s", strtotime($row['submitted_at'])) ?></td>
                                <td>
                                    <!-- Reply Button -->
                                    <button class="action-button reply-button" 
                                            onclick="openReplyModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>', '<?= htmlspecialchars($row['email']) ?>')">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>

                                    <!-- Resolve Button -->
                                    <button class="action-button resolve-button" 
                                            onclick="resolveFeedback(<?= $row['id'] ?>)">
                                        <i class="fas fa-check"></i> Resolve
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback available.</p>
            <?php endif; ?>
        </div>
        <script>
            // Function to open the reply modal with feedback details
            function openReplyModal(feedbackId, name, email) {
                // Replace this with the actual modal opening code
                alert('Replying to feedback from ' + name + ' (' + email + ')');
                // For example, you could open a modal and populate fields with the feedback details
            }

            // Function to resolve the feedback
            function resolveFeedback(feedbackId) {
                // Add the logic to resolve feedback, for example, send an AJAX request or reload the page
                if (confirm('Are you sure you want to resolve this feedback? This will delete the feedback!!!')) {
                    window.location.href = "resolve_feedback.php?id=" + feedbackId;
                }
            }
        </script>
    </body>
</html>