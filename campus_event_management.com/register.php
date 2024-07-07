<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Hardcode the role to "student"
    $role = 'student';

    // Hash the password for security
    $hashed_password = md5($password);

    $sql = "INSERT INTO users (username, password, email, role) 
            VALUES ('$username', '$hashed_password', '$email', '$role')";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Registered successfully! Your email is: " . $email;
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles2.css">
    <style>
        .success-container {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        /* Ensure input fields are the same width */
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Page</h2>
        <form action="" method="post">
            <label>Username :</label>
            <input type="text" name="username" required><br>
            <label>Password :</label>
            <input type="password" name="password" required><br>
            <label>Email :</label>
            <input type="email" name="email" required><br>
            <input type="submit" value="Register"><br>
        </form>
        <?php if(isset($success_message)): ?>
        <div class="success-container">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
        <div class="error-container">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        <a href="login.php">Back to Login Page</a>
    </div>
</body>
</html>

