<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}
include('config.php');

// Fetch event details based on event_id
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
    $result = $conn->query($sql);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Event Details</h1>
    </div>
    <?php include('sidebar.php'); ?>
    <div class="main">
        <?php if (!empty($event['event_poster'])): ?>
            <img src="<?php echo $event['event_poster']; ?>" alt="Event Poster" style="max-width: 100%; height: auto;">
        <?php endif; ?>

        <h2><?php echo $event['event_name']; ?></h2>
        <p>Date: <?php echo $event['event_date']; ?></p>
        <p>Location: <?php echo $event['event_location']; ?></p>
        <p>Description: <?php echo $event['event_description']; ?></p>
        
        <a href="admin_index.php" class="btn btn-primary">Back to Events</a>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
