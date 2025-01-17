<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./CSS/feedback.css">
    <title>Feedback Form</title>
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
   
</head>
<body>
    <div class="container">
    <i class="fas fa-window-close" onclick="window.location.href='contact.php';"></i>
        <h2>Submit Your Feedback</h2>
        <form method="POST" action="./database/submit_feedback.php">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="description">Feedback</label>
            <textarea id="description" name="description" rows="5" required></textarea>
            
            <button type="submit">Submit Feedback</button>
        </form>
        <div class="notification <?php echo (isset($_GET['status']) && $_GET['status'] == 'success') ? 'success' : ((isset($_GET['status']) && $_GET['status'] == 'false') ? 'error' : ''); ?>">
            <?php
            if (isset($_GET['status']) && $_GET['status'] == 'success') {
                echo "Thank you for your feedback, we will get back to you!!";
            } elseif (isset($_GET['status']) && $_GET['status'] == 'false') {
                echo "Sorry there was an Error!! processing your request";
            }
            ?>
        </div>

    </div>
</body>
</html>
