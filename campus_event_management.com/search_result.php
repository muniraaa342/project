<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}
include('config.php');

$search_query = $_GET['search'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
    </div>
    <div class="sidebar">
        <a href="index.php">Dashboard</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="main">
        <div class="events-container">
            <?php
            // Search for events in the database
            $sql = "SELECT * FROM events WHERE event_name LIKE '%$search_query%' OR event_location LIKE '%$search_query%'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='event-card'>
                            <h2>" . $row['event_name'] . "</h2>
                            <p>Date: " . $row['event_date'] . "</p>
                            <p>Location: " . $row['event_location'] . "</p>
                            <p><a href='eventdetails1.php?id=" . $row['event_id'] . "'>View Details</a></p>
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
