<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists in the database
    $sql = "SELECT user_id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $user_id = $row['user_id'];
        $token = bin2hex(random_bytes(50)); // Generate a secure token
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

        // Insert token into password_resets table
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES ('$user_id', '$token', '$expires_at')";
        if (mysqli_query($conn, $sql)) {
            // Display the reset link directly on the page
            $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
            $success_message = "A password reset link has been generated: <a href='" . $reset_link . "'>" . $reset_link . "</a>";
        } else {
            $error = "Failed to generate reset link. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        .success-container {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            word-wrap: break-word; /* Ensure long links break to the next line */
        }
        .success-container a {
            color: #721c24;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="" method="post">
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
        <?php if(isset($success_message)): ?>
        <div class="success-container">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <div class="error-message"><?php echo isset($error) ? $error : ''; ?></div>
        <a href="login.php">Back to Login Page</a>
    </div>
</body>
</html>


