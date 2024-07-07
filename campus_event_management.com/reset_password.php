<?php
include('config.php');
session_start();

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $current_time = date('Y-m-d H:i:s');

    // Check if token exists and is not expired
    $sql = "SELECT user_id FROM password_resets WHERE token = '$token' AND expires_at > '$current_time'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // Token is valid and not expired
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
            
            if ($new_password == $confirm_password) {
                $hashed_password = md5($new_password);
                $user_id = $row['user_id'];

                // Update user's password
                $sql = "UPDATE users SET password = '$hashed_password' WHERE user_id = '$user_id'";
                if (mysqli_query($conn, $sql)) {
                    // Delete the token after successful password reset
                    $sql = "DELETE FROM password_resets WHERE token = '$token'";
                    mysqli_query($conn, $sql);

                    $success_message = "Password has been reset successfully!";
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
            } else {
                $error = "Passwords do not match. Please try again.";
            }
        }
    } else {
        // Token is invalid or expired
        $error = "This reset link is invalid or has expired.";
    }
} else {
    $error = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php else: ?>
            <form action="" method="post">
                <div class="input-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="input-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
            <div class="error-message"><?php echo isset($error) ? $error : ''; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

