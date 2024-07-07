<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role'];

    // Hash the password for security
    $hashed_password = md5($password);

    $sql = "SELECT user_id FROM users WHERE username = '$username' and password = '$hashed_password' and role = '$role'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    // If result matched $username and $hashed_password, table row must be 1 row
    if ($count == 1) {
        $_SESSION['login_user'] = $username;
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role'] = $role;

        if ($role == 'admin') {
            header("location: admin.php");
        } else {
            header("location: index.php");
        }
    } else {
        $error = "Your Login Name or Password is invalid";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="container">
        <h2>Login Page</h2>
        <form action="" method="post">
            <div class="input-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-group">
                <label>Role:</label>
                <select name="role">
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Login</button>
        </form>
        <a href="forgot_password.php">Forgot Password?</a> <!-- Link to Forgot Password Page -->
        <a class="create-account" href="register.php">Create New Account</a>
        <div class="error-message"><?php echo isset($error) ? $error : ''; ?></div>
    </div>
</body>
</html>
