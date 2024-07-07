<?php
session_start();
include('config.php');

if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['edit_id'])) {
    $event_id = $_GET['edit_id'];
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit();
    }
} else {
    echo "Event ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $event_location = mysqli_real_escape_string($conn, $_POST['event_location']);
    $event_description = mysqli_real_escape_string($conn, $_POST['event_description']);

    // Handle poster upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["event_poster"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if a new file is uploaded
    if ($_FILES["event_poster"]["size"] > 0) {
        // Check if existing poster file exists and delete it
        if (file_exists($event['event_poster'])) {
            unlink($event['event_poster']);
        }

        // Check file size
        if ($_FILES["event_poster"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // Upload the new file
            if (move_uploaded_file($_FILES["event_poster"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["event_poster"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Update event details in database
    $sql_update = "UPDATE events 
                   SET event_name='$event_name', event_date='$event_date', event_location='$event_location', event_description='$event_description'";

    // Update poster path if a new file is uploaded
    if ($_FILES["event_poster"]["size"] > 0) {
        $sql_update .= ", event_poster='$target_file'";
    }

    $sql_update .= " WHERE event_id = $event_id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error updating event: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 10px;
        }
        form input[type="text"],
        form input[type="date"],
        form textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }
        form textarea {
            height: 100px;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Edit Event</h1>
    </div>
    <div class="main">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
            <label>Event Name:</label>
            <input type="text" name="event_name" value="<?php echo $event['event_name']; ?>" required><br>
            <label>Event Date:</label>
            <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required><br>
            <label>Event Location:</label>
            <input type="text" name="event_location" value="<?php echo $event['event_location']; ?>" required><br>
            <label>Event Description:</label>
            <textarea name="event_description" required><?php echo $event['event_description']; ?></textarea><br>
            <label>Current Poster:</label><br>
            <?php
            if (!empty($event['event_poster'])) {
                echo "<img src='" . $event['event_poster'] . "' width='200'><br>";
                echo "<label><input type='checkbox' name='remove_poster'> Remove Poster</label><br>";
            }
            ?>
            <label>New Poster:</label>
            <input type="file" name="event_poster" accept="image/*"><br>
            <input type="submit" name="update_event" value="Update Event">
        </form>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
