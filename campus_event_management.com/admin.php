<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .error-container {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .event-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
    </div>
    <div class="sidebar">
        <a href="admin_index.php">Dashboard</a>
        <a href="admin.php">Admin Panel</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="main">
        <h2>Add New Event</h2>
        <!-- PHP code for handling event addition -->
        <?php
        session_start();
        include('config.php');

        // Handle event deletion
        if (isset($_GET['delete_id'])) {
            $event_id = $_GET['delete_id'];
            $sql_delete = "DELETE FROM events WHERE event_id = $event_id";
            if (mysqli_query($conn, $sql_delete)) {
                echo '<div class="success-message">Event deleted successfully.</div>';
            } else {
                echo '<div class="error-container">Error deleting event: ' . mysqli_error($conn) . '</div>';
            }
        }

        // Handle event update
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
            $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
            $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
            $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
            $event_location = mysqli_real_escape_string($conn, $_POST['event_location']);
            $event_description = mysqli_real_escape_string($conn, $_POST['event_description']);

            // Update event details in database
            $sql_update = "UPDATE events 
                           SET event_name='$event_name', event_date='$event_date', event_location='$event_location', event_description='$event_description' 
                           WHERE event_id = $event_id";
            
            if (mysqli_query($conn, $sql_update)) {
                echo '<div class="success-message">Event updated successfully.</div>';
            } else {
                echo '<div class="error-container">Error updating event: ' . mysqli_error($conn) . '</div>';
            }
        }

        // Add Event with Poster
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
            $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
            $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
            $event_location = mysqli_real_escape_string($conn, $_POST['event_location']);
            $event_description = mysqli_real_escape_string($conn, $_POST['event_description']);

            // Poster upload handling
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["event_poster"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["event_poster"]["tmp_name"]);
            if ($check === false) {
                echo '<div class="error-container">File is not an image.</div>';
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["event_poster"]["size"] > 500000) {
                echo '<div class="error-container">Sorry, your file is too large.</div>';
                $uploadOk = 0;
            }

            // Allow certain file formats
            $allowed_formats = array("jpg", "jpeg", "png", "gif");
            if (!in_array($imageFileType, $allowed_formats)) {
                echo '<div class="error-container">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo '<div class="error-container">Sorry, your file was not uploaded.</div>';
            } else {
                // if everything is ok, try to upload file
                if (move_uploaded_file($_FILES["event_poster"]["tmp_name"], $target_file)) {
                    // Insert event details into database
                    $sql_insert = "INSERT INTO events (event_name, event_date, event_location, event_description, event_poster)
                                VALUES ('$event_name', '$event_date', '$event_location', '$event_description', '$target_file')";

                    if (mysqli_query($conn, $sql_insert)) {
                        echo '<div class="success-message">New event created successfully.</div>';
                    } else {
                        echo '<div class="error-container">Error: ' . $sql_insert . '<br>' . mysqli_error($conn) . '</div>';
                    }
                } else {
                    echo '<div class="error-container">Sorry, there was an error uploading your file.</div>';
                }
            }
        }

        // Fetch all events
        $sql_select = "SELECT * FROM events";
        $result = mysqli_query($conn, $sql_select);
        ?>

        <!-- Event addition form with poster upload -->
        <form action="" method="post" enctype="multipart/form-data">
            <label>Event Name:</label>
            <input type="text" name="event_name" required><br>
            <label>Event Date:</label>
            <input type="date" name="event_date" required><br>
            <label>Event Location:</label>
            <input type="text" name="event_location" required><br>
            <label>Event Description:</label>
            <textarea name="event_description" required></textarea><br>
            <label>Event Poster:</label>
            <input type="file" name="event_poster" accept="image/*" required><br>
            <input type="submit" name="add_event" value="Add Event">
        </form>

        <!-- Display all events with edit and deletion options -->
        <h2>All Events</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='event-title'>" . $row['event_name'] . " - " . $row['event_date'] . " - " . $row['event_location'] .
                    " <a href='admin.php?delete_id=" . $row['event_id'] . "' onclick='return confirm(\"Are you sure you want to delete this event?\");'>Delete</a>" .
                    " <a href='edit_event.php?edit_id=" . $row['event_id'] . "'>Edit</a></div>";

                // Fetch registered students
                $event_id = $row['event_id'];
                $reg_sql = "SELECT * FROM registrations WHERE event_id = $event_id";
                $reg_result = $conn->query($reg_sql);

                if ($reg_result->num_rows > 0) {
                    echo "<div class='table-container'>
                            <table>
                                <tr>
                                    <th>Name</th>
                                    <th>Matric No</th>
                                    <th>Faculty</th>
                                    <th>Programme</th>
                                </tr>";
                    while ($reg_row = $reg_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $reg_row['name'] . "</td>
                                <td>" . $reg_row['matric_no'] . "</td>
                                <td>" . $reg_row['faculty'] . "</td>
                                <td>" . $reg_row['programme'] . "</td>
                            </tr>";
                    }
                    echo "</table></div>";
                } else {
                    echo "<div>No registrations for this event.</div>";
                }
            }
        } else {
            echo "No events found.";
        }
        ?>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
