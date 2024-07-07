<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}
include('config.php');

// Fetch events from the database
$sql = "SELECT * FROM events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Event Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Campus Event Management</h1>
    </div>
    <?php include('sidebar.php'); ?>
    <div class="main">
        <h1>Upcoming Events</h1>
        <div class="events-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='event-card'>
                            <h2>" . $row['event_name'] . "</h2>
                            <p>Date: " . $row['event_date'] . "</p>
                            <p>Location: " . $row['event_location'] . "</p>
                            <p><a href='eventdetails2.php?id=" . $row['event_id'] . "'>View Details</a></p>
                          </div>";
                }
            } else {
                echo "No events found.";
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>

